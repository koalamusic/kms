<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Interaction;
use App\Models\Song;

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
            $album['songCount'] = $album->songs->count();
            $album->calculatePlayCount();
            unset($album['songs']);
        });

        return response()->json([
            'albums' => $albums
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $album = Album::with(['artist', 'songs', 'songs.interactions'])->findOrFail($id);
        $album->calculatePlayCount();
        unset($album['songs']);

        return response()->json([
            'album' => $album
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function songs($id)
    {
        $songs = Song::with(['album', 'interactions', 'genre', 'album.artist', 'contributingArtist'])
            ->where('album_id', '=', $id)
            ->orderBy('album_id', 'asc')
            ->orderBy('track', 'asc')
            ->get();

        return response()->json([
            'songs' => $songs
        ]);
    }
}
