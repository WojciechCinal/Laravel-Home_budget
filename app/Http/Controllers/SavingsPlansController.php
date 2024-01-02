<?php

namespace App\Http\Controllers;

use App\Models\SavingsPlan;
use App\Models\Priority;
use Carbon\Carbon;
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

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = SavingsPlan::where('id_user', $user->id_user);

            // Zawsze sortuj po id_priority
            $query->orderBy('id_priority', 'asc');

            // Sortowanie daty zakończenia
            if ($request->has('sort_end_date')) {
                $sortBy = $request->input('sort_end_date');
                $query->orderBy('end_date_savings_plan', $sortBy);
            } else {
                $query->orderBy('end_date_savings_plan', 'asc'); // Domyślne sortowanie, jeśli nie wybrano
            }

            // Wybór priorytetów do wyświetlenia - wszystkie
            $selectedPriorities = [1, 2, 3, 4, 5]; // Wszystkie priorytety
            if ($request->has('sort_priority')) {
                $selectedPriorities = $request->input('sort_priority');
            }
            $query->whereIn('id_priority', $selectedPriorities);

            // Filtrowanie po statusie ukończenia
            if ($request->has('sort_completed')) {
                $completed = $request->input('sort_completed');
                $query->where('is_completed', (bool) $completed);
            } else {
                $query->where('is_completed', (bool) false);
            }
            $savingsPlans = $query->paginate(6);

            // Sprawdzenie, czy lista jest pusta
            $isEmpty = $savingsPlans->isEmpty();

            foreach ($savingsPlans as $plan) {
                if ($plan->is_completed == 1) {
                    $plan->months_remaining = "-";
                    $plan->monthly_deposit_needed = "-";
                } else {
                    $endDate = Carbon::parse($plan->end_date_savings_plan);
                    $daysRemaining = $endDate->diffInDays(Carbon::now());
                    $monthsRemaining = $endDate->diffInMonths(Carbon::now());

                    if ($endDate->isPast()) {
                        $daysOverdue = $endDate->diffInDays(Carbon::now());
                        $plan->deadline = "Przekroczono termin o " . $daysOverdue . " dni!";
                    } elseif ($daysRemaining < 31) {
                        $plan->months_remaining = $daysRemaining . " dni";
                    } else {

                        $plan->months_remaining = $monthsRemaining . " mies.";
                    }

                    $remainingAmount = max(0, $plan->goal_savings_plan - $plan->amount_savings_plan);
                    $monthlyDeposit = $remainingAmount / max(1, $monthsRemaining);

                    $plan->monthly_deposit_needed = round($monthlyDeposit, 2);
                }
            }
            if ($isEmpty) {
                $msg = "Brak planów oszczędnościowych spełniających kryteria.";
                Session::flash('message', $msg);

                return view('SavingsPlan.index', compact('savingsPlans'));
            } else {
                return view('SavingsPlan.index', compact('savingsPlans'));
            }
        } catch (\Exception $e) {
            Log::error('SavingsPlansController. Błąd w metodzie index(): ' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Wystąpił błąd podczas pobierania planów oszczędnościowych.');
        }
    }

    public function edit($id)
    {
        try {
            $user = Auth::user();
            $savingsPlan = SavingsPlan::where('id_savings_plan', $id)
                ->where('id_user', $user->id_user)
                ->first();

            if (!$savingsPlan) {
                return redirect()->route('SavingsPlan.index')->with('error', 'Nie masz dostępu do tego planu oszczędnościowego!');
            }

            return view('SavingsPlan.edit', compact('savingsPlan'));
        } catch (\Exception $e) {
            Log::error('SavingsPlanController. Błąd w metodzie edit():' . $e->getMessage());
            return redirect()->route('SavingsPlan.index')->with('error', 'Nie udało się zedytować planu oszczędnościowego. Spróbuj ponownie później.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $savingsPlan = SavingsPlan::where('id_savings_plan', $id)
                ->where('id_user', $user->id_user)
                ->first();

            if (!$savingsPlan) {
                return redirect()->route('savings-plans.index')->with('error', 'Nie masz dostępu do tego planu oszczędnościowego!');
            }

            $savingsPlan->name_savings_plan = $request->input('name');
            $savingsPlan->id_priority = $request->input('priority');
            $savingsPlan->end_date_savings_plan = $request->input('end_date');
            $savingsPlan->goal_savings_plan = $request->input('goal');

            $savingsPlan->save();

            return redirect()->route('savings-plans.index')->with('success', 'Plan oszczędnościowy został zaktualizowany pomyślnie!');
        } catch (\Exception $e) {
            Log::error('SavingsPlanController. Błąd w metodzie update():' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Nie udało się zaktualizować planu oszczędnościowego. Spróbuj ponownie później.');
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
