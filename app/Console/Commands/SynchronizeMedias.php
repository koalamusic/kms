<?php

namespace App\Console\Commands;

use App\Jobs\SynchronizeMedia;
use App\Models\Setting;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;

class SynchronizeMedias extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kms:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync songs found in configured directory against the database.';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mediasToSync = $this->getMediasToSync();

        foreach ($mediasToSync as $mediaToSync) {
            $this->dispatchNow(new SynchronizeMedia($mediaToSync));
        }
    }

    /**
     * @return Collection
     */
    protected function getMediasToSync()
    {
        $mediasFromFileSystem = $this->getMediasFromFilesystem();
        $alreadySyncedMedias = $this->getAlreadySyncedMedias();

        return $mediasFromFileSystem->diffKeys($alreadySyncedMedias);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAlreadySyncedMedias()
    {
        return Song::all()->keyBy('id');
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    protected function getMediasFromFilesystem()
    {
        $mediaPath = Setting::get('media_path');

        if (!$this->filesystem->isDirectory($mediaPath)) {
            throw new \Exception();
        }
        
        $medias = $this->filesystem->allFiles($mediaPath);

        return Collection::make($medias)
            ->keyBy(function ($file) {
                return sha1_file($file);
            });
    }
}
