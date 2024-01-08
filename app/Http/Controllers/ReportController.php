<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->date_transaction);
            $year = $date->format('Y');
            $month = $date->format('m');
            $category = $transaction->category->name_category;

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
            $categoryStartName = $transaction->category->name_start;

            // Sprawdzenie warunku, czy nazwa startowa kategorii to "Plany oszczędnościowe"
            if ($categoryStartName === 'Plany oszczędnościowe') {
                continue; // Pomijamy tworzenie wykresu kołowego dla tej kategorii
            }

            $subcategory = $transaction->subcategory;
            $subcategoryName = $subcategory ? $subcategory->name_subCategory : 'Nie wskazano pokategorii';

            if (!isset($subcategoryYearlyTotal[$year][$categoryName][$subcategoryName])) {
                $subcategoryYearlyTotal[$year][$categoryName][$subcategoryName] = 0;
            }

            $subcategoryYearlyTotal[$year][$categoryName][$subcategoryName] += $transaction->amount_transaction;
        }

        return [
            'yearlyExpenses' => $yearlyExpenses,
            'categories' => $categories,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'monthlyTotalExpenses' => $monthlyTotalExpenses,
            'categoryYearlyTotal' => $categoryYearlyTotal,
            'subcategoryYearlyTotal' => $subcategoryYearlyTotal,
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
            Log::error('ReportControllerr. Błąd w metodzie generateYearlyReport(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas tworzenia raportu');
        }
    }
}
