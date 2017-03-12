<?php

namespace App\Http\Controllers\API;

use App\Models\Genre;
use App\Models\Song;

class GenreController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $genres = Genre::with(['songs'])->get();

        // Calculate playCount for each album
        $genres->each(function($genre) {
            $genre['songCount'] = $genre->songs->count();
            unset($genre['songs']);
        });

        return response()->json([
            'genres' => $genres
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $genre = Genre::with(['songs'])->findOrFail($id);
        $genre['songCount'] = $genre->songs->count();
        unset($genre['songs']);

        return response()->json([
            'genre' => $genre
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function songs($id)
    {
        $songs = Song::with(['album', 'interactions', 'genre', 'album.artist', 'contributingArtist'])
            ->where('genre_id', '=', $id)
            ->orderBy('album_id', 'asc')
            ->orderBy('track', 'asc')
            ->get();

        return response()->json([
            'songs' => $songs
        ]);
    }
}
