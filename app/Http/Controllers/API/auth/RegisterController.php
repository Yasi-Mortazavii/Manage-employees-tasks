<?php

namespace App\Http\Controllers\API\auth;

use App\Http\Controllers\Controller;
use App\Mail\ActiveUser;
use App\Mail\UserSignup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;
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

            Mail::to($user->email)->send(new UserSignup($user));
            return response()->json([
                'massage' => 'user  has ben register successfuly !'
            ], 201);

        }

    }
      // User active with signup email
      public function signupActive($token)
      {

          $user = User::where('activation_token', $token)->first();
          // If user activation toke is not exist
          if (!$user) {
              return response()->json([
                  'message' => 'This activation token is invalid .'
              ], 404);

              // If user activation toke has Exist
          } else {

              $user->active = true;
              $user->email_verified_at = Carbon::now();
              $user->activation_token = "";
              $user->save();

              Mail::to($user->email)->send(new ActiveUser($user));

              return response()->json([
                  'message' => 'user has ben active'
              ], 200);
          }
      }
}
