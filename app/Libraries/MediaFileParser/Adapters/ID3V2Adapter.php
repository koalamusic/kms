<?php

namespace App\Libraries\MediaFileParser\Adapters;

/**
 * Class ID3V1Adapter
 * @package App\Libraries\MediaFileParser\Adapters
 */
class ID3V2Adapter extends ID3V1Adapter
{
    /**
     * @return string|null
     */
    public function guessTitle()
    {
        if ($title = $this->getTag('id3v2.comments_html.title')) {
            return $title;
        }

        if ($title = $this->getTag('id3v2.comments.title.0')) {
            return $title;
        }

        return $this->getTag('id3v2.title');
    }

    /**
     * @return string|null
     */
    public function guessArtistName()
    {
        if ($artist = $this->getTag('id3v2.comments_html.artist.0')) {
            return $artist;
        }

        return $this->getTag('id3v2.artist.0');
    }

    /**
     * @return integer|null
     */
    public function guessYear()
    {
        if ($year = $this->getTag('id3v2.comments.creation_date')) {
            return $year;
        }

        if ($year = $this->getTag('id3v2.comments.year')) {
            return $year;
        }

        return $this->getTag('id3v2.year');
    }
}