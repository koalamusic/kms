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
     * @var MediaFileInfo
     */
    public $media;

    /**
     * Create a new job instance.
     *
     * @param \SplFileInfo $media
     */
    public function __construct(SplFileInfo $media)
    {
        $this->media = new MediaFileInfo($media);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Song::create([
                'album_id' => 1,
                'title' => $this->media->title,
                'path' => $this->media->getRealPath(),
                'mtime' => $this->media->getMTime(),
            ]);
        } catch (\Exception $e) {
            
        }

    }
}
