<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{


    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'inscription', 'listesAccepter', 'listesNonAccepter', 'accepted', 'index']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Get a JWT via given credentials",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         )
     *     )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
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

    /**
     * @OA\Post(
     *     path="/inscription",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="telephone", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Inscription réussie"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */
    public function inscription(RegisterUserRequest $request)
    {
        try {
            $user = new User();
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

    /**
     * @OA\Post(
     *     path="/accepted/{id}",
     *     summary="Accept a user's candidature",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidature accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Vous avez accepté cette candidature"),
     *             @OA\Property(property="candidat", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="telephone", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="is_accepeted", type="string"),
     *                 @OA\Property(property="role", type="string")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */

    public function accepted($id)
    {
        try {
            $user = User::find($id);
            $user->is_accepeted = 'accepter';
            $user->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => "vous avez accepter cette candidature",
                'candidat' => $user
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/refuser/{id}",
     *     summary="Refuse a user's candidature",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidature refused successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Vous avez refusé cette candidature"),
     *             @OA\Property(property="candidat", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="telephone", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="is_accepeted", type="string"),
     *                 @OA\Property(property="role", type="string"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */

    public function refuser($id)
    {
        try {
            $user = User::find($id);
            $user->is_accepeted = 'refuser';
            $user->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => "vous avez refuser cette candidature",
                'candidat' => $user
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/listes-accepter",
     *     summary="Get the list of accepted candidates",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="List of accepted candidates",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Listes des candidats acceptés"),
     *             @OA\Property(property="listes_accepter", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="telephone", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="role", type="string"),
     *             )),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */

    public function listesAccepter()
    {
        try {
            $users = User::where('is_accepeted', 'accepter')->where('role', 'user')->get();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats acceptés",
                'listes_accepter' => $users,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/listes-non-accepter",
     *     summary="Get the list of rejected candidates",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="List of rejected candidates",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Listes des candidats rejetés"),
     *             @OA\Property(property="listes_non_accepter", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="telephone", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="is_accepeted", type="string"),
     *                 @OA\Property(property="role", type="string"),
     *             )),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */
    public function listesNonAccepter()
    {
        try {
            $user = User::where('is_accepeted', 'refuser')->where('role', 'user')->get();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats rejeter",
                'listes_non_accepter' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/index",
     *     summary="Get the list of candidates",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="List of candidates",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Listes des candidats"),
     *             @OA\Property(property="listes_users", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="telephone", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="is_accepeted", type="string"),
     *                 @OA\Property(property="role", type="string"),
     *             )),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            return response()->json([
                'status_code' => 200,
                'status_message' => "Listes des candidats",
                'listes_users' => User::where('role', 'user')->get(),
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}