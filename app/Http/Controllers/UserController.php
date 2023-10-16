<?php

namespace App\Http\Controllers;

use App\Mail\ValidationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ], 401);
        }

        //attempt
        if (Auth::attempt($request->only('email', 'password'))) {
            //check status 
            if ($user->status == 0) {
                return response()->json([
                    'message' => 'User not verified'
                ], 401);
            }
            //generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            //return response
            return response()->json([
                'message' => 'User logged in',
                'token' => $token
            ], 200);
        }
        else {
            return response()->json([
                'message' => 'Wrong password'
            ], 401);
        }
    }

    //valider son compte
    public function valideUncompte(User $user, Request $request)
    {
        if ($user->code == $request->code) {
            $user->update([
                'code' => 00000,
                'status' => 1,
                'email_verified_at' => now()
            ]);

            //Ajoute aussi le code de auto login et aussi un redirect vers la page d'accueil pour tes app bien sur
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Compte validé'
            ], 200);
        }
        return response()->json([
            'message' => "Code invalide"
        ], 400);
    }



    //registration
    public function registration(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //check if user exists
        if (User::where('email', $request->email)->first()) {
            return response()->json([
                'message' => 'User exists'
            ], 200);
        }
        //generate 5 random integer
        $token = rand(10000, 99999);
        //create user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'code' => $token
        ]);
        //send email
        Mail::to($request->email)->send(new ValidationEmail($token, $request->name));

        //return response
        return response()->json([
            'message' => 'User created'
        ], 201);
    }
}
