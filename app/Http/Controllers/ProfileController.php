<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    protected function authUserArray()
    {
        $user = auth()->user();
        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'plan' => $user->plan ? [
                'id' => $user->plan->id,
                'name' => $user->plan->name,
                'price' => $user->plan->price,
            ] : null,
        ] : null;
    }

    public function edit(Request $request)
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'status' => session('status'),
            'auth' => ['user' => $this->authUserArray()],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $user->fill($request->only(['name', 'email']));
        $user->save();

        return redirect()->route('profile.edit');
    }

    public function show()
    {
        return Inertia::render('Profile', [
            'auth' => [
                'user' => $this->authUserArray(),
            ],
        ]);
    }

}
