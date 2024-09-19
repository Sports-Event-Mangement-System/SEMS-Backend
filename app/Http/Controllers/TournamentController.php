<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    public function index(): JsonResponse
    {
        $tournaments = Tournament::all();
        foreach ($tournaments as $tournament) {
            // Generate the full image URL
            $tournament->image_url = url('uploads/tournaments/' . $tournament->t_logo);
        }
        return response()->json([
            'tournaments' => $tournaments,
            'status' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTournamentRequest  $request
     */
    public function store(StoreTournamentRequest $request): JsonResponse
    {
        // Validate the request
        $validatedData = $request->validated();

        // Handle file upload
        $filename = null;
        if ($request->hasFile('t_logo')) {
            $file = $request->file('t_logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tournaments'), $filename);
        }

        // Create the tournament
        $tournament = Tournament::create([
            't_name' => $validatedData['t_name'],
            't_description' => $validatedData['t_description'],
            't_logo' => $filename,
            'prize_pool' => $validatedData['prize_pool'],
            'ts_date' => $validatedData['ts_date'],
            'te_date' => $validatedData['te_date'],
            'rs_date' => $validatedData['rs_date'],
            're_date' => $validatedData['re_date'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'team_number' => $validatedData['team_number'],
            'featured' => $validatedData['featured'],
        ]);

        return response()->json([
            'message' => 'Tournament created successfully',
            'tournament' => $tournament,
            'status' => true
        ]);
    }
    public function edit($id): JsonResponse
    {
        $tournament = Tournament::find($id);
        $tournament->image_url = url('uploads/tournaments/' . $tournament->t_logo);
        return response()->json([
            'tournament' => $tournament,
            'status' => true
        ]);
    }
    public function update(UpdateTournamentRequest $request, $id): JsonResponse
    {
        // Validate the request
        $validatedData = $request->validated();

        // Handle file upload
        $filename = null;
        if ($request->hasFile('t_logo')) {
            $file = $request->file('t_logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tournaments'), $filename);
        }

        // Update the tournament
        $tournament = Tournament::find($id);
        $tournament->update([
            't_name' => $validatedData['t_name'],
            't_description' => $validatedData['t_description'],
            't_logo' => $filename,
            'prize_pool' => $validatedData['prize_pool'],
            'ts_date' => $validatedData['ts_date'],
            'te_date' => $validatedData['te_date'],
            'rs_date' => $validatedData['rs_date'],
            're_date' => $validatedData['re_date'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'team_number' => $validatedData['team_number'],
            'featured' => $validatedData['featured'],
        ]);

        return response()->json([
            'message' => 'Tournament updated successfully',
            'tournament' => $tournament,
            'status' => true
        ]);
    }
    public function destroy($id): JsonResponse
    {
        $tournament = Tournament::find($id);
        $tournament->delete();
        return response()->json([
            'message' => 'Tournament deleted successfully',
            'status' => true
        ]);
    }


    public function updateStatus(Request $request, $id): JsonResponse
    {
        $tournament = Tournament::find($id);
        if( $tournament == null ) {
            return response()->json([
                'message' => 'Tournament not found',
                'status' => false
            ]);
        }
        $request->validate([
            'status' => 'required|boolean',
        ]);
        $tournament->update([
            'status' => $request->status
        ]);
        return response()->json([
            'message' => $tournament->t_name . ' status updated successfully',
            'status' => true
        ]);
    }
}
