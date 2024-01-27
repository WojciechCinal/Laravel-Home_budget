<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'name.required' => 'Nazwa użytkownika jest wymagana.',
            'name.min' => 'Nazwa użytkownika musi mieć przynajmniej :min znaków.',
            'name.max' => 'Nazwa użytkownika może mieć maksymalnie :max znaków.',
            'email.required' => 'Pole email jest wymagane.',
            'email.email' => 'Podaj poprawny adres email.',
            'email.max'=>'Adres email może mieć maksymalnie :max znaków.',
            'email.unique' => 'Podany adres email jest już zajęty.',
            'password.required' => 'Pole hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć przynajmniej :min znaków.',
            'password.confirmed' => 'Podane hasła są różne.',
            'monthly_budget.required' => 'Pole miesięczny budżet jest wymagane.',
            'monthly_budget.regex' => 'Prawidłowy format to np: 4200.22',
        ];

        $data = Validator::make($data, [
            'name' => ['required', 'string', 'max:240', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'monthly_budget' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
        ], $messages);

        return $data;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'monthly_budget' => $data['monthly_budget'],
            'id_role' => 2
        ]);
        $this->seedCategorySubCategory($user->id_user);
        return $user;
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
