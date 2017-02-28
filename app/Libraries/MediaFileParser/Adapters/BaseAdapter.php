<?php

namespace App\Libraries\MediaFileParser\Adapters;

/**
 * Class BaseAdapter
 * @package App\Libraries\MediaFileParser\Adapters
 */
abstract class BaseAdapter
{
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * BaseAdapter constructor.
     * @param array $tags
     */
    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    abstract public function guessTrackNumber();

    /**
     * @return string
     */
    abstract public function guessTitle();

    /**
     * @return string
     */
    abstract public function guessArtistName();

    /**
     * @return string
     */
    abstract public function guessGroupName();

    /**
     * @return string
     */
    abstract public function guessGenreName();

    /**
     * @return int
     */
    abstract public function guessYear();
}