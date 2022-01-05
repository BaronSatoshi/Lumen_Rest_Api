<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

//Controller de autencticação, verifica se o usuário é autenticado ou se é necessário se registrar e libera o token de acesso

class AuthController extends Controller
{
    public function register(Request $request){
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        //Verifica se o nome, email e a senha está preenchida
        if(empty($name) or empty($email) or empty($password)){
            return response()->json(['status' => 'error', 'message' => 'You must fill all the fields']);
        }

        //Verifica se é um email valido
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return response()->json(['status' => 'error', 'message' => 'You must enter a valid email']);
        }

        //Verifica se a senha é menor do que 6 caracteres
        if(strlen($password) < 6){
            return response()->json(['status' => 'error', 'message' => 'Password should be min 6 character']);
        }

        //Faz um verificação se o email já existe
        if(User::where('email', '=', $email)->exists()){
            return response()->json(['status' => 'error', 'message' => 'User already exists with this email']);
        }

        try{
            //Se tudo estiver correto, irá ser criado um novo usuário
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = app('hash')->make($request->password);

            if($user->save()){
                return $this->login($request);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function login(Request $request){ //Request $email, Request $password
        $email = $request->email;
        $password = $request->password;

        //check if field is empty
        if(empty($email) or empty($password)){
            return response()->json(['status' => 'error', 'message' => 'You must fill all the fields']);
        }

        $credentials = request(['email', 'password']);

        if(! $token = auth()->attempt($credentials)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //Se o login ocorrer com sucesso irá retornar um novo token
        return $this->respondWithToken($token);
    }

    public function logout(){
        auth()->logout();
        
        return response()->json(['message' => 'Sucessfully logged out']);
    }

    //Função que retorna o token
    protected function respondWithToken($token){
        return response()->json([
            'acess_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


}
