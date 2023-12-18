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

            return redirect()->route('shopping-lists.index');
        }
        Session::flash('error', 'Nie znaleziono takiej listy zakupów!');
        return redirect()->route('shopping-lists.index');
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
}
