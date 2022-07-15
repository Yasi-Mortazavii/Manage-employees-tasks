<?php

namespace App\Http\Controllers\API\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => ['required', 'string'] ,
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255', 'unique' ],
            'password' => ['required', 'confirmed', 'min:8']
        ]);

        //If Validator Has Ben Faild
        if($validate->failed()){
            return  response()->json([
                'massage'  => $validate->errors()->first()
            ], 500);

        //If Validator Is Ok
        }else{

            $user = new user([

                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'activation_token' => str::random(60),
                'register_ip' => $request->ip()
            ]);
            $user->save();
            return response()->json([
                'massage' => 'user  has ben register successfuly !'
            ], 201);
        }

    }
}
