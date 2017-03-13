<?php

namespace App\Http\Controllers\API;

use App\Models\Album;
use App\Models\Interaction;
use App\Models\Song;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $mostPlayedIds = Interaction::orderBy('play_count', 'desc')->take(5)->pluck('song_id');
        $recentlyPlayedIds = Interaction::orderBy('updated_at', 'desc')->take(5)->pluck('song_id');

        $mostPlayed = Song::with(['album', 'interactions', 'genre', 'album.artist', 'contributingArtist'])
            ->whereIn('id', $mostPlayedIds)
            ->orderByRaw('FIELD(id, "'.implode('","', $mostPlayedIds->toArray()).'")')
            ->get();

        $recentlyPlayed = Song::with(['album', 'interactions', 'genre', 'album.artist', 'contributingArtist'])
            ->whereIn('id', $recentlyPlayedIds)
            ->orderByRaw('FIELD(id, "'.implode('","', $recentlyPlayedIds->toArray()).'")')
            ->get();

        $lastAlbums = Album::with(['artist', 'songs', 'songs.interactions'])
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        // Calculate playCount for each album
        $lastAlbums->each(function($album) {
            $album['songCount'] = $album->songs->count();
            $album->calculatePlayCount();
            unset($album['songs']);
        });

        return response()->json([
            'top' => [
                'songs' => $mostPlayed,
                'albums' => [],
                'artists' => []
            ],
            'recentlyAdded' => [
                'albums' => $lastAlbums,
                'songs' => []
            ],
            'recentSongs' => $recentlyPlayed
        ]);
    }
}
