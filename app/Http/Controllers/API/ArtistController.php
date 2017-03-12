<?php

namespace App\Http\Controllers\API;

use App\Models\Artist;

class ArtistController extends Controller
{
    /**
     * Get extra information about an artist via Last.fm.
     *
     * @param Artist $artist
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo(Artist $artist)
    {
        return response()->json($artist->getInfo());
    }

    public function index()
    {
        $artists = Artist::with(['albums', 'albums.songs', 'albums.songs.interactions'])->has('albums')->get();

        // Calculate playCount for each album
        $artists->each(function($artist) {
            $artist->albums->each(function ($album) use ($artist) {
                $playCount = $album->songs->reduce(function ($carry, $song) {
                    if (!empty($song->interactions)) {
                        return $carry + $song->interactions->play_count;
                    } else
                        return $carry + 0;
                });
                $album['playCount'] = !empty($playCount) ? $playCount : 0;
                $artist['songCount'] += $album->songs->count();
            });
        });

        return response()->json([
            'artists' => $artists
        ]);
    }

    public function show($id)
    {
        $artist = Artist::with(['albums', 'albums.songs', 'albums.songs.interactions'])->has('albums')->findOrFail($id);
        $artist->albums->each(function ($album) use ($artist) {
            $album->calculatePlayCount();
            $artist['songCount'] += $album->songs->count();
        });

        return response()->json([
            'artist' => $artist
        ]);
    }
}
