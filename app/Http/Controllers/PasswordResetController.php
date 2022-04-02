<?php

namespace App\Http\Controllers;

use App\Utils;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function create(Request $request){

        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email'],

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => "Reset Password Mail successfully sended"])
            : response()->json(['message' => "The email you entered is not valid"], 400);
    }

    public function find(Request $request){

        return response()->json($request->all(), 200);
    }

    public function reset(Request $request){

        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        //$userid = DB::table('users')->select('id')->where('email', $request->email)->first();
        //DB::table('oauth_access_tokens')->where('user_id', $userid->id)->update(['revoked' => 1]);

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => "Password successfully changed"])
            : response()->json(['message' => "Invalid token or input"],400);
    }
}
