<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list()
    {
        $user = Auth::user(); // Pobierz zalogowanego użytkownika
        $categories = Category::where('id_user', $user->id_user)->get(); // Pobierz kategorie użytkownika

        return view('category.categoryList', compact('categories'));
    }

    public function update(Request $request, $id_category)
    {
        $category = Category::find($id_category);

        if ($category) {
            $category->name_category = $request->input('name_category');
            $category->save();

            return redirect()->back();
        }
        return redirect()->back()->withInput();
    }

    public function create()
    {
        return view('category.categoryNew'); // Zwraca widok formularza do tworzenia nowej kategorii
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $category = new Category();
        $category->name_category = $request->input('category_name');
        $category->id_user = $user->id_user; // Przypisz ID aktualnie zalogowanego użytkownika
        $category->save();

        return redirect()->route('category.list')->with('success', 'Kategoria została dodana.');
    }
}
