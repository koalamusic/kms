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
            $playCount = $album->songs->reduce(function($carry, $song) {
                if(!empty($song->interactions)) {
                    return $carry + $song->interactions->play_count;
                } else
                    return $carry + 0;
            });
            $album['playCount'] = !empty($playCount) ? $playCount : 0;
        });

        return response()->json([
            'albums' => $albums
        ]);
    }
}
