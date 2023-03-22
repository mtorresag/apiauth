<?php
namespace App\Http\Controllers;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SignupActivate;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Util\AgecoldexCrypter;


class AuthController extends Controller
{
    // public function encrypt($key, $stringToEncrypt)
    // {
    //     //$GStrError = '';
    //     $password = $key;
    //     $cipher = 'des-ede3';
    //     $mode = 'ECB';
    //     try {
    //         $key = md5($password, true);
    //         //$iv = null;
    //         $encrypted = openssl_encrypt($stringToEncrypt, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    //         $encrypted = base64_encode($encrypted);
    //     } catch (\Exception $e) {
    //         //$encrypted = '';
    //         //$GStrError = "[Encrypt]: No se puede Encryptar." . $e->getMessage();
    //     }
    //     return $encrypted;
    // }

    // public function Encrypt( $data, $hash ) {
    //     $key        = md5($hash, true);
    //     $key        .= substr($key, 0, 8);
    //     $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
    //     $encData = base64_encode($encData);

    //     return $encData;
    // }

    
    public function signup(Request $request)
    {    
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|string|email|unique:users',
            'password'  => 'required|string|confirmed',
        ]);
        $user = new User([
            'name'              => $request->name,
            'email'             => $request->email,
            //'password'          => bcrypt($request->password),
            //'password'          => openssl_encrypt ( $request->password , "AES-128-ECB" , "someone"),
            //'password'          => $this->encrypt($request->password, 'web_novo_agesoft'),
            'password'          => AgecoldexCrypter::FastEncrypt($request->password),
            'activation_token'  => str_random(60),
        ]);
        $user->save();
        //$user->notify(new SignupActivate($user));
        
        return response()->json(['message' => 'Usuario creado existosamente!', $request->password], 201);
    }

public function login(Request $request)
{
    $request->validate([
        'email'       => 'required|string|email',
        'password'    => 'required|string',
        'remember_me' => 'boolean',
    ]);
    $credentials = request(['email', 'password']);
    $credentials['active'] = 1;
    $credentials['deleted_at'] = null;
    
    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'No Autorizado'], 401);
    }
    $user = $request->user();
    $tokenResult = $user->createToken('Token Acceso Personal');
    $token = $tokenResult->token;
    if ($request->remember_me) {
        $token->expires_at = Carbon::now()->addWeeks(1);
    }
    $token->save();
    return response()->json([
        'access_token' => $tokenResult->accessToken,
        'token_type'   => 'Bearer',
        'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 
            'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function signupActivate($token)
{
    $user = User::where('activation_token', $token)->first();
    if (!$user) {
        return response()->json(['message' => 'El token de activación es inválido'], 404);
    }
    $user->active = true;
    $user->activation_token = '';
    $user->save();
    return $user;
}
}