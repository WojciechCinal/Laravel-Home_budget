<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        try {
            $user = Auth::user();
            $searchTerm = $request->input('search');

            $categories = Category::where('id_user', $user->id_user)
                ->withCount(['subcategories' => function (Builder $query) {
                    $query->where('is_active', true);
                }])
                ->when(!$searchTerm, function ($query) {
                    $query->where('is_active', true);
                })
                ->when($searchTerm, function ($query, $searchTerm) {
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('name_category', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('subcategories', function ($query) use ($searchTerm) {
                                $query->where('name_subCategory', 'like', '%' . $searchTerm . '%');
                            });
                    });
                })
                ->paginate(8);

            if ($categories->isNotEmpty()) {
                return view('category.categoryList', [
                    'categories' => $categories
                ]);
            } else {
                return redirect()->route('category.list')->with('message', "Brak rekordów dla frazy: $searchTerm.");
            }
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie list(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas pobierania listy kategorii!');
        }
    }

    public function update(Request $request, $id_category)
    {
        try {
            $category = Category::find($id_category);

            if ($category) {
                $category->name_category = $request->input('name_category');
                $category->save();

                return redirect()->back();
            }
            return redirect()->back()->withInput();
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie update(): ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Wystąpił błąd podczas aktualizacji kategorii!');
        }
    }

    public function create()
    {
        return view('category.categoryNew');
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $category = new Category();
            $category->name_category = $request->input('category_name');
            $category->id_user = $user->id_user;
            $category->save();

            return redirect()->route('category.list')->with('success', "Kategoria: $category->name_category została dodana do listy.");
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie store(): ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Wystąpił błąd podczas dodawania kategorii!');
        }
    }

    public function archive($id)
    {
        try {
            $user = Auth::user();
            $category = Category::where('id_category', $id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();

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
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie archive(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas archiwizacji kategorii.');
        }
    }

    public function archiveList()
    {
        try {
            $user = Auth::user();
            $archivedCategories = Category::where('id_user', $user->id_user)->where('is_active', false)->get();

            if ($archivedCategories->isEmpty()) {
                return redirect()->back()->with('message', 'Brak zarchiwizowanych kategorii.');
            }

            return view('category.categoryArchive', compact('archivedCategories'));
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie archiveList(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas wczytywania archium z kategoriami.');
        }
    }

    public function restore($id)
    {
        try {
            $category = Category::find($id);
            if ($category) {
                $category->is_active = true;
                $category->save();

                $msg = "Kategoria $category->name_category została przywrócona.";

                return redirect()->route('category.list')->with('success', $msg);
            }

            Session::flash('error', 'Nie znaleziono takiej kategorii!');
            return redirect()->back()->withInput();
        } catch (Exception $e) {
            Log::error('CategoryController. Wystąpił błąd w metodzie restore(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas usuwania kategorii z archiwum.');
        }
    }
}
