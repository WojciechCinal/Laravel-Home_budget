<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        $subcategories = SubCategory::where('id_user', $user->id_user)->get(); // Pobierz podkategorie użytkownika

        return view('category.categoryList', compact('categories', 'subcategories'));
    }

    public function update(Request $request, $id_category)
    {
        $category = Category::find($id_category);

        if ($category) {
            $category->name_category = $request->input('name_category');
            $category->save();

            return response()->json(['message' => 'Category updated successfully']);
        }

        return response()->json(['error' => 'Category not found'], 404);
    }

}
