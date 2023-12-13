<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubCategoryController extends Controller
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
    public function list($id)
    {
        $user = Auth::user();
        $category = Category::where('id_category', $id)
            ->where('id_user', $user->id_user)
            ->first();

        if (!$category) {
            // Obsługa sytuacji, gdy użytkownik próbuje uzyskać dostęp do kategorii, do której nie ma dostępu
            return redirect()->route('home')->with('error', 'Nie masz dostępu do tej kategorii.');
        }

        // Pobranie podkategorii należących do wybranej kategorii
        $subCategories = SubCategory::where('id_user', $user->id_user)
            ->where('id_category', $id)
            ->get();

        return view('category.subCategoryList', compact('subCategories', 'category'));
    }

    public function updateSubcategoryStatus(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);

        if ($subCategory) {
            $isActive = $request->input('is_active');
            $subCategory->is_active = $isActive;
            $subCategory->save();

            return response()->json([
                'subCategoryId' => $subCategory->id_subCategory,
                'isActive' => $subCategory->is_active
            ]);
        }

        return response()->json(['error' => 'Nie znaleziono tej podkategorii'], 404);
    }

    public function updateSubcategoryName(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);

        if ($subCategory) {
            $subCategory->name_subCategory = $request->input('name_subCategory');
            $subCategory->save();

            // Tutaj możesz zwrócić odpowiedź JSON w razie potrzeby
            return response()->json(['message' => 'Nazwa podkategorii została zaktualizowana.']);
        }

        // Obsługa, gdy nie znaleziono podkategorii
        return response()->json(['error' => 'Nie znaleziono podkategorii.'], 404);
    }

    public function create($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        return view('Category.subCategoryNew', compact('category'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name_subCategory' => 'required|string',
            'category_id' => 'required|exists:categories,id_category',
        ]);

        SubCategory::create([
            'name_subCategory' => $data['name_subCategory'],
            'id_category' => $data['category_id'],
            'id_user' => $user->id_user
        ]);

        return redirect()->route('subCategory.list', ['id' => $data['category_id']])
            ->with('success', 'Dodano nową podkategorię.');
    }
}
