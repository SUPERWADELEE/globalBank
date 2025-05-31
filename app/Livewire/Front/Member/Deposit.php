<?php

namespace App\Livewire\Front\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Deposit extends Component
{
   
    public function render()
    {
        return view('livewire.front.member.deposit');
    }
}
