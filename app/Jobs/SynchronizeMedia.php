<?php

namespace App\Jobs;

use App\Libraries\MediaFileParser\MediaFileInfo;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SplFileInfo;

class SynchronizeMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var SplFileInfo
     */
    public $media;

    /**
     * Create a new job instance.
     *
     * @param \SplFileInfo $media
     */
    public function __construct(SplFileInfo $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media = new MediaFileInfo($this->media);

        if ($this->media->isMediaFile()) {
            Song::create([
                'album_id' => 1,
                'title' => $media->title,
                'path' => $media->getRealPath(),
                'mtime' => $media->getMTime(),
            ]);
        }
    }
}
