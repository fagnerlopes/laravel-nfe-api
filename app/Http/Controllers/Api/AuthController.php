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
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use ApiResponser, HasApiTokens;

    /**
     * Realiza o registro de um usuário retornando o token de acesso para autenticação
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            ]);

        } catch (\Exception $e) {
            return $this->error([
                'error_message' => $e->getMessage(),
            ]);
        }

    }

    /**
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        $payload = $request->all();
        // loga e verifica se foi bem sucedida
        if (!Auth::attempt($payload))
        {
            return $this->error([
                'error_message' => 'ops!! O login falhou. Verifique o usuário e senha.',
            ]);
        }

        auth()->user()->tokens()->delete();

        /*
        Implementar autenticação por nível de acesso [ MasterAdministrator, Administrator, User]
        */

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->success([
            'success_message' => 'Token revogado. Faça login novamente para gerar um novo token',
        ]);
    }
}

