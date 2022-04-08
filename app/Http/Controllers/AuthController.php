<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\SignupActivate;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    public function signup(Request $request){

        $validator = Validator::make($request->all(), [

            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed']

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_attribute = $request->all();
        $user_attribute['password'] = bcrypt($request->password);
        $user_attribute['activation_token'] = md5(uniqid());
        $user = User::create($user_attribute);
        $role_attribute = [
            'user_id' => $user['id'],
            'role' => 'basic'
        ];
        Role::create($role_attribute);
        $user->notify(new SignupActivate());

        return response()->json($user);
    }

    public function signupByAdmin(Request $request){

        $validator = Validator::make($request->all(), [

            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'basic'])]

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_attribute = $request->all();
        $user_attribute['password'] = bcrypt($request->password);
        $user_attribute['activation_token'] = md5(uniqid());
        $user = User::create($user_attribute);
        $role_attribute = [
            'user_id' => $user['id'],
            'role' => $request->role
        ];
        Role::create($role_attribute);
        $user->notify(new SignupActivate());

        return response()->json($user);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember_me' => ['boolean']

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $userRole = $user->role()->first();

        if ($userRole) { $this->scope = $userRole->role; }else{ return response()->json(['message' => 'The user do not have a role']); }

        $tokenResult = $user->createToken('Personal access Token', [$this->scope]);
        $token = $tokenResult->token;

        if($request->query('remember_me')){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response()->json([

            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()

        ]);
    }

    public function logout(Request $request){

        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request){

        $user = $request->user();

        return response()->json($user);
    }

    public function signupActivate($token){

        $user = User::where('activation_token', $token)->first();

        if(!$user){
            return response()->json(['is_active' => 0, 'message' => "Invalid token"],400);
        }elseif($user->email_verified_at !== null){
            return response()->json(['is_active' => 1, 'message' => "Invalid token"],400);
        }

        $user->active= true;
        $user->email_verified_at = date( 'Y-m-d H:i:s' );
        $user->update();

        return response()->json($user);
    }

    public function destroyUser(User $user){
        $isUser = Auth::id()==$user['id'];
        if($isUser) {
            $user->token->revoke();
            $user->role()->delete();
            $user->delete();
            return response()->json(['message' => 'User successfully deleted'], 204);
        }
        return response()->json(['message' => 'You can delete only your profile'], 400);
    }

}
