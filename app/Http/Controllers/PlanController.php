<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlanController extends Controller
{
    protected function authUserArray()
    {
        $user = auth()->user();
        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // Можно добавить другие нужные поля, например, plan_id
            'plan_id' => $user->plan_id ?? null,
        ] : null;
    }

    // Список тарифов для выбора
    public function index()
    {
        $plans = Plan::all();
        $userPlanId = Auth::user()->plan_id ?? null;

        return Inertia::render('Plans/Index', [
            'plans' => $plans,
            'currentPlanId' => $userPlanId,
            'auth' => ['user' => $this->authUserArray()], // <-- добавлено
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    // Смена тарифа
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        $user->plan()->associate($plan);
        $user->save();

        return redirect()->back()->with('success', 'Тариф успешно изменён!');
    }

    // Отображение страницы оплаты
    public function payment(Plan $plan)
    {
        return Inertia::render('Plans/Payment', [
            'plan' => $plan,
            'auth' => ['user' => $this->authUserArray()], // <-- добавлено
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    // Обработка платежа (заглушка)
    public function pay(Request $request, Plan $plan)
    {
        $request->validate([
            'card_number' => 'required|string',
            'expiry_date' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $user = $request->user();

        $user->plan()->associate($plan);
        $user->save();

        return redirect()->route('plans.index')->with('success', 'Оплата прошла успешно! Тариф обновлен.');
    }
}
