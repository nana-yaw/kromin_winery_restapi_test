<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function changeRole(Request $request, String $user_id){

        $validator = Validator::make($request->all(), [

            'role' => ['required', Rule::in(['admin', 'basic'])],

        ]);

        if ($validator->fails()) { return response()->json($validator->errors(), 400); }

        if($role = Role::where('user_id', '=', $user_id)->first()){
            DB::table('roles')->where('user_id','=', $user_id)->update(['role' => $request->role]);
            DB::table('oauth_access_tokens')->where('user_id', $user_id)->update(['revoked' => 1]);

            return response()->json(['message' => "User's role successfully changed"]);
        }
        return response()->json(['message' => "Invalid input"], 400);
    }

    public function getRole(String $user_id){

        if($role = Role::where('user_id', '=', $user_id)->first()){
            return response()->json($role);
        }
        return response()->json(['message' => "Invalid input"], 400);

    }
}
