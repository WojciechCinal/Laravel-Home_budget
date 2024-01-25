<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RankingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $categories = Category::whereNotNull('name_start')->get();

        // Sprawdzenie rodzaju rankingu
        $rankingType = $request->input('ranking_type');

        if ($rankingType === 'monthly') {
            $selectedMonth = $request->input('date');
            $dateStart = Carbon::parse($selectedMonth)->startOfMonth();
            $dateEnd = Carbon::parse($selectedMonth)->endOfMonth();
            $month = Carbon::parse($selectedMonth)->translatedFormat('F - Y');
            $rankingName = "Ranking miesięczny: $month r.";
        } elseif ($rankingType === 'yearly') {
            $selectedYear = $request->input('date');
            $dateStart = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
            $dateEnd = Carbon::createFromDate($selectedYear, 1, 1)->endOfYear();
            $year = Carbon::createFromDate($selectedYear, 1, 1)->format('Y');
            $rankingName = "Ranking roczny: $year r.";
        } else {
            $dateStart = Carbon::now()->subYear()->startOfYear();
            $dateEnd = Carbon::now()->subYear()->endOfYear();
            $year = Carbon::now()->subYear()->startOfYear()->format('Y');
            $rankingName = "Ranking roczny: $year r.";
        }

        // tablica z przypisaniem id do name_start np: "Mieszkanie" => [1,9,17,30,...]
        $groupedCategories = $categories->groupBy('name_start')->map(function ($items) {
            return $items->pluck('id_category')->toArray();
        });

        $transactions = Transaction::whereIn('id_category', $categories->pluck('id_category'))
            ->whereBetween('date_transaction', [$dateStart, $dateEnd])
            ->get();

        // suma transakcji dla każdej name_start
        $summedTransactions = $transactions->groupBy('category.name_start')->map(function ($items) {
            return $items->sum('amount_transaction');
        });

        // liczba użytkowników, którzy mają chociaż jedną transakcję z daną kategorią po name_start
        $usersCount = $transactions->groupBy('category.name_start')->map(function ($items) {
            return $items->pluck('id_user')->unique()->count();
        });

        $averageAmounts = $categories->mapWithKeys(function ($category) use ($summedTransactions, $usersCount) {
            $nameStart = $category->name_start;
            $sum = $summedTransactions->get($nameStart, 0);
            $users = $usersCount->get($nameStart, 0);
            $average = $users > 0 ? round($sum / $users, 2) : 0;

            return [$nameStart => $average];
        });

        $myTransactions = Transaction::whereIn('id_category', $categories->pluck('id_category'))
            ->whereBetween('date_transaction', [$dateStart, $dateEnd])
            ->where('id_user', Auth::id())
            ->get();

        // suma transakcji dla każdej name_start
        $mySummedTransactions = $myTransactions->groupBy('category.name_start')->map(function ($items) {
            return $items->sum('amount_transaction');
        });

        // suma transakcji dla kategorii posiadających name_start
        $myExpenses = $categories->mapWithKeys(function ($category) use ($mySummedTransactions) {
            $nameStart = $category->name_start;
            $sum = $mySummedTransactions->get($nameStart, 0);

            return [$nameStart => $sum];
        });

        $data = [
            'averageAmounts' => $averageAmounts,
            'summedTransactions' => $summedTransactions,
            'myExpenses' => $myExpenses,
            'rankingName' => $rankingName,
        ];
        // dd($data);
        return view('Ranking.ranking', $data);
    }
}
