<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Transaction;

class CreateFakeDataForUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for($i=1;$i<=15;$i++){
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 12345678,
                'monthly_budget' => $faker->numberBetween(4000, 10000),
                'id_role' => 2
            ]);
            $this->seedCategorySubCategory($user->id_user);
            $this->seedTransactions($user->id_user);
        }
    }

    protected function seedTransactions(int $userId)
    {
        $categories = Category::where('id_user', $userId)->get();

        $startDate = Carbon::create(2019, 1, 1);
        $endDate = Carbon::create(2024, 2, 15);

        $currentDate = $startDate->copy();

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            foreach ($categories as $category) {
                $j = rand(1, 5);
                for ($i = 0; $i < $j; $i++) {
                    $transaction = new Transaction();
                    $transaction->name_transaction = 'Fake Transaction';
                    $transaction->amount_transaction = rand(1, 250);
                    $transaction->date_transaction = $currentDate->copy()->subDays(rand(1, 28));
                    $transaction->id_user = $userId;
                    $transaction->id_category = $category->id_category;
                    $transaction->save();
                }
            }

            $currentDate->addMonth();
        }
    }


    protected function seedCategorySubCategory(int $userId)
    {
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

        $noName = Category::create([
            'name_category' => 'Brak kategorii',
            'id_user' => $userId
        ]);
    }
}
