<?php

namespace App\Livewire\Front\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Login extends Component
{
    public function render()
    {
        return view('livewire.front.auth.login');
    }
}
