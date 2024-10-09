<?php

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    public function index(): JsonResponse
    {
        $tournaments = Tournament::with('teams')->get();
        foreach ($tournaments as $tournament) {
            $tournament->image_urls = ImageHelper::generateImageUrls($tournament->t_images);
        }
        return response()->json([
            'tournaments' => $tournaments,
            'status' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTournamentRequest  $request
     * @return JsonResponse
     */
    public function store(StoreTournamentRequest $request): JsonResponse
    {
        // Validate the request
        $validatedData = $request->validated();
        // Handle file uploads
        $filenames = [];
        if ($request->hasFile('t_images')) {
            foreach ($request->file('t_images') as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/tournaments'), $filename);
                $filenames[] = $filename;
            }
        }

        // Create the tournament
        $tournament = Tournament::create([
            't_name' => $validatedData['t_name'],
            't_description' => $validatedData['t_description'],
            't_images' => $filenames,
            'prize_pool' => $validatedData['prize_pool'],
            'ts_date' => $validatedData['ts_date'],
            'te_date' => $validatedData['te_date'],
            'rs_date' => $validatedData['rs_date'],
            're_date' => $validatedData['re_date'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'tournament_type' => $validatedData['tournament_type'],
            'max_teams' => $validatedData['max_teams'],
            'min_teams' => $validatedData['min_teams'],
            'max_players_per_team' => $validatedData['max_players_per_team'],
            'min_players_per_team' => $validatedData['min_players_per_team'],
            'featured' => $validatedData['featured'],
        ]);

        return response()->json([
            'message' => 'Tournament created successfully',
            'tournament' => $tournament,
            'status' => true,
        ]);
    }

    public function edit($id): JsonResponse
    {
        $tournament = Tournament::find($id);

        // Generate image URLs using the helper method
        $tournament->image_urls = ImageHelper::generateImageUrls($tournament->t_images ?? '');

        return response()->json([
            'tournament' => $tournament,
            'status' => true,
        ]);
    }

    public function update(UpdateTournamentRequest $request, $id): JsonResponse
    {
        $validatedData = $request->validated();
        $tournament = Tournament::find($id);

        $existingImages = is_string($tournament->t_images ?? '') ? json_decode($tournament->t_images,
            true) : $tournament->t_images;

        $existingImages = $existingImages ?? [];

        $filenames = [];

        if ($request->has('existing_images')) {
            $requestedExistingImages = $request->input('existing_images');

            // Extract base filenames from the existing images URLs
            $requestedFilenames = array_map(function ($url) {
                return basename(parse_url($url, PHP_URL_PATH));
            }, $requestedExistingImages);

            // Set the filenames to the requested existing images
            $filenames = array_merge($filenames, $requestedFilenames);
        }

        // Handle new image uploads
        if ($request->hasFile('t_images')) {
            $newImages = $request->file('t_images');

            foreach ($newImages as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/tournaments'), $filename);
                $filenames[] = $filename;
            }
        }
        $filenames = array_unique($filenames);

        if (empty($filenames)) {
            $filenames = [];
        }

        // Update the tournament
        $tournament->update([
            't_name' => $validatedData['t_name'],
            't_description' => $validatedData['t_description'],
            't_images' => $filenames,
            'prize_pool' => $validatedData['prize_pool'],
            'ts_date' => $validatedData['ts_date'],
            'te_date' => $validatedData['te_date'],
            'rs_date' => $validatedData['rs_date'],
            're_date' => $validatedData['re_date'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'tournament_type' => $validatedData['tournament_type'],
            'max_teams' => $validatedData['max_teams'],
            'min_teams' => $validatedData['min_teams'],
            'max_players_per_team' => $validatedData['max_players_per_team'],
            'min_players_per_team' => $validatedData['min_players_per_team'],
            'featured' => $validatedData['featured'],
        ]);

        return response()->json([
            'message' => 'Tournament updated successfully',
            'tournament' => $tournament,
            'status' => true,
        ]);
    }


    public function destroy($id): JsonResponse
    {
        $tournament = Tournament::find($id);
        $tournament->delete();
        return response()->json([
            'message' => 'Tournament deleted successfully',
            'status' => true,
        ]);
    }


    public function updateStatus(Request $request, $id): JsonResponse
    {
        $tournament = Tournament::find($id);
        if ($tournament === null) {
            return response()->json([
                'message' => 'Tournament not found',
                'status' => false,
            ]);
        }
        $request->validate([
            'status' => 'required|boolean',
        ]);
        $tournament->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => $tournament->t_name.' status updated successfully',
            'status' => true,
        ]);
    }

    /**
     * Display the specified resource.
     * This function is used for to fetch specific tournaments data for users
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return response()->json([
                'status' => false,
                'message' => 'Tournament not found.',
            ], 404);
        }

        $teams = Team::where('tournament_id', $id)->get();
        if ($teams->count() > 0) {
            foreach ($teams as $team) {
                $team->logo_url = url('uploads/teams/'.$team->team_logo);
            }
            $tournament->teams = $teams->where('status', 1);
            $tournament->team_register = $teams->count();
            $team_confirmed = $teams->where('status', 1)->count();
            $tournament->team_confirmed = $team_confirmed;
            $tournament->slot_left = $tournament->max_teams - $team_confirmed;
        }
        // Generate image URLs using the helper method
        $tournament->image_urls = ImageHelper::generateImageUrls($tournament->t_images ?? '');

        return response()->json([
            'tournament' => $tournament,
            'status' => true,
        ]);
    }

    /**
     * Display the specified active tournaments.
     *
     * @return JsonResponse
     */
    public function activeTournaments(): JsonResponse
    {
        $tournaments = Tournament::where('status', 1)->get();

        // Generate image URLs using the helper method
        foreach ($tournaments as $tournament) {
            $tournament->image_urls = ImageHelper::generateImageUrls($tournament->t_images);
        }

        return response()->json([
            'tournaments' => $tournaments,
            'status' => true,
        ]);
    }
}
