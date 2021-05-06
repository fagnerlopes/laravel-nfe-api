<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'cnpj_emitente' => 'required|string|size:14'
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'Os campos nome, email e senha são obrigatórios',
            'email.email' => 'O email informado é inválido',
            'email.unique' => 'O email informado já está em uso',
            'password.min' => 'A senha deve conter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas fornecidas não são idênticas',
            'cnpj_emitente.size' => 'O CNPJ informado não possui 14 caracteres'
        ];
    }
}
