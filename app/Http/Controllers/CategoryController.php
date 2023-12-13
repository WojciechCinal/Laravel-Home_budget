<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Builder;
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
        $user = Auth::user();
        $categories = Category::where('id_user', $user->id_user)
            ->withCount(['subCategories' => function (Builder $query) {
                $query->where('is_active', true);
            }])
            ->get();

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

        return redirect()->route('category.list')->with('success', "$category->name_category została dodana do listy kategorii.");
    }

    public function archive($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->is_active = false;
            $category->save();

            $msg = "Kategoria $category->name_category została przeniesiona do archiwum.";
            Session::flash('success', $msg);

            // Zmiana is_active dla powiązanych podkategorii
            $subCategories = SubCategory::where('id_category', $id)->get();
            foreach ($subCategories as $subCategory) {
                $subCategory->is_active = false;
                $subCategory->save();
            }

            return redirect()->route('category.list');
        }

        Session::flash('error', 'Nie znaleziono tej kategorii!');
        return redirect()->back()->withInput();
    }


    public function archiveList()
    {
        $user = Auth::user();
        $archivedCategories = Category::where('id_user', $user->id_user)->where('is_active', false)->get();

        if ($archivedCategories->isEmpty()) {
            return redirect()->back()->with('message', 'Brak zarchiwizowanych kategorii');
        }

        return view('category.categoryArchive', compact('archivedCategories'));
    }

    public function restore($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->is_active = true;
            $category->save();

            $msg = "Kategoria $category->name_category została przywrócona.";

            return redirect()->route('category.list')->with('success', $msg);
        }

        Session::flash('error', 'Nie znaleziono tej kategorii!');
        return redirect()->back()->withInput();
    }
}
