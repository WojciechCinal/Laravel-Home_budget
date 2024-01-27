<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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
                ->first();

            if (!$category) {
                return redirect()->route('category.list')->with('error', 'Nie masz dostępu do tych podkategorii!');
            }

            // Pobranie podkategorii należących do wybranej kategorii
            $subCategories = SubCategory::where('id_user', $user->id_user)
                ->where('id_category', $id)
                ->paginate(8);

            return view('category.subCategoryList', compact('subCategories', 'category'));
        } catch (\Exception $e) {
            Log::error('SubCategoryController. Błąd w metodzie list(): ' . $e->getMessage());
            return redirect()->route('category.list')->with('error', 'Wystąpił błąd prosimy spróbować później.');
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
            Log::error('SubCategoryController. Błąd w metodzie updateSubcategoryStatus(): ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas aktualizacji statusu podkategorii.'], 500);
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
            Log::error('SubCategoryController. Błąd w metodzie updateSubcategoryName(): ' . $e->getMessage());
            return response()->json(['error' => 'Wystąpił błąd podczas aktualizacji nazwy podkategorii.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Wywołanie osobnej metody walidacji
            $validator = $this->validateSubCategory($request);

            // Sprawdź, czy walidacja zakończyła się sukcesem
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = Auth::user();

            SubCategory::create([
                'name_subCategory' => $request->input('name_subCategory'),
                'id_category' => $request->input('category_id'),
                'id_user' => $user->id_user,
            ]);

            return redirect()->route('subCategory.list', ['id' => $request->input('category_id')])
                ->with('success', 'Dodano nową podkategorię.');
        } catch (\Exception $e) {
            Log::error('SubCategoryController. Błąd w metodzie store(): ' . $e->getMessage());
            return redirect()->route('category.list')->with('error', 'Wystąpił błąd podczas dodawania nowej podkategorii.');
        }
    }

    // Osobna metoda walidacji dla SubCategory
    private function validateSubCategory(Request $request)
    {
        return Validator::make($request->all(), [
            'name_subCategory' => ['required', 'string', 'min:3', 'max:100'],
            'category_id' => ['required', 'exists:categories,id_category'],
        ], [
            'name_subCategory.required' => 'Nazwa podkategorii jest wymagana.',
            'name_subCategory.min' => 'Nazwa podkategorii musi mieć przynajmniej :min znaki.',
            'name_subCategory.max' => 'Nazwa podkategorii może mieć maksymalnie :max znaków.',
        ]);
    }

    public function create($categoryId)
    {
        $user = Auth::user();
        try {
            $category = Category::where('id_category', $categoryId)
                ->where('id_user', $user->id_user)
                ->first();

            if ($category) {
                return view('Category.subCategoryNew', compact('category'));
            } else {
                return redirect()->route('category.list')->with('error', 'Nie masz dostępu do tej kategorii!');
            }
        } catch (\Exception $e) {
            Log::error('SubCategoryController. Błąd w metodzie create(): ' . $e->getMessage());
            return redirect()->route('category.list')->with('error', 'Wystąpił błąd podczas dodawania nowej podkategorii.');
        }
    }
}
