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
use Carbon\CarbonImmutable;
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
        $startDate = Carbon::create($startYear)->startOfYear();
        $endDate = Carbon::create($endYear)->endOfYear();

        $transactions = Transaction::with('category', 'subcategory')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->orderBy('date_transaction', 'asc')
            ->get();

        $categories = Category::where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->get();

        // Podziel transakcje na lata
        $transactionsByYear = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y');
        });

        $messages = [];

        // Sprawdź każdy rok w zakresie
        foreach (range($startYear, $endYear) as $year) {

            if (!isset($transactionsByYear[$year]) || $transactionsByYear[$year]->isEmpty()) {
                $msg = $year . 'r. - brak transakcji.';
                $messages[] = $msg;
            }
        }

        // Zapisujemy wszystkie komunikaty w sesji
        if (!empty($messages)) {
            session()->put('ReportMessages', $messages);
        }


        // Suma kwot na daną kategorię w poszczególnych miesiącach
        $yearTotalsCat = [];
        $yearTotalsSubCat = [];

        foreach ($transactionsByYear as $year => $transactions) {
            foreach ($categories as $category) {
                $categoryTransactions = $transactions->where('id_category', $category->id_category);
                $categorySum = $categoryTransactions->sum('amount_transaction');

                // Dodaj do $monthTotals tylko, jeśli suma nie jest równa 0
                if ($categorySum != 0) {
                    // Przypisz sumę do nazwy kategorii zamiast do id
                    $yearTotalsCat[$year][$category->name_category] = $categorySum;

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
                                $yearTotalsSubCat[$year][$category->name_category][$subCategory->name_subCategory] = $subCategorySum;
                            }
                        }

                        // Dodaj sumę dla transakcji bez przypisanej podkategorii
                        $transactionsWithoutSubCategory = $categoryTransactions->where('id_subCategory', null);
                        $amountWithoutSubCategory = $transactionsWithoutSubCategory->sum('amount_transaction');
                        if ($amountWithoutSubCategory != 0) {
                            $yearTotalsSubCat[$year][$category->name_category]['Nie podano kategorii'] = $amountWithoutSubCategory;
                        }
                    }
                }
            }
        }

        // Podział transakcji na miesiące w poszczególnych latach
        $transactionsByMonth = [];

        foreach ($transactionsByYear as $year => $transactions) {
            $transactionsByMonth[$year] = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction->date_transaction)->format('m');
            });
        }

        // Obliczenie sumy kwot dla kategorii w danym miesiącu
        $monthTotals = [];
        foreach ($transactionsByMonth as $year => $months) {
            foreach ($months as $month => $transactionsInMonth) {
                $monthTotals[$year][$month] = $transactionsInMonth->sum('amount_transaction');
            }
        }

        return [
            'transactionsByYear' => $transactionsByYear,
            'transactionsByMonth' => $transactionsByMonth,
            'monthTotals' => $monthTotals,
            'categories' => $categories,
            'yearTotalsCat' => $yearTotalsCat,
            'yearTotalsSubCat' => $yearTotalsSubCat,
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
            //dd($data);
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

        $messages = [];

        // Sprawdź każdy miesiąc w zakresie
        foreach (range($startMonth, $endMonth) as $month) {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT); // formatowanie na dwie cyfry

            if (!isset($transactionsByMonth[$month]) || $transactionsByMonth[$month]->isEmpty()) {
                $msg = Carbon::createFromDate($selectedYear, $month, 1)->isoFormat('MMMM') . ' ' . $selectedYear . ' - brak transakcji.';
                $messages[] = $msg;
            }
        }

        // Zapisujemy wszystkie komunikaty w sesji
        if (!empty($messages)) {
            session()->put('ReportMessages', $messages);
        }


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

        // Dodanie informacji o zakresie dat dla każdego tygodnia
        foreach ($transactionsByWeek as $month => $weeks) {
            foreach ($weeks as $week => $transactionsInWeek) {
                $firstTransaction = $transactionsInWeek->first();
                $startOfWeek = Carbon::parse($firstTransaction->date_transaction)->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->endOfWeek();

                // Sformatowanie dat (miesiąc jest nadrzędny w porównaniu do tygodnia)
                if ($startOfWeek->format('Y') != $selectedYear) {
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
                // dd($data);
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

    private function fetchDataForWeeklyReport($startDate, $endDate, $selectedCategories)
    {
        $transactions = Transaction::with('category', 'subcategory.category')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->orderBy('date_transaction', 'asc')
            ->get();

        $categories = Category::where('id_user', Auth::id())
            ->whereIn('id_category', $selectedCategories)
            ->get();

        // Podział transakcji na tygodnie
        $transactionsByWeek = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->startOfWeek()->format('Y-\WW');
        });

        // Podział transakcji w tygodniu na dni
        $transactionsByDay = $transactionsByWeek->map(function ($weekTransactions) {
            return $weekTransactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction->date_transaction)->format('Y-m-d');
            });
        });

        // Zsumowanie dziennej kwoty wydatków
        $dayTotals = $transactionsByDay->map(function ($weekTransactions) {
            return $weekTransactions->map(function ($dayTransactions) {
                return $dayTransactions->sum('amount_transaction');
            });
        });

        // Zsumowanie tygodniowej kwoty wydatków ze względu na kategorie
        $weekTotalsCat = $transactionsByWeek->map(function ($weekTransactions) use ($categories) {
            $weekTotals = [];
            foreach ($categories as $category) {
                $categoryTransactions = $weekTransactions->where('id_category', $category->id_category);
                $categorySum = $categoryTransactions->sum('amount_transaction');

                // Dodaj do $weekTotalsCat tylko, jeśli suma nie jest równa 0
                if ($categorySum != 0) {
                    $weekTotals[$category->name_category] = $categorySum;
                }
            }
            return $weekTotals;
        });

        // Zsumowanie tygodniowej kwoty wydatków ze względu na podkategorie
        $weekTotalsSubCat = $transactionsByWeek->map(function ($weekTransactions) use ($categories) {
            $weekTotals = [];
            foreach ($categories as $category) {
                $categoryTransactions = $weekTransactions->where('category.id_category', $category->id_category);
                $subCategories = $category->subcategories;
                foreach ($subCategories as $subCategory) {
                    $subCategoryTransactions = $categoryTransactions->where('id_subCategory', $subCategory->id_subCategory);
                    $subCategorySum = $subCategoryTransactions->sum('amount_transaction');
                    if ($subCategorySum != 0) {
                        $weekTotals[$category->name_category][$subCategory->name_subCategory] = $subCategorySum;
                    }
                }

                // Dodaj sumę dla transakcji bez przypisanej podkategorii
                $transactionsWithoutSubCategory = $categoryTransactions->where('id_subCategory', null);
                $amountWithoutSubCategory = $transactionsWithoutSubCategory->sum('amount_transaction');
                if ($amountWithoutSubCategory != 0) {
                    $weekTotals[$category->name_category]['Nie podano kategorii'] = $amountWithoutSubCategory;
                }
            }
            return $weekTotals;
        });


        return [
            'categories' => $categories,
            'transactionsByWeek' => $transactionsByWeek,
            'transactionsByDay' => $transactionsByDay,
            'dayTotals' => $dayTotals,
            'weekTotalsCat' => $weekTotalsCat,
            'weekTotalsSubCat' => $weekTotalsSubCat,
        ];
    }

    public function generateWeeklyReport(Request $request)
    {
        try {
            $startWeek = $request->input('startWeek');
            $endWeek = $request->input('endWeek');
            $selectedCategories = $request->input('categories');

            // Sprawdzenie poprawności zakresu dat
            $startDate = CarbonImmutable::parse($startWeek);
            $endDate = CarbonImmutable::parse($endWeek)->endOfWeek();

            if ($endDate->lt($startDate)) {
                return redirect()->back()->with('error', 'Data końcowa nie może być wcześniejsza niż data początkowa!');
            }

            $data = $this->fetchDataForWeeklyReport($startDate, $endDate, $selectedCategories);
            //dd($data);
            return view('Report.weekReport', $data);
        } catch (\Exception $e) {
            Log::error('ReportController. Błąd w metodzie generateWeeklyReport(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas tworzenia raportu');
        }
    }

    public function weeklyReportPDF(Request $request)
    {
        $startWeek = $request->input('startWeek');
        $endWeek = $request->input('endWeek');
        $selectedCategories = $request->input('categories');

        // Sprawdzenie poprawności zakresu dat
        $startDate = CarbonImmutable::parse($startWeek);
        $endDate = CarbonImmutable::parse($endWeek)->endOfWeek();

        if ($endDate->lt($startDate)) {
            return redirect()->back()->with('error', 'Data końcowa nie może być wcześniejsza niż data początkowa!');
        }

        $data = $this->fetchDataForWeeklyReport($startDate, $endDate, $selectedCategories);

        // $monthStartName = \Carbon\Carbon::createFromFormat('m', $startMonth)->locale('pl')->isoFormat('MMMM');
        // $monthEndName = \Carbon\Carbon::createFromFormat('m', $endMonth)->locale('pl')->isoFormat('MMMM');
        $now = Carbon::now()->format('Y-m-d');
        $name = "$now Budżet domowy - zestawienie tygodniowe $startWeek - $endWeek";

        $pdf = PDF::loadView('Report.weekReportPDF', $data)->setPaper('a4', 'portrait');

        return $pdf->download("$name.pdf");
    }
}
