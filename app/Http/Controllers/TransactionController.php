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

            return view('Transaction.index', compact('transactions'));
        } catch (\Exception $e) {
            Log::error('TransactionController. Błąd w metodzie index(): ' . $e->getMessage());
            return redirect()->route('transactions.index')->with('error', 'Wystąpił błąd podczas pobierania list zakupów.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('id_user', $user->id_user)->get(); // Pobieranie kategorii przypisanych do użytkownika

        $subcategoriesByCategory = [];

        // Pobieranie podkategorii dla każdej kategorii
        foreach ($categories as $category) {
            $subcategories = SubCategory::where('id_category', $category->id_category)->get();
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
            // Walidacja danych
            $data = $request->validate([
                'name_transaction' => 'required|string',
                'amount_transaction' => 'required|numeric',
                'date_transaction' => 'required|date',
                'category_id' => 'required|exists:categories,id_category', // Walidacja kategorii
                'subcategory_id' => 'nullable|exists:sub_categories,id_subCategory', // Walidacja podkategorii (opcjonalna)
                // Dodaj walidację i zapisz inne pola z formularza
            ]);

            // Zapisanie transakcji do bazy danych
            $transactionData = [
                'name_transaction' => $data['name_transaction'],
                'amount_transaction' => $data['amount_transaction'],
                'date_transaction' => $data['date_transaction'],
                'id_user' => $user->id_user, // Przypisanie ID zalogowanego użytkownika
                'id_category' => $data['category_id'],
            ];

            // Dodanie id_subCategory tylko jeśli zostało przesłane w formularzu
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
}
