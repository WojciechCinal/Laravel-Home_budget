<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Category;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generateYearlyReport(Request $request)
    {
        try {
            $startYear = $request->input('start_year');
            $endYear = $request->input('end_year');

            if ($startYear > $endYear) {
                throw new \Exception('Niepoprawny zakres dat!');
            }

            $transactionsQuery = Transaction::where('id_user', Auth::id())
                ->whereYear('date_transaction', '>=', $startYear)
                ->whereYear('date_transaction', '<=', $endYear)
                ->get();

            $yearlyExpenses = [];

            foreach ($transactionsQuery as $transaction) {
                $date = Carbon::parse($transaction->date_transaction);
                $year = $date->format('Y');
                $month = $date->format('m');
                $category = $transaction->category->name_category;

                // Inicjalizacja, jeśli nie istnieje jeszcze wartość dla tego miesiąca
                if (!isset($yearlyExpenses[$year][$month][$category])) {
                    $yearlyExpenses[$year][$month][$category] = 0;
                }

                // Dodanie wydatku dla kategorii w danym miesiącu
                $yearlyExpenses[$year][$month][$category] += $transaction->amount_transaction;

                // Inicjalizacja łącznych wydatków w danym miesiącu, jeśli nie została jeszcze ustawiona
                if (!isset($monthlyTotalExpenses[$year][$month])) {
                    $monthlyTotalExpenses[$year][$month] = 0;
                }

                // Dodanie wydatku do łącznych wydatków w danym miesiącu
                $monthlyTotalExpenses[$year][$month] += $transaction->amount_transaction;
            }
            $categories = Category::where('id_user', Auth::id())->get();

            // Przekazanie danych do widoku
            return view('Report.yearReport', [
                'yearlyExpenses' => $yearlyExpenses,
                'categories' => $categories,
                'startYear' => $startYear,
                'endYear' => $endYear,
                'monthlyTotalExpenses' => $monthlyTotalExpenses,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('transactions.index')->with('error', $e->getMessage());
        }
    }
}
