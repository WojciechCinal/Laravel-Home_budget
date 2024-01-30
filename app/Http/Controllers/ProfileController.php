<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('User.profile');
    }

    public function edit()
    {
        return view('User.edit');
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $data['id_user'] = Auth::id();
        $user = User::find($data['id_user']);


        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:240', 'min:3'],
            'email' => ['required', 'string', 'email', 'min:5', 'max:255', 'unique:users,email,' . $user->id_user . ',id_user'],
            'monthly_budget' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
        ], [
            'name.required' => 'Nazwa użytkownika jest wymagana.',
            'name.min' => 'Nazwa użytkownika musi mieć przynajmniej :min znaki.',
            'name.max' => 'Nazwa użytkownika może mieć maksymalnie :max znaków.',
            'email.required' => 'Pole email jest wymagane.',
            'email.email' => 'Podaj poprawny adres email.',
            'email.max' => 'Adres email może mieć przynajmniej :min znaków.',
            'email.max' => 'Adres email może mieć maksymalnie :max znaków.',
            'email.unique' => 'Podany adres email jest już zajęty.',
            'monthly_budget.required' => 'Pole miesięczny budżet jest wymagane.',
            'monthly_budget.regex' => 'Prawidłowy format to np: 4200.22',
        ]);

        // Sprawdź, czy walidacja zakończyła się sukcesem
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'monthly_budget' => $data['monthly_budget'],
        ]);

        // Przekieruj użytkownika z komunikatem o sukcesie
        return redirect()->route('profile.index')->with('success', 'Dane zostały zaktualizowane pomyślnie.');
    }

    public function editPassword()
    {
        return view('User.changePassword');
    }


    public function changePassword(Request $request)
    {
        $user = User::find(Auth::id());

        $messages = [
            'current_password.required' => 'Podaj aktualne hasło.',
            'new_password.required' => 'Podaj nowe hasło.',
            'confirm_password.required' => 'Potwierdź nowe hasło.',
            'new_password.min' => 'Hasło musi mieć przynajmniej :min znaków.',
            'confirm_password.same' => 'Podane hasła są różne.',
        ];

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ],$messages);

        if (!Hash::check($data['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Podane hasło jest nieprawidłowe.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return redirect()->route('profile.index')->with('success', 'Hasło zostało zmienione pomyślnie.');
    }
}
