<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCandidatureRequest;
use App\Models\Candidature;
use App\Models\Formation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
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
