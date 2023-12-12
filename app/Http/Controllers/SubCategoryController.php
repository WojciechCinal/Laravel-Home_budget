<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                                    ->where('is_active', true)
                                    ->get();

        return view('category.subCategoryList', compact('subCategories'));
    }

}
