<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTournamentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( StoreTournamentRequest $request )
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

}
