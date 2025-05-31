<?php

namespace App\Livewire\Front\Member\Records;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Show extends Component
{
   
    public function render()
    {
        return view('livewire.front.member.records.show');
    }
}
