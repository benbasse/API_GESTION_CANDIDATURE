<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCandidatureRequest;
use App\Models\Candidature;
use App\Models\Formation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="API_CANDIDATURE_SIMPLON", version="0.1")
 * @OA\PathItem(path="/candidature")
 * @OA\Tag(
 *     name="Candidature",
 *     description="Endpoints pour la gerer les candidature."
 * )
 * 
 * @OA\Server(url="127.0.0.1:8001/")
 */

class CandidatureController extends Controller
{
    /**
 * @OA\Post(
 *     path="/candidater",
 *     summary="Submit a candidature for a formation",
 *     tags={"Candidature"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Candidature data",
 *         @OA\JsonContent(
 *             @OA\Property(property="formation_id", type="integer", description="ID of the selected formation"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Candidature submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Vous avez choisi cette formation"),
 *             @OA\Property(property="candidature", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="users_id", type="integer"),
 *                 @OA\Property(property="formation_id", type="integer"),
 *             ),
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Formation not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=404),
 *             @OA\Property(property="status_message", type="string", example="La formation spécifiée n'a pas été trouvée."),
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
    public function candidater(CreateCandidatureRequest $request)
    {
        try {
            $formation = Formation::findOrFail($request->formation_id);
            $candidature = new Candidature();
            $candidature->users_id = Auth::guard()->user()->id;
            $candidature->formation_id = $formation->id;
            $candidature->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Vous avez choisi cette formation",
                'candidature' => $candidature,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status_code' => 404,
                'status_message' => "La formation spécifiée n'a pas été trouvée.",
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
