<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $totalBalance = 69827.5;
    public $wallets = [];
    public $userName = '';

    public function mount()
    {
        // 檢查用戶是否已認證 - 使用 web guard
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        // 只儲存用戶名稱，不儲存整個用戶物件
        $this->userName = Auth::guard('web')->user()->name ?? 'Edward';
        
        // 模擬錢包數據
        $this->wallets = [
            [
                'currency' => 'USDT',
                'symbol' => '₮',
                'balance' => 56789.12,
                'color' => 'bg-green-500',
                'icon' => 'fas fa-dollar-sign'
            ],
            [
                'currency' => 'KRW',
                'symbol' => '₩',
                'balance' => 3.456,
                'color' => 'bg-blue-500',
                'icon' => 'fas fa-won-sign'
            ],
            [
                'currency' => 'JPY',
                'symbol' => '¥',
                'balance' => 345.678,
                'color' => 'bg-blue-400',
                'icon' => 'fas fa-yen-sign'
            ],
            [
                'currency' => 'SGD',
                'symbol' => '$',
                'balance' => 5.678,
                'color' => 'bg-blue-300',
                'icon' => 'fas fa-dollar-sign'
            ]
        ];
    }

    public function refreshBalance()
    {
        // 這裡可以添加刷新餘額的邏輯
        $this->dispatch('balance-refreshed');
    }

    public function render()
    {
        return view('livewire.member.dashboard');
    }
}
