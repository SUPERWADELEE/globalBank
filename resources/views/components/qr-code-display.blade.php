<div class="flex flex-col items-center gap-4 p-4">
    <div class="text-lg font-semibold">{{ $email }}</div>
    <img src="{{ $qrCode }}" style="max-width: 250px; border: 1px solid #e5e7eb; border-radius: 8px;" alt="TOTP QR Code">
    <div class="text-sm text-gray-600">
        <div class="font-medium">Secret Key:</div>
        <div class="font-mono text-xs bg-gray-100 p-2 rounded mt-1 break-all">{{ $secret }}</div>
    </div>
    <div class="text-xs text-gray-500 text-center max-w-sm">
        {{ __('user.qr_code_description') }}
    </div>
</div> 