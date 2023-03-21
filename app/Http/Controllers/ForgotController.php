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
                    ->select('users.password') -> first();

        return response()->json($consulta);

    }
}
