<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

// 4|JCiZQhrg2xmVzRFUaK7HmpwiGaRHIWuZhN4adf6U -> invoice

// 5|pwkqkkRZX8zN4ZhzsWOAPbySD8QKDNZvXzGSa9Iv -> user

//  6|v5fFwTTeoFkz4vPidXrTf3zRFc5orIvLui44VyWe -> teste

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $request){
        if(Auth::attempt($request->only('email', 'password'))){
            return $this->response('Authorized', 200, [
                'token' => $request->user()->createToken('invoice', ['teste-index'])->plainTextToken,
            ]);
        }
        else{
            return $this->response('Not Authorized', 403);
        }
        
    }

    public function logout(){

    }
}
