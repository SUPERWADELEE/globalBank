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

                TextInput::make('current_password')
                    ->label(__('admin_user.current_password'))
                    ->password(),

                TextInput::make('new_password')
                    ->label(__('admin_user.new_password'))
                    ->password(),

                TextInput::make('new_password_confirmation')
                    ->label(__('admin_user.new_password_confirmation'))
                    ->password()
                    ->same('new_password'),

                Select::make('locale')
                    ->label(__('admin_user.locale'))
                    ->options(collect(LocaleEnum::cases())->mapWithKeys(fn($case) => [
                        $case->value => $case->label()
                    ])->toArray())
                    ->default(LocaleEnum::TraditionalChinese->value)
                    ->required(),
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
}
