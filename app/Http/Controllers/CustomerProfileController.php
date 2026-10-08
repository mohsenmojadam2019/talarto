<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CustomerProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        $user->update(['name' => $data['name'], 'email' => $data['email'] ?? null]);
        return redirect()->route('account.profile')->with('success', 'مشخصات حساب شما به‌روزرسانی شد.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ]);
        $request->user()->update([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ]);
        $request->session()->regenerate();
        return redirect()->route('account.profile')->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}
