@if(auth()->check() && auth()->user()->can('view_account_settings'))
<a
    href="{{ route('filament.admin.pages.account-settings') }}"
    class="fi-topbar-item ml-2 text-gray-400 hover:text-primary-500 transition"
    title="帳號設定">
    <x-heroicon-o-cog class="w-5 h-5" />
</a>
@endif
