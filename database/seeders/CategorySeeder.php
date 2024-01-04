<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $userId = $user->id_user;

            // MIESZKANIE
            $home = Category::create([
                'name_category' => 'Mieszkanie',
                'id_user' => $userId,
                'name_start' => 'Mieszkanie'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Czynsz',
                'id_category' => $home->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Rachunki',
                'id_category' => $home->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Remonty',
                'id_category' => $home->id_category,
                'id_user' => $userId
            ]);


            // TRANSPORT
            $transport = Category::create([
                'name_category' => 'Transport',
                'id_user' => $userId,
                'name_start' => 'Transport'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Paliwo',
                'id_category' => $transport->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Serwis samochodu',
                'id_category' => $transport->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Ubezpieczenie pojazdu',
                'id_category' => $transport->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Transport publiczny',
                'id_category' => $transport->id_category,
                'id_user' => $userId
            ]);


            // ROZRYWKA
            $entertainment = Category::create([
                'name_category' => 'Rozrywka',
                'id_user' => $userId,
                'name_start' => 'Rozrywka'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Kino / Teatr',
                'id_category' => $entertainment->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Restauracja',
                'id_category' => $entertainment->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Jedzenie na wynos',
                'id_category' => $entertainment->id_category,
                'id_user' => $userId
            ]);

            // ŻYWNOŚĆ
            $food = Category::create([
                'name_category' => 'Żywność',
                'id_user' => $userId,
                'name_start' => 'Żywność'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Nabiał i jajka',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Mięso',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Produkty zbożowe',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Owoce i warzywa',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Słodycze i przekąski',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Alkohol',
                'id_category' => $food->id_category,
                'id_user' => $userId
            ]);

            // ODZIEŻ
            $clothes = Category::create([
                'name_category' => 'Odzież',
                'id_user' => $userId,
                'name_start' => 'Odzież'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Obuwie',
                'id_category' => $clothes->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Bielizna',
                'id_category' => $clothes->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Spodnie',
                'id_category' => $clothes->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Odzież wierzchnia',
                'id_category' => $clothes->id_category,
                'id_user' => $userId
            ]);

            // ZDROWIE I HIGIENA
            $health = Category::create([
                'name_category' => 'Zdrowie i higiena',
                'id_user' => $userId,
                'name_start' => 'Zdrowie i higiena'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Wizyty u lekarza',
                'id_category' => $health->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Środki higieny',
                'id_category' => $health->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Lekarstwa',
                'id_category' => $health->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Suplementy diety',
                'id_category' => $health->id_category,
                'id_user' => $userId
            ]);

            // HOBBY I REKREACJA
            $hobby = Category::create([
                'name_category' => 'Hobby i rekreacja',
                'id_user' => $userId,
                'name_start' => 'Hobby i rekreacja'
            ]);

            // EDUKACJA
            $study = Category::create([
                'name_category' => 'Edukacja',
                'id_user' => $userId,
                'name_start' => 'Edukacja'
            ]);

            SubCategory::create([
                'name_subCategory' => 'Kursy',
                'id_category' => $study->id_category,
                'id_user' => $userId
            ]);
            SubCategory::create([
                'name_subCategory' => 'Książki',
                'id_category' => $study->id_category,
                'id_user' => $userId
            ]);

            $savings = Category::create([
                'name_category' => 'Plany oszczędnościowe',
                'id_user' => $userId,
                'name_start' => 'Plany oszczędnościowe'
            ]);
        }
    }
}
