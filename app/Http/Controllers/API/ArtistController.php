<?php

namespace App\Http\Controllers\API;

use App\Models\Artist;
use App\Models\Song;

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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $artists = Artist::with(['albums', 'albums.songs', 'albums.songs.interactions'])->has('albums')->get();

        // Calculate playCount for each album
        $artists->each(function($artist) {
            $artist->albums->each(function ($album) use ($artist) {
                $album->calculatePlayCount();
                $artist['songCount'] += $album->songs->count();
                unset($album['songs']);
            });
        });

        return response()->json([
            'artists' => $artists
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $artist = Artist::with(['albums', 'albums.songs', 'albums.songs.interactions'])->has('albums')->findOrFail($id);
        $artist->albums->each(function ($album) use ($artist) {
            $album['songCount'] = $album->songs->count();
            $album->calculatePlayCount();
            $artist['songCount'] += $album->songs->count();
            unset($album['songs']);
        });

        return response()->json([
            'artist' => $artist
        ]);
    }

    /**
     * @param $artistId
     * @return \Illuminate\Http\JsonResponse
     */
    public function songs($artistId)
    {
        $songs = Song::with(['album', 'interactions', 'genre', 'album.artist', 'contributingArtist'])
            ->whereHas('album', function($query) use ($artistId) {
                $query->where('artist_id', '=', $artistId);
            })
            ->orderBy('album_id', 'asc')
            ->orderBy('track', 'asc')
            ->get();

        return response()->json([
            'songs' => $songs
        ]);
    }
}
