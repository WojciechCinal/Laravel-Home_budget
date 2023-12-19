<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list($id)
    {
        try {
            $user = Auth::user();
            $category = Category::where('id_category', $id)
                ->where('id_user', $user->id_user)
                ->firstOrFail();

            // Pobranie podkategorii należących do wybranej kategorii
            $subCategories = SubCategory::where('id_user', $user->id_user)
                ->where('id_category', $id)
                ->get();

            return view('category.subCategoryList', compact('subCategories', 'category'));
        } catch (\Exception $e) {
            Log::error('Błąd w metodzie list(): ' . $e->getMessage());
            return redirect()->route('category.list')->with('error', 'Nie masz dostępu do tych podkategorii!');
        }
    }

    public function updateSubcategoryStatus(Request $request, $id)
    {
        try {
            $subCategory = SubCategory::findOrFail($id);

            $isActive = $request->input('is_active');
            $subCategory->is_active = $isActive;
            $subCategory->save();

            return response()->json([
                'subCategoryId' => $subCategory->id_subCategory,
                'isActive' => $subCategory->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd w metodzie updateSubcategoryStatus(): ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas aktualizacji statusu podkategorii!'], 500);
        }
    }

    public function updateSubcategoryName(Request $request, $id)
    {
        try {
            $subCategory = SubCategory::findOrFail($id);

            $subCategory->name_subCategory = $request->input('name_subCategory');
            $subCategory->save();

            return response()->json(['message' => 'Nazwa podkategorii została zaktualizowana.']);
        } catch (\Exception $e) {
            Log::error('Błąd w metodzie updateSubcategoryName(): ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas aktualizacji nazwy podkategorii!'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Błąd w metodzie store(): ' . $e->getMessage());
            return redirect()->route('category.list')->with('error', 'Wystąpił błąd podczas dodawania nowej podkategorii!');
        }
    }
    public function create($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        return view('Category.subCategoryNew', compact('category'));
    }

}
