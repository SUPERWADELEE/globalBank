<div class="min-h-screen bg-gray-50 p-4">
    <!-- 主容器 -->
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
        
        <!-- 頂部導航欄 -->
        <div class="bg-white p-4 flex items-center justify-between border-b border-gray-100">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-sm"></i>
                </div>
                <div>
                    <span class="text-lg font-bold text-gray-700">Wallet</span>
                    <div class="text-xs text-gray-500">CURRENCY EXCHANGE</div>
                </div>
            </div>
            
            <!-- 用戶信息 -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">{{ __('dashboard.hi_user', ['name' => $userName]) }}</span>
                <div class="bg-gray-700 text-white text-xs px-2 py-1 rounded-full flex items-center space-x-1">
                    <i class="fas fa-crown text-yellow-400"></i>
                    <span>{{ __('dashboard.vip_level', ['level' => '1']) }}</span>
                </div>
            </div>
        </div>

        <!-- 錢包餘額區域 -->
        <div class="bg-gradient-to-br from-blue-100 to-blue-50 p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-blue-400 mb-1">{{ __('dashboard.my_wallet') }}</h2>
                <p class="text-gray-500 text-sm">My Wallet</p>
            </div>

            <!-- 總餘額顯示 -->
            <div class="text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">{{ __('dashboard.wallet_balance') }}</h3>
                <div class="bg-white rounded-xl p-4 flex items-center justify-between shadow-sm">
                    <span class="text-2xl font-bold text-blue-500">{{ number_format($totalBalance, 1) }} USDT</span>
                    <button wire:click="refreshBalance" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500 transition-colors">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 貨幣明細 -->
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('dashboard.currency_details') }}</h3>
            
            <div class="space-y-4">
                @foreach($wallets as $wallet)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 {{ $wallet['color'] }} rounded-full flex items-center justify-center text-white">
                            <span class="text-lg font-bold">{{ $wallet['symbol'] }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">{{ $wallet['currency'] }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-semibold text-blue-400">{{ number_format($wallet['balance'], 3) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- 操作按鈕 -->
            <div class="flex space-x-4 mt-8">
                <button class="flex-1 bg-blue-400 hover:bg-blue-500 text-white font-medium py-3 rounded-xl transition-colors">
                    {{ __('dashboard.send') }}
                </button>
                <button class="flex-1 bg-blue-400 hover:bg-blue-500 text-white font-medium py-3 rounded-xl transition-colors">
                    {{ __('dashboard.receive') }}
                </button>
            </div>
        </div>

        <!-- 客服按鈕 -->
        <div class="fixed bottom-20 right-4">
            <button class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center border border-gray-200">
                <i class="fas fa-headset text-gray-600"></i>
            </button>
            <div class="text-xs text-gray-500 text-center mt-1">{{ __('dashboard.customer_service') }}</div>
        </div>

        <!-- 底部導航 -->
        <div class="bg-white border-t border-gray-100 p-4">
            <div class="grid grid-cols-4 gap-4">
                <!-- 我的錢包 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-400 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <span class="text-xs text-blue-400 font-medium">{{ __('dashboard.my_wallet') }}</span>
                </div>
                
                <!-- 即時匯兌 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-gray-500"></i>
                    </div>
                    <span class="text-xs text-gray-500">{{ __('dashboard.instant_exchange') }}</span>
                </div>
                
                <!-- 交易紀錄 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-file-alt text-gray-500"></i>
                    </div>
                    <span class="text-xs text-gray-500">{{ __('dashboard.transaction_history') }}</span>
                </div>
                
                <!-- 會員中心 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-gray-500"></i>
                    </div>
                    <span class="text-xs text-gray-500">{{ __('dashboard.member_center') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
