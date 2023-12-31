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

    public function create()
    {
        $priorities = Priority::all();
        return view('savingsPlan.new', compact('priorities'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_savings_plan' => 'required|string',
            'goal_savings_plan' => 'required|numeric',
            'end_date_savings_plan' => 'required|date',
            'priority_id' => 'required|exists:priorities,id_priority'
        ]);


        $savingsPlan = SavingsPlan::create([
            'name_savings_plan' => $validatedData['name_savings_plan'],
            'goal_savings_plan' => $validatedData['goal_savings_plan'],
            'end_date_savings_plan' => $validatedData['end_date_savings_plan'],
            'id_user' => auth()->id(),
            'id_priority' => $validatedData['priority_id'],
            'is_completed' => false
        ]);

        return redirect()->route('savings-plans.index')->with('success', 'Nowy cel oszczędnościowy został dodany!');
    }
}
