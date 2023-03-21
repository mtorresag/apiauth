<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotController extends Controller
{
    public function forgot (ForgotRequest $request)
    {
        $email = $request->input('email');

        if (User::where('email', $email)->doesntExist()) {
            return response ([
                'message' => 'User doen\'t exist!'
            ], 404);
        }

        //$consulta = User::where ('email', $email) -> first();

        $consulta = User::where('email', $email)
                    ->value('password');

        $pass_real = openssl_decrypt($consulta, "AES-128-ECB", "someone");

        return response()->json($pass_real);

    }
}
