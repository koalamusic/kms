<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Interaction;

class AlbumController extends Controller
{
    /**
     * Get extra information about an album via Last.fm.
     *
     * @param Album $album
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo(Album $album)
    {
        return response()->json($album->getInfo());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $albums = Album::with(['artist', 'songs', 'songs.interactions'])->get();

        // Calculate playCount for each album
        $albums->each(function($album) {
            $album->calculatePlayCount();
        });

        return response()->json([
            'albums' => $albums
        ]);
    }

    public function show($id)
    {
        $album = Album::with(['artist', 'songs', 'songs.interactions'])->findOrFail($id);
        $album->calculatePlayCount();

        return response()->json([
            'album' => $album
        ]);
    }
}
