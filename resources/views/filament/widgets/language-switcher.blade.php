<div class="flex items-center gap-2">
    <form method="POST" action="{{ route('language.switch.post') }}">
        @csrf
        <select name="locale" onchange="this.form.submit()" class="text-sm border-gray-300 rounded">
            <option value="zh_TW" @selected(app()->getLocale() === 'zh_TW')>繁體中文</option>
            <option value="en" @selected(app()->getLocale() === 'en')>English</option>
            <option value="ja" @selected(app()->getLocale() === 'ja')>日本語</option>
        </select>
    </form>
</div>