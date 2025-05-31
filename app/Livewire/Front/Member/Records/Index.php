<?php

namespace App\Livewire\Front\Member\Records;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
   
    public function render()
    {
        return view('livewire.front.member.records.index');
    }
}
