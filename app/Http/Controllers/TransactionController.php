<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $categories = Category::where('id_user', $user->id_user)->get();
            $transactions = Transaction::where('id_user', $user->id_user);

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $selectedCategories = $categories->pluck('id_category')->toArray();

            if ($startDate && $endDate && ($startDate > $endDate)) {
                return redirect()->route('transactions.index')->with('error', 'Niepoprawny zakres dat!');
            }

            if ($startDate == null && $endDate == null) {
                $startDate = now()->startOfMonth()->toDateString();
                $endDate = now()->toDateString();
            } elseif ($endDate == null) {
                $endDate = now()->toDateString();
            }

            switch (true) {
                case ($endDate && $startDate == null):
                    $transactions->where('date_transaction', '<=', $endDate);
                    break;
                case ($startDate && $endDate):
                    $transactions->whereBetween('date_transaction', [$startDate, $endDate]);
                    break;
                default:
                    $transactions->whereYear('date_transaction', now()->year)
                        ->whereMonth('date_transaction', now()->month);
                    break;
            }

            if ($request->has('categories')) {
                $selectedCategories = $request->input('categories');
                $transactions->whereIn('id_category', $selectedCategories);
            }

            if ($request->has('sort_ratio') && in_array($request->input('sort_ratio'), ['asc', 'desc'])) {
                $sortRatio = $request->input('sort_ratio');
                $transactions->orderBy('amount_transaction', $sortRatio);
            }

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $transactions->where('name_transaction', 'LIKE', '%' . $searchTerm . '%');
            }

            $transactions = $transactions->orderByDesc('date_transaction')->paginate(12);

            $expensesThisMonth = Transaction::where('id_user', $user->id_user)
                ->whereYear('date_transaction', now()->year)
                ->whereMonth('date_transaction', now()->month)
                ->sum('amount_transaction');

            $expensesThisMonth = $expensesThisMonth ?? 0;

            $dateNow = now()->monthName . " " . now()->year;
            $monthlyBudget = $user->monthly_budget;
            $remainingFunds = $monthlyBudget - $expensesThisMonth;

            return view('Transaction.index', compact('transactions', 'expensesThisMonth', 'monthlyBudget', 'remainingFunds', 'dateNow', 'categories', 'selectedCategories'));
        } catch (\Exception $e) {
            Log::error('TransactionController. Błąd w metodzie index(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas pobierania list zakupów.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('id_user', $user->id_user)->where('is_active', true)->get();

        $subcategoriesByCategory = [];

        foreach ($categories as $category) {
            $subcategories = SubCategory::where('id_category', $category->id_category)->where('is_active', true)->get();
            $subcategoriesByCategory[$category->id_category] = $subcategories;
        }

        return view('Transaction.new', [
            'categories' => $categories,
            'subcategoriesByCategory' => $subcategoriesByCategory,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name_transaction' => 'required|string|min:3|max:100',
            'amount_transaction' => 'required|numeric|min:0.01',
            'date_transaction' => 'required|date',
            'category_id' => 'required|exists:categories,id_category',
            'subcategory_id' => 'nullable|exists:sub_categories,id_subCategory',
        ], [
            'name_transaction.required' => 'Nazwa transakcji jest wymagana.',
            'name_transaction.string' => 'Nazwa transakcji powinna być tekstem.',
            'name_transaction.min' => 'Nazwa transakcji powinna mieć co najmniej :min znaków.',
            'name_transaction.max' => 'Nazwa transakcji nie może przekraczać :max znaków.',
            'amount_transaction.required' => 'Kwota transakcji jest wymagana.',
            'amount_transaction.numeric' => 'Kwota transakcji musi mieć format np: 35.99.',
            'amount_transaction.min' => 'Kwota transakcji musi być większa niż 0.',
            'date_transaction.required' => 'Data transakcji jest wymagana.',
            'date_transaction.date' => 'Podana data transakcji nie jest poprawna.',
            'category_id.required' => 'Kategoria jest wymagana.',
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
            'subcategory_id.exists' => 'Wybrana podkategoria nie istnieje.',
        ]);

        $transactionData = [
            'name_transaction' => $validatedData['name_transaction'],
            'amount_transaction' => $validatedData['amount_transaction'],
            'date_transaction' => $validatedData['date_transaction'],
            'id_user' => $user->id_user,
            'id_category' => $validatedData['category_id'],
        ];

        if (isset($validatedData['subcategory_id'])) {
            $transactionData['id_subCategory'] = $validatedData['subcategory_id'];
        }

        // Oblicz sumę wydatków w miesiącu transakcji
        $expensesThisMonth = Transaction::where('id_user', $user->id_user)
            ->whereYear('date_transaction', date('Y', strtotime($validatedData['date_transaction'])))
            ->whereMonth('date_transaction', date('m', strtotime($validatedData['date_transaction'])))
            ->sum('amount_transaction');

        $remainingFunds = $user->monthly_budget - $expensesThisMonth;
        $expensesThisMonth += $validatedData['amount_transaction']; // Dodaj nową transakcję do sumy

        // Sprawdź, czy nie przekracza to miesięcznego budżetu
        if ($expensesThisMonth > $user->monthly_budget) {
            $message = 'Nowa transakcja przekroczyła miesięczny budżet!';
            return redirect()->route('transactions.create')->with('error', $message);
        }

        $transaction = Transaction::create($transactionData);

        return redirect()->route('transactions.index')->with('success', 'Transakcja została dodana pomyślnie.');
    }

    public function edit($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::where('id_transaction', $id)
                ->where('id_user', $user->id_user)
                ->first();

            if (!$transaction) {
                return redirect()->route('transactions.index')->with('error', 'Nie masz dostępu do tej transakcji!');
            }

            $categories = Category::where('id_user', $user->id_user)->where('is_active', true)->get();

            $subcategoriesByCategory = [];

            foreach ($categories as $category) {
                $subcategories = SubCategory::where('id_category', $category->id_category)->where('is_active', true)->get();
                $subcategoriesByCategory[$category->id_category] = $subcategories;
            }

            return view('Transaction.edit', [
                'transaction' => $transaction,
                'categories' => $categories,
                'subcategoriesByCategory' => $subcategoriesByCategory,
            ]);
        } catch (\Exception $e) {
            Log::error('TransactionController. Błąd w metodzie edit():' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Nie udało się zedytować transakcji. Spróbuj ponownie później.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::findOrFail($id);

            $remainingFunds = $user->monthly_budget - $transaction->amount_transaction;

            $validator = Validator::make($request->all(), [
                'name_transaction' => 'required|string|min:3|max:100',
                'amount_transaction' => 'required|numeric|min:0.01',
                'date_transaction' => 'required|date',
                'category_id' => 'required|exists:categories,id_category',
                'subcategory_id' => 'nullable|exists:sub_categories,id_subCategory',
            ], [
                'name_transaction.required' => 'Pole Nazwa transakcji jest wymagane.',
                'name_transaction.min' => 'Nazwa transakcji musi mieć co najmniej :min znaki.',
                'name_transaction.max' => 'Nazwa transakcji nie może mieć więcej niż :max znaków.',
                'amount_transaction.required' => 'Pole Kwota jest wymagane.',
                'amount_transaction.numeric' => 'Kwota musi być liczbą.',
                'amount_transaction.min' => 'Kwota musi być większa niż 0.',
                'date_transaction.required' => 'Pole Data jest wymagane.',
                'date_transaction.date' => 'Data musi być w formacie daty.',
                'category_id.required' => 'Pole Kategoria jest wymagane.',
                'category_id.exists' => 'Wybrana kategoria jest nieprawidłowa.',
                'subcategory_id.exists' => 'Wybrana podkategoria jest nieprawidłowa.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('transactions.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $validator->validated();

            $transaction->name_transaction = $data['name_transaction'];
            $transaction->amount_transaction = $data['amount_transaction'];
            $transaction->date_transaction = $data['date_transaction'];
            $transaction->id_category = $data['category_id'];
            $transaction->id_subCategory = $data['subcategory_id'] ?? null;

            $transactionDate = $data['date_transaction'];

            // Oblicz sumę wydatków w miesiącu danej transakcji, wykluczając edytowaną transakcję
            $expensesThisMonth = Transaction::where('id_user', $user->id_user)
                ->whereYear('date_transaction', date('Y', strtotime($transactionDate)))
                ->whereMonth('date_transaction', date('m', strtotime($transactionDate)))
                ->where('id_transaction', '!=', $id) // Wyklucz edytowaną transakcję
                ->sum('amount_transaction');

            $remainingFunds -= $expensesThisMonth;

            $expensesThisMonth += $data['amount_transaction']; // Dodaj zmodyfikowaną transakcję do sumy

            // Sprawdź, czy nie przekracza to miesięcznego budżetu
            if ($expensesThisMonth > $user->monthly_budget) {
                $message = 'Kwota transakcji po edycji przekroczyła miesięczny budżet!';
                return redirect()->route('transactions.edit', $id)
                    ->with('error', $message)
                    ->withInput();
            }

            $transaction->save();

            return redirect()->route('transactions.index')->with('success', 'Transakcja została zaktualizowana.');
        } catch (\Exception $e) {
            Log::error('TransactionController. Błąd w metodzie update():' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas aktualizacji transakcji.');
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = Transaction::find($id);

            if ($transaction) {
                $transaction->delete();

                $msg = "Transakcja została pomyślnie usunięta.";
                return response()->json(['success' => $msg], 200);
            }

            return response()->json(['error' => 'Nie znaleziono takiej transakcji!'], 404);
        } catch (\Exception $e) {
            Log::error('TransactionsController. Błąd w metodzie destroy(): ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas usuwania transakcji!'], 500);
        }
    }

    public function generatePrediction(Request $request)
    {
        $user = Auth::user();
        $startYear = Carbon::create($request->input('start_date'))->format('Y');
        $month = Carbon::create($request->input('start_date'))->format('m');

        $activeCategories = Category::where('id_user', $user->id_user)
            ->where('is_active', true)
            ->where('name_category', '!=', "Brak kategorii")
            ->get();

        $averageExpenses = [];

        for ($i = 1; $i < 4; $i++) {
            $startYear -= 1;
            $transactions = Transaction::where('id_user', $user->id_user)
                ->whereMonth('date_transaction', $month)
                ->whereYear('date_transaction', $startYear)
                ->get();

            foreach ($activeCategories as $category) {
                $categoryTransactions = $transactions->where('id_category', $category->id_category);

                // Jeśli są transakcje dla danej kategorii w danym roku
                if ($categoryTransactions->count() > 0) {
                    $totalAmount = $categoryTransactions->sum('amount_transaction');

                    if (!isset($categoryTotals[$category->name_category])) {
                        $categoryTotals[$category->name_category] = 0;
                    }

                    $categoryTotals[$category->name_category] += $totalAmount;
                    $categoryYears[$category->name_category][] = $startYear;
                }
            }
        }

        $averageExpenses = [];

        // Średnia dla każdej kategorii
        foreach ($activeCategories as $category) {
            $categoryName = $category->name_category;

            if (isset($categoryTotals[$categoryName])) {
                $sum = $categoryTotals[$categoryName];
                $years = count($categoryYears[$categoryName]);
                $average = $years > 0 ? round($sum / $years, 2) : 0;

                $averageExpenses[$categoryName] = $average;
            } else {
                $averageExpenses[$categoryName] = "Brak danych";
            }
        }
        $title = Carbon::parse($request->input('start_date'))->translatedFormat('F Y');
        $data = [
            'averageExpenses' => $averageExpenses,
            'title' => $title,
        ];

        return view('Transaction.predictionExpenses', $data);
    }
}
