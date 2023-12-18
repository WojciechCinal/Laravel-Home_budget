<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Session;

class ShoppingListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $shoppingLists = ShoppingList::all()
            ->where('id_user', $user->id_user)
            ->sortByDesc('updated_at')
            ->map(function ($list) {
                $list->formatted_updated_at = Carbon::parse($list->updated_at)->translatedFormat('H:i, d M Y');
                return $list;
            });

        return view('shopping_lists.index', compact('shoppingLists'));
    }

    public function destroy($id)
    {
        $shoppingList = ShoppingList::find($id);

        if ($shoppingList) {
            $shoppingList->delete();

            $msg = "Lista zakupów: $shoppingList->title_shopping_list została pomyślnie usunięta.";
            Session::flash('success', $msg);

            return redirect()->route('shopping-lists.index')->with('success', "Lista zakupów: $shoppingList->title_shopping_list została pomyślnie usunięta.");
        }
        return redirect()->route('shopping-lists.index')->with('error', 'Nie znaleziono takiej listy zakupów!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $shoppingList = ShoppingList::where('id_shopping_list', $id)
            ->where('id_user', $user->id_user)
            ->first();

        if (!$shoppingList) {
            return redirect()->route('shopping-lists.index')->with('error', 'Nie masz dostępu do tej listy zakupów.');
        }

        return view('shopping_lists.edit', compact('shoppingList'));
    }

    public function update(Request $request, $id)
    {
        $shoppingList = ShoppingList::findOrFail($id);

        $shoppingList->update([
            'title_shopping_list' => $request->input('title'),
            'description_shopping_list' => $request->input('description')
        ]);

        return redirect()->route('shopping-lists.index')->with('success', 'Lista zakupów została pomyślnie zaktualizowana.');
    }

    public function create()
    {
        return view('shopping_lists.new');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'title_shopping_list' => 'required|max:255',
            'description_shopping_list' => 'max:500',
        ]);

        $shoppingList = ShoppingList::create([
            'title_shopping_list' => $validatedData['title_shopping_list'],
            'description_shopping_list' => $validatedData['description_shopping_list'],
            'id_user' => $user->id_user, // Ustawienie id_user na ID zalogowanego użytkownika
        ]);

        if ($shoppingList) {
            return redirect()->route('shopping-lists.index')->with('success', 'Pomyślnie dodano nową listę zakupów.');
        }
        return redirect()->route('shopping-lists.index')->with('error', 'Wystąpił błąd podczas dodawania listy zakupów.');

    }
}
