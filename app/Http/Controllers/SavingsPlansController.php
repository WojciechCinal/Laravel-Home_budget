<?php

namespace App\Http\Controllers;

use App\Models\SavingsPlan;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SavingsPlansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $user = Auth::user();

            $savingsPlans = SavingsPlan::where('id_user', $user->id_user)
                ->orderBy('id_priority')
                ->paginate(6);

            return view('SavingsPlan.index', compact('savingsPlans'));
        } catch (\Exception $e) {
            Log::error('SavingsPlansController. Błąd w metodzie index(): ' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Wystąpił błąd podczas pobierania planów oszczędnościowych.');
        }
    }

    public function destroy($id)
    {
        try {
            $savingsPlan = SavingsPlan::find($id);

            if ($savingsPlan) {
                $savingsPlan->delete();

                $msg = "Plan oszczędnościowy: $savingsPlan->name_savings_plan został pomyślnie usunięty.";
                Session::flash('success', $msg);

                return redirect()->route('savings-plans.index')->with('success', $msg);
            }

            return redirect()->route('savings-plans.index')->with('error', 'Nie znaleziono takiego planu oszczędnościowego!');
        } catch (\Exception $e) {
            Log::error('SavingsPlansController. Błąd w metodzie destroy(): ' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Wystąpił błąd podczas usuwania planu oszczędnościowego!');
        }
    }
}
