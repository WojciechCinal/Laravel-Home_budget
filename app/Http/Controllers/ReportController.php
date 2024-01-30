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

        // Transakcje na lata
        $transactionsByYear = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y');
        });

        $messages = [];

        // Sprawdzenie każdego roku w zakresie
        foreach (range($startYear, $endYear) as $year) {

            if (!isset($transactionsByYear[$year]) || $transactionsByYear[$year]->isEmpty()) {
                $msg = $year . 'r. - brak transakcji.';
                $messages[] = $msg;
            }
        }

        if (!empty($messages)) {
            session()->put('ReportMessages', $messages);
        }

        $yearTotalsCat = [];
        $yearTotalsSubCat = [];

        foreach ($transactionsByYear as $year => $transactions) {
            foreach ($categories as $category) {
                $categoryTransactions = $transactions->where('id_category', $category->id_category);
                $categorySum = $categoryTransactions->sum('amount_transaction');

                if ($categorySum != 0) {
                    $yearTotalsCat[$year][$category->name_category] = $categorySum;

                    if ($category->name_start == "Plany oszczędnościowe") {
                        continue;
                    } else {
                        $subCategories = $category->subcategories;
                        foreach ($subCategories as $subCategory) {
                            $subCategoryTransactions = $categoryTransactions->where('id_subCategory', $subCategory->id_subCategory);
                            $subCategorySum = $subCategoryTransactions->sum('amount_transaction');
                            if ($subCategorySum != 0) {
                                $yearTotalsSubCat[$year][$category->name_category][$subCategory->name_subCategory] = $subCategorySum;
                            }
                        }

                        // Suma dla transakcji bez przypisanej podkategorii
                        $transactionsWithoutSubCategory = $categoryTransactions->where('id_subCategory', null);
                        $amountWithoutSubCategory = $transactionsWithoutSubCategory->sum('amount_transaction');
                        if ($amountWithoutSubCategory != 0) {
                            $yearTotalsSubCat[$year][$category->name_category]['Nie podano'] = $amountWithoutSubCategory;
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

            // Puste kolekcje dla miesięcy, które nie występują
            for ($month = 1; $month <= 12; $month++) {
                $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);

                if (!isset($transactionsByMonth[$year][$monthKey])) {
                    $transactionsByMonth[$year][$monthKey] = collect();
                }
            }
            $transactionsByMonth[$year] = $transactionsByMonth[$year]->sortBy(function ($value, $key) {
                return $key;
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
                return redirect()->route('transactions.index')->with('error', 'Niepoprawny zakres dat!');
            }

            $data = $this->fetchDataForYearlyReport($startYear, $endYear, $selectedCategories);

            if ($data['transactionsByYear']->isEmpty()) {
                session()->forget('ReportMessages');
                return redirect()->route('transactions.index')->with('message', 'Brak transakcji w wybranym okresie: ' . $startYear . 'r. - ' . $endYear . 'r.');
            }

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
        $name = "$now Budżetomierz - zestawienie roczne $startYear-$endYear";

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

        // Transakcje na miesiące
        $transactionsByMonth = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('m');
        });

        $messages = [];

        // Sprawdzenie każdego miesiąca w zakresie
        foreach (range($startMonth, $endMonth) as $month) {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            if (!isset($transactionsByMonth[$month]) || $transactionsByMonth[$month]->isEmpty()) {
                $msg = Carbon::createFromDate($selectedYear, $month, 1)->isoFormat('MMMM') . ' ' . $selectedYear . ' - brak transakcji.';
                $messages[] = $msg;
            }
        }

        if (!empty($messages)) {
            session()->put('ReportMessages', $messages);
        }

        $monthTotalsCat = [];
        $monthTotalsSubCat = [];

        foreach ($transactionsByMonth as $month => $transactions) {
            foreach ($categories as $category) {
                $categoryTransactions = $transactions->where('id_category', $category->id_category);
                $categorySum = $categoryTransactions->sum('amount_transaction');

                if ($categorySum != 0) {
                    $monthTotalsCat[$month][$category->name_category] = $categorySum;

                    if ($category->name_start == "Plany oszczędnościowe") {
                        continue;
                    } else {
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
                            $monthTotalsSubCat[$month][$category->name_category]['Nie podano'] = $amountWithoutSubCategory;
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

                if ($data['transactionsByMonth']->isEmpty()) {
                    session()->forget('ReportMessages');
                    $startMonthName = Carbon::create($selectedYear, $startMonth)->translatedFormat('F');
                    $endMonthName = Carbon::create($selectedYear, $endMonth)->translatedFormat('F');
                    return redirect()->route('transactions.index')->with('message', 'Brak transakcji w wybranym okresie: ' . $selectedYear . 'r. ' . $startMonthName . ' - ' . $endMonthName . '.');
                }

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
        $name = "$now Budżetomierz - zestawienie miesięczne $selectedYear $monthStartName-$monthEndName";

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

        // Transakcje na tygodnie
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

                $transactionsWithoutSubCategory = $categoryTransactions->where('id_subCategory', null);
                $amountWithoutSubCategory = $transactionsWithoutSubCategory->sum('amount_transaction');
                if ($amountWithoutSubCategory != 0) {
                    $weekTotals[$category->name_category]['Nie podano'] = $amountWithoutSubCategory;
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
            $startWeek = Carbon::parse($request->input('startWeek'));
            $endWeek = Carbon::parse($request->input('endWeek'));
            $selectedCategories = $request->input('categories');

            $startDate = CarbonImmutable::parse($startWeek);
            $endDate = CarbonImmutable::parse($endWeek)->endOfWeek();

            if ($endDate->lt($startDate)) {
                return redirect()->back()->with('error', 'Niepoprawny zakres dat!');
            }

            $data = $this->fetchDataForWeeklyReport($startDate, $endDate, $selectedCategories);

            if ($data['transactionsByWeek']->isEmpty()) {
                session()->forget('ReportMessages');
                $formattedStartWeek = 'Tydz. ' . $startWeek->week . ' ' . $startWeek->year . 'r.';
                $formattedEndWeek = 'Tydz. ' . $endWeek->week . ' ' . $endWeek->year . 'r.';
                return redirect()->route('transactions.index')->with('message', 'Brak transakcji w wybranym okresie: ' .  $formattedStartWeek . ' - ' . $formattedEndWeek . '.');
            }

            return view('Report.weekReport', $data);
        } catch (\Exception $e) {
            Log::error('ReportController. Błąd w metodzie generateWeeklyReport(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas tworzenia raportu');
        }
    }

    public function weeklyReportPDF(Request $request)
    {
        $startWeek = Carbon::parse($request->input('startWeek'));
        $endWeek = Carbon::parse($request->input('endWeek'));
        $selectedCategories = $request->input('categories');

        $startDate = CarbonImmutable::parse($startWeek);
        $endDate = CarbonImmutable::parse($endWeek)->endOfWeek();

        if ($endDate->lt($startDate)) {
            return redirect()->back()->with('error', 'Data końcowa nie może być wcześniejsza niż data początkowa!');
        }

        $data = $this->fetchDataForWeeklyReport($startDate, $endDate, $selectedCategories);

        $formattedStartWeek = 'Tydz. ' . $startWeek->week . ' ' . $startWeek->year . 'r.';
        $formattedEndWeek = 'Tydz. ' . $endWeek->week . ' ' . $endWeek->year . 'r.';
        $now = Carbon::now()->format('Y-m-d');
        $name = "$now Budżetomierz - zestawienie tygodniowe $formattedStartWeek - $formattedEndWeek";

        $pdf = PDF::loadView('Report.weekReportPDF', $data)->setPaper('a4', 'portrait');

        return $pdf->download("$name.pdf");
    }
}
