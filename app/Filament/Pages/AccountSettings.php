<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use App\Enums\LocaleEnum;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Closure;

class AccountSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.account-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin_user.account_settings');
    }


    public function getTitle(): string
    {
        return __('admin_user.account_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'job_title' => Auth::user()->job_title,
        ]);
    }


    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema($this->getFormSchema());
    }



    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label(__('admin_user.name'))
                    ->disabled(),


                TextInput::make('email')
                    ->label(__('admin_user.email'))
                    ->disabled(),

                TextInput::make('job_title')
                    ->label(__('admin_user.job_title'))
                    ->disabled(),

                // 舊密碼檢查，看是不是舊的密碼資料
                TextInput::make('password')
                    ->label(__('admin_user.current_password'))
                    ->password()
                    ->maxLength(255)
                    ->required()
                    ->rule(function () {
                        return function (string $attribute, $value, Closure $fail) {
                            $user = Auth::user();

                            if (! Hash::check($value, $user->password)) {
                                $fail(__('admin_user.current_password_incorrect'));
                            }
                        };
                    }),


                TextInput::make('new_password')
                    ->label(__('admin_user.new_password'))
                    ->password()
                    ->password()
                    // 建立時必填；編輯時可空白（表示不變更密碼）
                    ->revealable()
                    // 右側按鈕：產生隨機密碼
                    ->suffixAction(
                        Action::make('generatePassword')
                            ->tooltip(__('admin_user.random_password'))      // 滑鼠提示
                            ->icon('heroicon-o-sparkles')              // 圖示可換
                            ->color('secondary')                       // 按鈕顏色
                            ->action(
                                fn(Set $set) =>
                                $set('new_password', Str::random(12))      // 寫回欄位
                            )
                    )
                    ->default(Str::random(12))
                    ->maxLength(255)
                    ->required()
                    ->disabled(fn() => !Auth::user()->can('edit_account_settings')),

                TextInput::make('new_password_confirmation')
                    ->label(__('admin_user.new_password_confirmation'))
                    ->password()
                    ->disabled(fn() => !Auth::user()->can('edit_account_settings'))
                    ->same('new_password'),

                Select::make('locale')
                    ->label(__('admin_user.locale'))
                    ->options(collect(LocaleEnum::cases())->mapWithKeys(fn($case) => [
                        $case->value => $case->label()
                    ])->toArray())
                    ->default(LocaleEnum::TraditionalChinese->value)
                    ->required()
                    ->disabled(fn() => !Auth::user()->can('edit_account_settings')),
            ])
        ];
    }

    public function submit(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        $user->locale = $data['locale'];
        if (!empty($data['new_password'])) {
            $user->password = Hash::make($data['new_password']);
        }
        $user->save();

        Notification::make()
            ->title(__('admin_user.account_settings_updated'))
            ->success()
            ->send();
    }
    public static function canAccess(): bool
    {
        return Auth::user()->can('view_account_settings');
    }
}
