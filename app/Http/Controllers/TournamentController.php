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
    public function store(StoreTournamentRequest $request)
    {
        // Handle file upload
        $filename = null;
        if ($request->hasFile('t_logo')) {
            $file = $request->file('t_logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tournaments'), $filename);
        }

        // Create the tournament
        $tournament = Tournament::create([
            't_name' => $request->t_name,
            't_description' => $request->t_description,
            't_logo' => $filename,
            't_type' => $request->t_type,
            'ts_date' => $request->ts_date,
            'te_date' => $request->te_date,
            'rs_date' => $request->rs_date,
            're_date' => $request->re_date,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address' => $request->address,
            'status' => $request->status,
            'team_number' => $request->team_number,
            'featured' => $request->featured,
        ]);

        return response()->json([
            'message' => 'Tournament created successfully',
            'tournament' => $tournament,
            'status' => true
        ]);
    }
}
