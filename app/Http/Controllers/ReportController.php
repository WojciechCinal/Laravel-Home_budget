<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\SubCategory;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function fetchDataForYearlyReport($startYear, $endYear, $selectedCategories)
    {
        $transactionsQuery = Transaction::where('id_user', Auth::id())
            ->whereYear('date_transaction', '>=', $startYear)
            ->whereYear('date_transaction', '<=', $endYear);

        if (!empty($selectedCategories)) {
            $transactionsQuery->whereIn('id_category', $selectedCategories);
        }

        $transactions = $transactionsQuery->get();
        $yearlyExpenses = [];
        $monthlyTotalExpenses = [];
        $categoryYearlyTotal = [];
        $categories = Category::where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->get();

        $subcategoryYearlyTotal = [];
        $yearlyTotal = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date_transaction);
            $year = $date->format('Y');
            $month = $date->format('m');
            $category = $transaction->category->name_category;

            if (!isset($yearlyTotal[$year])) {
                $yearlyTotal[$year] = 0;
            }

            $yearlyTotal[$year] += $transaction->amount_transaction;

            if (!isset($yearlyExpenses[$year][$month][$category])) {
                $yearlyExpenses[$year][$month][$category] = 0;
            }

            $yearlyExpenses[$year][$month][$category] += $transaction->amount_transaction;
            if (!isset($monthlyTotalExpenses[$year][$month])) {
                $monthlyTotalExpenses[$year][$month] = 0;
            }

            $monthlyTotalExpenses[$year][$month] += $transaction->amount_transaction;

            if (!isset($categoryYearlyTotal[$year][$category])) {
                $categoryYearlyTotal[$year][$category] = 0;
            }

            $categoryYearlyTotal[$year][$category] += $transaction->amount_transaction;

            $categoryName = $transaction->category->name_category;

            $subcategory = $transaction->subcategory;
            $subcategoryName = $subcategory ? $subcategory->name_subCategory : 'Nie podano podkategorii';

            if (!isset($subcategoryYearlyTotal[$year][$categoryName][$subcategoryName])) {
                $subcategoryYearlyTotal[$year][$categoryName][$subcategoryName] = 0;
            }

            // Dodajemy wartość do podkategorii tylko, jeśli nie jest pusta ani równa zero
            if ($transaction->amount_transaction > 0) {
                $subcategoryYearlyTotal[$year][$categoryName][$subcategoryName] += $transaction->amount_transaction;
            }
        }

        // Tworzymy tablicę z wszystkimi latami w zakresie
        $allYears = range($startYear, $endYear);
        $messages = [];

        // Sprawdzamy każdy rok z zakresu
        foreach ($allYears as $year) {
            $transactionsForYear = $transactions->filter(function ($transaction) use ($year) {
                return Carbon::parse($transaction->date_transaction)->format('Y') == $year;
            });

            if ($transactionsForYear->isEmpty()) {
                $msg = "$year - brak transakcji spełniających kryteria.";
                $messages[] = $msg;
            }
        }

        // Zapisujemy wszystkie komunikaty w sesji
        if (!empty($messages)) {
            session()->put('yearReportMessages', $messages);
        }

        return [
            'yearlyExpenses' => $yearlyExpenses,
            'categories' => $categories,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'monthlyTotalExpenses' => $monthlyTotalExpenses,
            'categoryYearlyTotal' => $categoryYearlyTotal,
            'subcategoryYearlyTotal' => $subcategoryYearlyTotal,
            'yearlyTotal' => $yearlyTotal,
        ];
    }

    public function generateYearlyReport(Request $request)
    {
        try {
            $startYear = $request->input('start_year');
            $endYear = $request->input('end_year');
            $selectedCategories = $request->input('categories', []);

            if ($startYear > $endYear) {
                throw new \Exception('Niepoprawny zakres dat!');
            }

            $data = $this->fetchDataForYearlyReport($startYear, $endYear, $selectedCategories);

            return view('Report.yearReport', $data);
        } catch (\Exception $e) {
            Log::error('ReportController. Błąd w metodzie generateYearlyReport(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas tworzenia raportu');
        }
    }

    public function yearlyReportPDF(Request $request)
    {
        $startYear = $request->input('start_year');
        $endYear = $request->input('end_year');
        $selectedCategories = $request->input('categories', []);

        $data = $this->fetchDataForYearlyReport($startYear, $endYear, $selectedCategories);
        $now = Carbon::now()->format('Y-m-d');
        $name = "$now Budżet domowy - zestawienie roczne $startYear-$endYear";

        $pdf = PDF::loadView('Report.yearReportPDF', $data)->setPaper('a4', 'portrait');
        return $pdf->download("$name.pdf");
    }

    private function fetchDataForMonthlyReport($selectedYear, $startMonth, $endMonth, $startDate, $endDate, $selectedCategories)
    {
        $transactions = Transaction::with('category', 'subcategory')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->orderBy('date_transaction', 'asc')
            ->get();

        $categories = Category::where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->get();

        // Podziel transakcje na miesiące
        $transactionsByMonth = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('m');
        });


        // Suma kwot na daną kategorię w poszczególnych miesiącach
        $monthTotalsCat = [];
        $monthTotalsSubCat = [];

        foreach ($transactionsByMonth as $month => $transactions) {
            foreach ($categories as $category) {
                $categoryTransactions = $transactions->where('id_category', $category->id_category);
                $categorySum = $categoryTransactions->sum('amount_transaction');

                // Dodaj do $monthTotals tylko, jeśli suma nie jest równa 0
                if ($categorySum != 0) {
                    // Przypisz sumę do nazwy kategorii zamiast do id
                    $monthTotalsCat[$month][$category->name_category] = $categorySum;

                    // Pomijaj kategorie "Plany oszczędnościowe" na wykresie kołowym
                    if ($category->name_start == "Plany oszczędnościowe") {
                        continue;
                    } else {
                        // Dodaj sumę dla podkategorii
                        $subCategories = $category->subcategories;
                        foreach ($subCategories as $subCategory) {
                            $subCategoryTransactions = $categoryTransactions->where('id_subCategory', $subCategory->id_subCategory);
                            $subCategorySum = $subCategoryTransactions->sum('amount_transaction');
                            if ($subCategorySum != 0) {
                                $monthTotalsSubCat[$month][$category->name_category][$subCategory->name_subCategory] = $subCategorySum;
                            }
                        }

                        // Dodaj sumę dla transakcji bez przypisanej podkategorii
                        $transactionsWithoutSubCategory = $categoryTransactions->where('id_subCategory', null);
                        $amountWithoutSubCategory = $transactionsWithoutSubCategory->sum('amount_transaction');
                        if ($amountWithoutSubCategory != 0) {
                            $monthTotalsSubCat[$month][$category->name_category]['Nie podano kategorii'] = $amountWithoutSubCategory;
                        }
                    }
                }
            }
        }

        // Podział transakcji na tygodnie w poszczególnych miesiącach
        $transactionsByWeek = [];
        foreach ($transactionsByMonth as $month => $transactions) {
            $transactionsByWeek[$month] = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction->date_transaction)->format('W');
            });
        }

        // Obliczenie sumy kwot dla kategorii w danym tygodniu
        $weekTotals = [];
        foreach ($transactionsByWeek as $month => $weeks) {
            foreach ($weeks as $week => $transactionsInWeek) {
                $weekTotals[$month][$week] = $transactionsInWeek->sum('amount_transaction');
            }
        }

        // Dodanie informacji o zakresie dat dla każdego tygodnia do istniejącej struktury danych
        foreach ($transactionsByWeek as $month => $weeks) {
            foreach ($weeks as $week => $transactionsInWeek) {
                // Pobierz pierwszą transakcję w tygodniu, aby uzyskać datę dla początku tygodnia
                $firstTransaction = $transactionsInWeek->first();
                $startOfWeek = Carbon::parse($firstTransaction->date_transaction)->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->endOfWeek();

                // Sprawdź, czy początek tygodnia wchodzi w inny rok
                if ($startOfWeek->format('Y') != $selectedYear) {
                    // Jeśli tak, ustaw zakres dat na początek stycznia obecnego roku
                    $transactionsByWeek[$month][$week]['week_dates'] = Carbon::createFromDate($selectedYear, $month, 1)->isoFormat('D') . ' - ' . Carbon::createFromDate($selectedYear, $month, 1)->endOfWeek()->isoFormat('D MMM');
                } elseif ($startOfWeek->format('m') < $month) {
                    $transactionsByWeek[$month][$week]['week_dates'] = Carbon::createFromDate($selectedYear, $month, 1)->startOfMonth()->isoFormat('D') . ' - ' . $endOfWeek->isoFormat('D MMM');
                } elseif ($endOfWeek->format('m') > $month) {
                    $transactionsByWeek[$month][$week]['week_dates'] = $startOfWeek->isoFormat('D') . ' - ' . Carbon::createFromDate($selectedYear, $month, 1)->endOfMonth()->isoFormat('D MMM');
                } else {
                    $transactionsByWeek[$month][$week]['week_dates'] = $startOfWeek->isoFormat('D') . ' - ' . $endOfWeek->isoFormat('D MMM');
                }
            }
        }

        return [
            'transactionsByMonth' => $transactionsByMonth,
            'transactionsByWeek' => $transactionsByWeek,
            'weekTotals' => $weekTotals,
            'categories' => $categories,
            'monthTotalsCat' => $monthTotalsCat,
            'monthTotalsSubCat' => $monthTotalsSubCat,
        ];
    }

    public function generateMonthlyReport(Request $request)
    {
        try {
            $selectedYear = $request->input('selected_year');
            $startMonth = $request->input('start_date');
            $endMonth = $request->input('end_date');
            $selectedCategories = $request->input('categories', []);

            $startDate = Carbon::create($selectedYear, $startMonth, 1);
            $endDate = Carbon::create($selectedYear, $endMonth, 1)->endOfMonth();

            if ($endDate->gte($startDate)) {
                $data = $this->fetchDataForMonthlyReport($selectedYear, $startMonth, $endMonth, $startDate, $endDate, $selectedCategories);

                return view('Report.monthReport', $data);
            } else {
                return redirect()->route('transactions.index')->with('error', 'Nieprawidłowy przedział dat.');
            }
        } catch (\Exception $e) {
            Log::error('ReportController. Błąd w metodzie generateMonthlyReport(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas tworzenia raportu');
        }
    }
    public function monthlyReportPDF(Request $request)
    {
        $selectedYear = $request->input('selected_year');
        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');
        $selectedCategories = $request->input('categories', []);

        $startDate = Carbon::create($selectedYear, $startMonth, 1);
        $endDate = Carbon::create($selectedYear, $endMonth, 1)->endOfMonth();
        $data = $this->fetchDataForMonthlyReport($selectedYear, $startMonth, $endMonth, $startDate, $endDate, $selectedCategories);

        $monthStartName = \Carbon\Carbon::createFromFormat('m', $startMonth)->locale('pl')->isoFormat('MMMM');
        $monthEndName = \Carbon\Carbon::createFromFormat('m', $endMonth)->locale('pl')->isoFormat('MMMM');
        $now = Carbon::now()->format('Y-m-d');
        $name = "$now Budżet domowy - zestawienie miesięczne $selectedYear $monthStartName-$monthEndName";

        $pdf = PDF::loadView('Report.monthReportPDF', $data)->setPaper('a4', 'portrait');
        return $pdf->download("$name.pdf");
    }
}
