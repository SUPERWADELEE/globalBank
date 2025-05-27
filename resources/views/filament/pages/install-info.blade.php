<x-filament::page>
    <div class="max-w-7xl mx-auto w-full space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold">{{ __('install_info.app_download_info') }}</h2>
                <div class="flex items-center gap-6 mt-2">
                    <div class="text-center">
                        <img src="{{ asset('images/qr/ios.png') }}" class="w-32 h-32 mx-auto" alt="iPhone QR" />
                        <div class="mt-2">iPhone</div>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-ios-image')">{{ __('install_info.copy_download_image') }}</x-filament::button>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-ios-url')">{{ __('install_info.copy_download_url') }}</x-filament::button>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('images/qr/android.png') }}" class="w-32 h-32 mx-auto" alt="Android QR" />
                        <div class="mt-2">Android</div>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-android-image')">{{ __('install_info.copy_download_image') }}</x-filament::button>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-android-url')">{{ __('install_info.copy_download_url') }}</x-filament::button>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold">{{ __('install_info.frontend_url') }}</h2>
                <p class="mt-2 text-blue-600">https://www.kgsexchange.net/</p>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mt-6">{{ __('install_info.google_authenticator') }}</h2>
            <img src="{{ asset('images/qr/google-authenticator.png') }}" class="w-32 h-32 mt-2" alt="Google Auth QR" />
            <p class="mt-2">{{ __('install_info.google_authenticator_qr_code') }}</p>
            <x-filament::button onclick="navigator.clipboard.writeText('YOUR_QR_URL')">{{ __('install_info.copy_qr_code') }}</x-filament::button>
            <x-filament::button onclick="navigator.clipboard.writeText('YOUR_SECRET')">{{ __('install_info.copy_secret') }}</x-filament::button>

            <div class="mt-4">
                <p>{{ __('install_info.android_download_url') }}</p>
                <p class="text-sm text-gray-600">
                    https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=zh_TW&pli=1
                </p>
                <p class="mt-2">{{ __('install_info.ios_download_url') }}</p>
                <p class="text-sm text-gray-600">
                    https://apps.apple.com/tw/app/google-authenticator/id388497605
                </p>
            </div>
        </div>
    </div>
</x-filament::page>