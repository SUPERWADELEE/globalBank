<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Checkbox;
use Illuminate\Validation\ValidationException;
use Filament\Pages\Auth\Login as BaseAuth;

class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('admin_user.name'))
            ->rules(['required'])
            ->markAsRequired()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('admin_user.password'))
            ->rules(['required'])
            ->markAsRequired()
            ->autocomplete()
            ->password();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('admin_user.remember_me'))
            ->default(true);
    }

    /**
     * 覆寫認證用的 credential 欄位
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'name' => $data['name'],
            'password' => $data['password'],
        ];
    }

    /**
     * 覆寫失敗時的錯誤訊息（帳號/密碼都給同一訊息，防止資訊洩漏）
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.name' => __('admin_user.username_is_incorrect'),
            'data.password' => __('admin_user.password_is_incorrect'),
        ]);
    }
}
