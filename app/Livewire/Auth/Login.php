<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

#[Layout('layouts.guest')]
class Login extends Component
{
    public $username;
    public $password;
    public $remember = false;

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $this->username)->first();

        if (! $user) {
            $this->addError('username', __('auth.username_failed'));
            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('username', __('auth.password_failed'));
            return;
        }

        Auth::guard('web')->login($user, $this->remember);
        session()->regenerate();

        return redirect()->intended(route('member.dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
