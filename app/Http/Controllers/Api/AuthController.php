<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\Emitente;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(RegisterUserRequest $request)
    {
        try {
            $payload = $request->all();

            $request->validated();


            $payload['password'] = Hash::make($payload['password']);
            $emitente = Emitente::where('cnpj', $payload['cnpj_emitente'] )->get();

            if(!sizeof($emitente) === 1){
                throw new \Exception('Falha ao estabelecer relação com o emitente. Entre em contato com o suporte.');
            }

            $user = User::create([
                'emitente_id' => $emitente[0]->id,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => $payload['password']
            ]);

            return $this->success([
                'token' => $user->createToken('API Token')->plainTextToken
            ], 'Token gerado com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }

    }

    public function login(UserLoginRequest $request)
    {
        $payload = $request->all();

        if (!Auth::attempt($payload))
        {
            return $this->error('ops!! O login falhou. Verifique o usuário e senha.', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Token revogado'
        ];
    }
}

