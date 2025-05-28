<div class="min-h-screen bg-white flex flex-col">
    <!-- 頂部 Logo 區域 -->
    <div class="bg-white px-6 pt-12 pb-6 border-b border-gray-100">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-wallet text-white text-xl"></i>
            </div>
            <div>
                <span class="text-xl font-bold text-gray-700">Wallet</span>
                <div class="text-xs text-gray-500 leading-tight">CURRENCY EXCHANGE</div>
            </div>
        </div>
    </div>

    <!-- 登入表單區域 -->
    <div class="flex-1 flex flex-col items-center justify-start px-6 py-4 mt-6">
        <div class="w-full max-w-sm">
            <!-- 標題 -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-blue-400 mb-1">{{ __('auth.login') }}</h1>
                <p class="text-gray-500 text-sm">Login</p>
            </div>

            <!-- 登入表單 -->
            <form wire:submit.prevent="login" class="space-y-5">
                <!-- 帳號輸入框 -->
                <div class="relative">
                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-4 py-4">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <input
                            type="text"
                            wire:model.defer="username"
                            placeholder="{{ __('auth.username_placeholder') }}"
                            class="flex-1 bg-transparent border-none outline-none text-gray-700 placeholder-gray-400 text-base webview-input"
                            autocomplete="username">
                    </div>
                </div>

                <!-- 密碼輸入框 -->
                <div class="relative">
                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-4 py-4">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-lock text-gray-600"></i>
                        </div>
                        <input
                            type="password"
                            wire:model.defer="password"
                            placeholder="{{ __('auth.password_placeholder') }}"
                            class="flex-1 bg-transparent border-none outline-none text-gray-700 placeholder-gray-400 text-base webview-input"
                            autocomplete="current-password">
                    </div>
                </div>

                <!-- 記住帳號密碼 - 置中 -->
                <div class="flex items-center justify-center">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="remember"
                            class="w-4 h-4 text-blue-400 border-2 border-gray-300 rounded focus:ring-blue-400 focus:ring-2">
                        <span class="ml-2 text-gray-600 text-sm">{{ __('auth.remember') }}</span>
                    </label>
                </div>

                <!-- 錯誤訊息 -->
                @error('username')
                <div class="text-red-500 text-sm text-center bg-red-50 p-3 rounded-lg">
                    {{ $message }}
                </div>
                @enderror

                <!-- 登入按鈕 -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="block mx-auto w-48 py-2.5 bg-blue-400 hover:bg-blue-500 active:bg-blue-600 text-white font-medium rounded-full transition-all duration-200 text-base shadow-sm webview-button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75">
                        <span wire:loading.remove>{{ __('auth.login') }}</span>
                        <span wire:loading class="flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            {{ __('auth.login_loading') }}
                        </span>
                    </button>
                </div>

                <!-- 忘記密碼連結 -->
                <div class="text-center pt-2">
                    <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors text-sm">
                        {{ __('auth.forgot_password') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>