<?php

namespace App\Libraries\MediaFileParser;

use getID3;
use App\Libraries\MediaFileParser\Adapters\BaseAdapter as Adapter;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class MediaFile
 * @package App\Libraries\MediaFileParser
 */
class MediaFile extends File
{
    /**
     * @var getID3
     */
    protected $getID3;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * MediaFile constructor.
     * @param string $path
     * @param getID3 $getID3
     */
    public function __construct($path, getID3 $getID3 = null)
    {
        parent::__construct($path, true);

        $this->getID3 = (!is_null($getID3)) ? $getID3 : new getID3();
        $this->tags = $this->getID3->analyze($this->getPathname(), $this->getSize());
        $this->adapter = $this->guessAdapterFromTagsType();
    }

    /**
     * @return int
     */
    public function getTrackNumber()
    {
        return $this->getAdapter()->guessTrackNumber();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getAdapter()->guessTitle();
    }

    /**
     * @return string
     */
    public function getArtistName()
    {
        return $this->getAdapter()->guessArtistName();
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->getAdapter()->guessGroupName();
    }

    /**
     * @return string
     */
    public function getGenreName()
    {
        return $this->getAdapter()->guessGenreName();
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->getAdapter()->guessYear();
    }

    /**
     * @return string
     */
    public function guessTagsType()
    {

    }

    /**
     * @return Adapter
     */
    protected function guessAdapterFromTagsType()
    {

    }

    /**
     * @return getID3
     */
    public function getGetID3()
    {
        return $this->getID3;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}