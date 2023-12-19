<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller 
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','inscription', 'listesAccepter', 'listesNonAccepter', 'accepted', 'index']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public function inscription(RegisterUserRequest $request)
    {
        try {
            $user= new User();
            $user->nom = $request->nom;
            $user->telephone = $request->telephone;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => 'inscription reussi'
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function accepted($id)
    {
        try {
            $user = User::find($id);
            $user->is_accepeted = 1;
            $user->save();
            return response()->json([
            'status_code' => 200,
            'status_message'=>"vous avez accepter cette candidature",
            'candidat'=> $user
        ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function listesAccepter()
    {
        try {
            $users = User::where('is_accepeted', 1)->where('role', 'user')->get();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats acceptés",
                'listes_accepter' => $users,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function listesNonAccepter()
    {
        try {
            $user = User::where('is_accepeted', 0)->where('role', 'user')->get();

            if ($user = isEmpty()) {
                return response()->json([
                    'status_code' => 200,
                    'status_message' => "Il n'ya pas de candidature rejeter",
                ]);
            }
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats rejeter",
                'listes_non_accepter' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function index()
    {
        try {
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats pas encors accepter",
                'listes_users' => User::where('role', 'user')->get(),
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}