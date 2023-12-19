<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFormationRequest;
use App\Http\Requests\EditFormationRequest;
use App\Models\Formation;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My First API", version="0.1")
 * @OA\PathItem(path="/Formation")
 * @OA\Tag(
 *     name="Formation",
 *     description="Endpoints pour les formations."
 * )
 * 
 * @OA\Server(url="127.0.0.1:8001/")
 */
class FormationController extends Controller
{
/**
 * @OA\Get(
 *     path="/index",
 *     summary="Get the list of formations",
 *     tags={"Formation"},
 *     @OA\Response(
 *         response=200,
 *         description="List of formations",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Listes des formations"),
 *             @OA\Property(property="formations", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="libelle", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="duree", type="integer"),
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
                'status_message' => "listes des formations",
                'formations' => Formation::all(),
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/store",
     *     summary="Create a new formation",
     *     tags={"Formation"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Formation data",
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string", description="Libelle of the formation"),
     *             @OA\Property(property="description", type="string", description="Description of the formation"),
     *             @OA\Property(property="duree", type="integer", description="Duration of the formation in hours"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Formation enregistrée"),
     *             @OA\Property(property="formations", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="libelle", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="duree", type="integer"),
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
    public function store(CreateFormationRequest $request)
    {
        try {
            $formation = new Formation();
            $formation->libelle = $request->libelle;
            $formation->description = $request->description;
            $formation->duree = $request->duree;
            $formation->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Formation enregistrer",
                'formations' => $formation,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/update/{id}",
     *     summary="Update an existing formation",
     *     tags={"Formation"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the formation to be updated",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated formation data",
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string", description="Updated libelle of the formation"),
     *             @OA\Property(property="description", type="string", description="Updated description of the formation"),
     *             @OA\Property(property="duree", type="integer", description="Updated duration of the formation in hours"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Formation mise à jour"),
     *             @OA\Property(property="formations", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="libelle", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="duree", type="integer"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Formation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=404),
     *             @OA\Property(property="status_message", type="string", example="Formation not found"),
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

    public function update(EditFormationRequest $request, $id)
    {
        try {
            $formation = Formation::find($id);
            $formation->libelle = $request->libelle;
            $formation->description = $request->description;
            $formation->duree = $request->duree;
            $formation->save();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Formation mise à jour",
                'formations' => $formation,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/destroy/{id}",
     *     summary="Delete a formation",
     *     tags={"Formation"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the formation to be deleted",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formation deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="Formation supprimée"),
     *             @OA\Property(property="formations", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="libelle", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="duree", type="integer"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Formation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=404),
     *             @OA\Property(property="status_message", type="string", example="Formation not found"),
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

    public function destroy($id)
    {
        try {
            $formation = Formation::find($id);
            $formation->delete();
            return response()->json([
                'status_code' => 200,
                'status_message' => "Formation supprimée",
                'formations' => $formation,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

}
