<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use phpseclib\Crypt\TripleDES;
use App\Util\AgecoldexCrypter;

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

        //$pass_real = openssl_decrypt($consulta, "AES-128-ECB", "someone");
        //$pass_real = $this-> Decrypt($consulta, 'web_novo_agesoft');
        $pass_real = AgecoldexCrypter::FastDecrypt($consulta);

        return response()->json($pass_real);

    }


    // public function Decrypt( $data, $hash ) {
    //     $key        = md5($hash, true);
    //     $key        .= substr($key, 0, 8);

    //     $data = base64_decode($data);
    //     $decData = openssl_decrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

    //     return $decData;
    // }
}
