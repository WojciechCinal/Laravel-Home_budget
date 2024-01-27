<?php

namespace App\Http\Controllers;

use App\Models\SavingsPlan;
use App\Models\Priority;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            $selectedPriorities = [1, 2, 3, 4, 5];
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
                if (SavingsPlan::where('id_user', $user->id_user)->count() == 0) {
                    $msg = "Brak planów oszczędnościowych - utwórz nowy.";
                    session()->flash('sortSavingsPlans', $msg);
                } else {

                    $msg = "Brak planów oszczędnościowych spełniających kryteria filtrowania.";
                    session()->flash('sortSavingsPlans', $msg);
                }
            }


            return view('SavingsPlan.index', compact('savingsPlans'));
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
                return redirect()->route('savings-plans.index')->with('error', 'Nie masz dostępu do tego planu oszczędnościowego!');
            }

            return view('SavingsPlan.edit', compact('savingsPlan'));
        } catch (\Exception $e) {
            Log::error('SavingsPlanController. Błąd w metodzie edit():' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Nie udało się zedytować planu oszczędnościowego. Spróbuj ponownie później.');
        }
    }

    private function validateUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'goal' => ['required', 'numeric', 'min:50'],
            'end_date' => ['required', 'date'],
        ], [
            'name.required' => 'Nazwa celu oszczędnościowego jest wymagana.',
            'name.min' => 'Nazwa celu oszczędnościowego musi mieć przynajmniej :min znaki.',
            'name.max' => 'Nazwa celu oszczędnościowego może mieć maksymalnie :max znaków.',
            'goal.required' => 'Cel oszczędnościowy (PLN) jest wymagany.',
            'goal.numeric' => 'Cel oszczędnościowy musi być liczbą.',
            'goal.min' => 'Cel oszczędnościowy musi być większy niż :min.',
            'end_date.required' => 'Planowana data zakończenia jest wymagana.',
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = $this->validateUpdate($request);

            // Sprawdź, czy walidacja zakończyła się sukcesem
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

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

    public function updateAmount(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $savingsPlan = SavingsPlan::where('id_savings_plan', $id)
                ->where('id_user', $user->id_user)
                ->first();

            if (!$savingsPlan) {
                return redirect()->route('savings-plans.index')->with('error', 'Nie masz dostępu do tego planu oszczędnościowego!');
            }

            $increaseAmount = $request->input('increase_amount');

            $category = Category::where('id_user', $user->id_user)
                ->where('name_start', 'Plany oszczędnościowe')
                ->first();

            if (!$category) {
                return redirect()->route('savings-plans.index')->with('error', 'Nie znaleziono kategorii oszczędnościowej!');
            }

            // Aktualne wydatki w bieżącym miesiącu
            $expensesThisMonth = Transaction::where('id_user', $user->id_user)
                ->whereYear('date_transaction', date('Y'))
                ->whereMonth('date_transaction', date('m'))
                ->sum('amount_transaction');

            // Oblicz dostępną kwotę do wydania w tym miesiącu
            $remainingFunds = $user->monthly_budget - $expensesThisMonth;

            // Czy dodanie wpłacanej kwoty nie przekroczy miesięcznego budżetu
            if ($increaseAmount > $remainingFunds) {
                $message = 'Dodanie tej kwoty przekroczy miesięczny budżet! Środków do wydania pozostało: ' . $remainingFunds . ' PLN.';
                session()->flash('warning', $message);
            }

            $newAmount = $savingsPlan->amount_savings_plan + $increaseAmount;

            if ($newAmount > $savingsPlan->goal_savings_plan) {
                return redirect()->route('savings-plans.index')->with('warning', 'Wpłacana kwota przekroczyłaby cel!');
            } elseif ($newAmount == $savingsPlan->goal_savings_plan) {
                $savingsPlan->amount_savings_plan = $newAmount;
                $savingsPlan->is_completed = 1;
                $savingsPlan->save();

                $transaction = Transaction::create([
                    'name_transaction' => 'Wpłata do planu oszczędnościowego ' . $savingsPlan->name_savings_plan,
                    'amount_transaction' => $increaseAmount,
                    'date_transaction' => now(),
                    'id_user' => $user->id_user,
                    'id_category' => $category->id_category,
                ]);

                return redirect()->route('savings-plans.index')->with('success', "Plan oszczędnościowy '$savingsPlan->name_savings_plan' został zrealizowany!");
            } else {
                $savingsPlan->amount_savings_plan = $newAmount;
                $savingsPlan->save();

                // Tworzenie nowej transakcji
                $transaction = Transaction::create([
                    'name_transaction' => 'Wpłata do planu oszczędnościowego ' . $savingsPlan->name_savings_plan,
                    'amount_transaction' => $increaseAmount,
                    'date_transaction' => now(),
                    'id_user' => $user->id_user,
                    'id_category' => $category->id_category,
                ]);

                return redirect()->route('savings-plans.index')->with('success', 'Kwota oszczędności została zwiększona pomyślnie!');
            }
        } catch (\Exception $e) {
            Log::error('SavingsPlansController. Błąd w metodzie updateAmount(): ' . $e->getMessage());
            return redirect()->route('savings-plans.index')->with('error', 'Nie udało się zwiększyć kwoty oszczędnościowej. Spróbuj ponownie później.');
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
            'name_savings_plan' => 'required|string|min:3|max:100',
            'goal_savings_plan' => 'required|numeric|min:50',
            'end_date_savings_plan' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
            'priority_id' => 'required|exists:priorities,id_priority'
        ], [
            'name_savings_plan.required' => 'Nazwa celu oszczędnościowego jest wymagana.',
            'name_savings_plan.min' => 'Nazwa celu oszczędnościowego musi mieć przynajmniej :min znaki.',
            'name_savings_plan.max' => 'Nazwa celu oszczędnościowego może mieć maksymalnie :max znaków.',
            'goal_savings_plan.required' => 'Cel oszczędnościowy (PLN) jest wymagany.',
            'goal_savings_plan.numeric' => 'Cel oszczędnościowy musi być liczbą.',
            'goal_savings_plan.min' => 'Cel oszczędnościowy musi być większy niż :min.',
            'end_date_savings_plan.required' => 'Planowana data zakończenia jest wymagana.',
            'end_date_savings_plan.date' => 'Planowana data zakończenia musi być poprawną datą.',
            'end_date_savings_plan.after_or_equal' => 'Planowana data zakończenia musi być dzisiaj lub w przyszłości.',
            'priority_id.required' => 'Priorytet jest wymagany.',
            'priority_id.exists' => 'Wybrany priorytet jest nieprawidłowy.'
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
