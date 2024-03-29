<?php

namespace App\Http\Controllers;

use App\Mail\ValidationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function gitHandleLogin()
    {
        return Socialite::driver('github')->redirect();
    }

    public function gitCallback(Request $request)
    {
        // dd("{fhskjhfklsdjfklsdjfkl");
        //On ajoute le stateless  ca marchera pas si on l'enleve par contre avec google ca marche de ouf
        // dd(Socialite::driver('github')->stateless()->user());
        try {
            $user = Socialite::driver('github')->stateless()->user();
            $user = User::firstOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->nickname,
                    'email' => $user->email,
                    'password' => bcrypt('password'),
                    'status' => 1,
                    'code' => rand(10000, 99999),
                    'email_verified_at' => now()
                ]
            );
            //login automatically
            Auth::login($user);
            //generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'User logged',
                'token' => $token,
                'name' => $user->name
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $user = User::firstOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => bcrypt('password'),
                    'status' => 1,
                    'code' => rand(10000, 99999),
                    'email_verified_at' => now()
                ]
            );
            //login automatically
            Auth::login($user);
            //generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'User logged',
                'token' => $token,
                'name' => $user->name
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function handleGoogleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function loginGoogleForm()
    {
        return view("auth.login");
    }

    public function check($token)
    {
        $user = User::where('password_reset_token', $token)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ], 401);
        }
        return response()->json([
            'message' => 'User exist'
        ], 200);
    }

    //traitement de la reinitialisation du mot de passe
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required'
        ]);

        //check if user exists
        $user = User::where('password_reset_token', $request->token)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ], 401);
        }

        //update user
        $user->update([
            'password' => bcrypt($request->password),
            'password_reset_token' => null,
        ]);

        return response()->json(
            [
                'message' => 'Mot de passe réinitialisé avec succès'
            ],
            200
        );
    }

    //mot de pass oublie 
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ], 401);
        }
        //generate  random string token
        $randomString = bin2hex(random_bytes(16));

        //update user
        $user->update([
            'password_reset_token' => $randomString,
        ]);

        Mail::send("mails.forgot", [
            'token' => $randomString,
            'user' => $user,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Réinitialisation de votre mot de passe');
        });

        // tu dois creer la vue mails.forgot dans le dossier resources/views/mails/forgot.blade.php 
        // et  lors de la soumissions de la  tu dois utuliser le controller (resetPassword) ci dessus
        // en recuperant ce le token dans l'url
        // n'oublie  pas de modifier l'url dans le forgot qui se rediriger vers un pages avec la formulaire de reset
        return response()->json(
            [
                'message' => 'Un lien de réinitialisation de mot de passe vous a été envoyé par email.'
            ],
            200
        );
    }

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
        } else {
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
                'code' => rand(10000, 99999),
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
        Mail::send("mails.validation", [
            'token' => $token,
            'user' => $request,
            'name' => $request->name
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Réinitialisation de votre mot de passe');
        });

        //return response
        return response()->json([
            'message' => 'User created'
        ], 201);
    }
}
