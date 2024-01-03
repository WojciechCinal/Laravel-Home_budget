<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $user = Auth::user();

            $transactions = Transaction::where('id_user', $user->id_user)
                ->orderByDesc('date_transaction')
                ->paginate(15);

            // Wylicz sumę wydatków z bieżącego miesiąca
            $expensesThisMonth = Transaction::where('id_user', $user->id_user)
                ->whereYear('date_transaction', now()->year)
                ->whereMonth('date_transaction', now()->month)
                ->sum('amount_transaction');

            $monthlyBudget = $user->monthly_budget;

            $remainingFunds = $monthlyBudget - $expensesThisMonth;

            return view('Transaction.index', compact('transactions', 'expensesThisMonth', 'monthlyBudget', 'remainingFunds'));
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
        try {
            $user = Auth::user();
            $data = $request->validate([
                'name_transaction' => 'required|string',
                'amount_transaction' => 'required|numeric',
                'date_transaction' => 'required|date',
                'category_id' => 'required|exists:categories,id_category',
                'subcategory_id' => 'nullable|exists:sub_categories,id_subCategory',
            ]);

            $transactionData = [
                'name_transaction' => $data['name_transaction'],
                'amount_transaction' => $data['amount_transaction'],
                'date_transaction' => $data['date_transaction'],
                'id_user' => $user->id_user,
                'id_category' => $data['category_id'],
            ];

            if (isset($data['subcategory_id'])) {
                $transactionData['id_subCategory'] = $data['subcategory_id'];
            }

            $transaction = Transaction::create($transactionData);

            return redirect()->route('transactions.index')->with('success', 'Transakcja została dodana pomyślnie.');
        } catch (\Exception $e) {
            Log::error('TransactionController. Błąd w metodzie store(): ' . $e->getMessage());
            return redirect()->route('transactions.create')->with('error', 'Wystąpił błąd podczas dodawania transakcji.');
        }
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

            // Sprawdź, czy transakcja należy do bieżącego użytkownika
            if ($transaction->id_user !== $user->id_user) {
                return redirect()->route('transactions.index')->with('error', 'Nie masz dostępu do tej transakcji!');
            }

            $data = $request->validate([
                'name_transaction' => 'required|string',
                'amount_transaction' => 'required|numeric',
                'date_transaction' => 'required|date',
                'category_id' => 'required|exists:categories,id_category',
                'subcategory_id' => 'nullable|exists:sub_categories,id_subCategory',
            ]);

            $transaction->name_transaction = $data['name_transaction'];
            $transaction->amount_transaction = $data['amount_transaction'];
            $transaction->date_transaction = $data['date_transaction'];
            $transaction->id_category = $data['category_id'];
            $transaction->id_subCategory = $data['subcategory_id'];

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
}
