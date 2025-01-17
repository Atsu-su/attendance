<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'family_name' => 'required|string',
            'given_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'family_name.required' => '姓名を入力してください',
            'family_name.string' => '姓名は文字列で入力してください',
            'given_name.required' => '姓名を入力してください',
            'given_name.string' => '姓名は文字列で入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'password.required' => 'パスワードを入力してください',
            'password.string' => 'パスワードは文字列で入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'confirm_password.required' => '確認用パスワードを入力してください',
            'confirm_password.same' => 'パスワードと一致しません'
        ];
    }
}
