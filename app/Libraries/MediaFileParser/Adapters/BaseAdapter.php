<?php

namespace App\Libraries\MediaFileParser\Adapters;

use App\Libraries\MediaFileParser\Contracts\Adapter;
use App\Libraries\MediaFileParser\MediaFileInfo;
use Illuminate\Support\Arr;

/**
 * Class BaseAdapter
 *
 * @package App\Libraries\MediaFileParser\Adapters
 */
abstract class BaseAdapter implements Adapter
{
    /**
     * @var MediaFileInfo
     */
    protected $mediaFileInfo;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * BaseAdapter constructor.
     *
     * @param \App\Libraries\MediaFileParser\MediaFileInfo $mediaFileInfo
     */
    public function __construct(MediaFileInfo $mediaFileInfo)
    {
        $this->mediaFileInfo = $mediaFileInfo;
        $this->tags = $mediaFileInfo->getTags();
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

    /**
     * @return float|null
     */
    public function guessDuration()
    {
        return $this->getTag('playtime_seconds');
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getTag($key, $default = null)
    {
        return Arr::get($this->tags, $key, $default);
    }

    /**
     * @return integer|null
     */
    public function guessBitrate()
    {
        return $this->getTag('bitrate');
    }
}