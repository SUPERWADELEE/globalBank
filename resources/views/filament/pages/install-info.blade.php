<x-filament::page>
    <div class="max-w-7xl mx-auto w-full space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold">APP下載資訊</h2>
                <div class="flex items-center gap-6 mt-2">
                    <div class="text-center">
                        <img src="{{ asset('images/qr/ios.png') }}" class="w-32 h-32 mx-auto" alt="iPhone QR" />
                        <div class="mt-2">iPhone</div>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-ios-image')">複製下載圖片</x-filament::button>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-ios-url')">複製下載網址</x-filament::button>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('images/qr/android.png') }}" class="w-32 h-32 mx-auto" alt="Android QR" />
                        <div class="mt-2">Android</div>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-android-image')">複製下載圖片</x-filament::button>
                        <x-filament::button onclick="navigator.clipboard.writeText('https://your-android-url')">複製下載網址</x-filament::button>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold">前台網址</h2>
                <p class="mt-2 text-blue-600">https://www.kgsexchange.net/</p>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mt-6">Google驗證下載與金鑰</h2>
            <img src="{{ asset('images/qr/google-authenticator.png') }}" class="w-32 h-32 mt-2" alt="Google Auth QR" />
            <p class="mt-2">Google驗證器 QR Code</p>
            <x-filament::button onclick="navigator.clipboard.writeText('YOUR_QR_URL')">複製QR Code</x-filament::button>
            <x-filament::button onclick="navigator.clipboard.writeText('YOUR_SECRET')">複製金鑰</x-filament::button>

            <div class="mt-4">
                <p>Android下載網址：</p>
                <p class="text-sm text-gray-600">
                    https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=zh_TW&pli=1
                </p>
                <p class="mt-2">iOS下載網址：</p>
                <p class="text-sm text-gray-600">
                    https://apps.apple.com/tw/app/google-authenticator/id388497605
                </p>
            </div>
        </div>
    </div>
</x-filament::page>