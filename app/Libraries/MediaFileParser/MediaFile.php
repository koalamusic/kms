<?php

namespace App\Libraries\MediaFileParser;

use App\Libraries\MediaFileParser\Adapters\BaseAdapter as Adapter;
use App\Libraries\MediaFileParser\Adapters\ID3V2Adapter;
use getID3;
use getid3_lib;
use Illuminate\Support\Arr;
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
     *
     * @param string $path
     * @param getID3 $getID3
     */
    public function __construct($path, getID3 $getID3 = null)
    {
        parent::__construct($path, true);

        $this->getID3 = (!is_null($getID3)) ? $getID3 : new getID3();
        $this->tags = $this->getTagsFromFile();
        $this->adapter = $this->guessAdapterFromTagsType();
    }

    /**
     * @return array
     */
    private function getTagsFromFile()
    {
        $tags = $this->getID3->analyze($this->getPathname(), $this->getSize());
        getid3_lib::CopyTagsToComments($tags);

        return $tags;
    }

    /**
     * @return Adapter
     */
    protected function guessAdapterFromTagsType()
    {
        switch ($this->guessTagsType()) {
            case 'ID3V2':
                return new ID3V2Adapter($this->tags);
                break;
        }
    }

    /**
     * @return string|null
     */
    public function guessTagsType()
    {
        if (Arr::exists($this->tags, 'id3v2')) {
            return 'ID3V2';
        }

        return null;
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
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->getAdapter()->{'guess' . ucfirst($name)}($arguments);
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}