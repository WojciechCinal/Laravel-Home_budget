<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
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

    public function edit($id)
    {
        // Pobierz listę zakupów o danym ID i przekaż do widoku edycji
        $shoppingList = ShoppingList::find($id);
        return view('shopping_lists.edit', compact('shoppingList'));
    }

    public function show($id)
    {
        // Pobierz listę zakupów o danym ID i przekaż do widoku szczegółów
        $shoppingList = ShoppingList::find($id);
        return view('shopping_lists.show', compact('shoppingList'));
    }
}
