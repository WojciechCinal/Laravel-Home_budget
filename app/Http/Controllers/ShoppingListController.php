<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ShoppingListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $user = Auth::user();

            $shoppingLists = ShoppingList::where('id_user', $user->id_user)
            ->orderByDesc('updated_at')
            ->paginate(6);

            return view('shopping_lists.index', compact('shoppingLists'));
        } catch (\Exception $e) {
            Log::error('Error while fetching shopping lists: ' . $e->getMessage());
            return redirect()->route('shopping-lists.index')->with('error', 'Wystąpił błąd podczas pobierania list zakupów.');
        }
    }

    public function destroy($id)
    {
        try {
            $shoppingList = ShoppingList::find($id);

            if ($shoppingList) {
                $shoppingList->delete();

                $msg = "Lista zakupów: $shoppingList->title_shopping_list została pomyślnie usunięta.";
                Session::flash('success', $msg);

                return redirect()->route('shopping-lists.index')->with('success', "Lista zakupów: $shoppingList->title_shopping_list została pomyślnie usunięta.");
            }
            return redirect()->route('shopping-lists.index')->with('error', 'Nie znaleziono takiej listy zakupów!');
        } catch (\Exception $e) {
            Log::error('ShoppingListController. Błąd w metodzie destroy(): ' . $e->getMessage());
            return redirect()->route('shopping-lists.index')->with('error', 'Wystąpił błąd podczas usuwania listy zakupów!');
        }
    }

    public function edit($id)
    {
        try {
            $user = Auth::user();
            $shoppingList = ShoppingList::where('id_shopping_list', $id)
                ->where('id_user', $user->id_user)
                ->first();
            if (!$shoppingList) {
                return redirect()->route('shopping-lists.index')->with('error', 'Nie masz dostępu do tej list zakupów!');
            }

            return view('shopping_lists.edit', compact('shoppingList'));
        } catch (\Exception $e) {
            Log::error('ShoppingListController. Błąd w metodzie edit():' . $e->getMessage());
            return redirect()->route('shopping-lists.index')->with('error', 'Nie udało się zedytować listy. Spróbuj ponownie później.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $shoppingList = ShoppingList::findOrFail($id);

            $shoppingList->update([
                'title_shopping_list' => $request->input('title'),
                'description_shopping_list' => $request->input('description')
            ]);

            return redirect()->route('shopping-lists.index')->with('success', 'Lista zakupów została pomyślnie zaktualizowana.');
        } catch (\Exception $e) {
            Log::error('ShoppingListController. Błąd w metodzie update():' . $e->getMessage());
            return redirect()->route('shopping-lists.index')->with('error', 'Wystąpił błąd podczas aktualizacji listy zakupów.');
        }
    }

    public function create()
    {
        return view('shopping_lists.new');
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validatedData = $request->validate([
                'title_shopping_list' => 'required|max:255',
                'description_shopping_list' => 'max:5000',
            ]);

            $shoppingList = ShoppingList::create([
                'title_shopping_list' => $validatedData['title_shopping_list'],
                'description_shopping_list' => $validatedData['description_shopping_list'],
                'id_user' => $user->id_user
            ]);

            if ($shoppingList) {
                return redirect()->route('shopping-lists.index')->with('success', 'Pomyślnie dodano nową listę zakupów.');
            }
        } catch (\Exception $e) {
            Log::error('ShoppingListController. Błąd w metodzie store():' . $e->getMessage());
            return redirect()->route('shopping-lists.index')->with('error', 'Wystąpił błąd podczas dodawania listy zakupów.');
        }
    }
}
