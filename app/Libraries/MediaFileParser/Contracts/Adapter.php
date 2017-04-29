<?php

namespace App\Libraries\MediaFileParser\Contracts;

/**
 * Interface Adapter
 */
interface Adapter
{
    /**
     * @return int
     */
    public function guessTrackNumber();

    /**
     * @return string
     */
    public function guessTitle();

    /**
     * @return string
     */
    public function guessArtistName();

    /**
     * @return string
     */
    public function guessGroupName();

    /**
     * @return string
     */
    public function guessGenreName();

    /**
     * @return int
     */
    public function guessYear();

    /**
     * @return float|null
     */
    public function guessDuration();

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getTag($key, $default = null);

    /**
     * @return integer|null
     */
    public function guessBitrate();
}