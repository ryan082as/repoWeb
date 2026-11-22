<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $presence = $isUpdate ? 'sometimes' : 'required';
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name' => [$presence, 'string', 'max:255'],
            'email' => [
                $presence,
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['sometimes', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }
}
