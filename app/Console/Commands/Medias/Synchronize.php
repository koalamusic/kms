<?php

namespace App\Console\Commands\Medias;

use App\Jobs\SynchronizeMedia;
use App\Models\Setting;
use App\Models\Song;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use SplFileInfo;

/**
 * Class Synchronize
 *
 * @package App\Console\Commands\Medias
 */
class Synchronize extends Command
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
     */
    public function handle()
    {
        $mediasToSync = $this->getMediasToSync();

        $bar = $this->output->createProgressBar($mediasToSync->count());

        foreach ($mediasToSync as $mediaToSync) {
            $this->dispatch(new SynchronizeMedia($mediaToSync->getRealPath()));

            $bar->advance();
        }

        $bar->finish();
    }

    /**
     * @return Collection
     */
    protected function getMediasToSync()
    {
        $mediasFromFileSystem = $this->getMediasFromFilesystem()->keyBy(function (SplFileInfo $media) {
            return hash('sha1', $media->getRealPath());
        });

        $alreadySyncedMedias = $this->getAlreadySyncedMedias()->keyBy(function (Song $media) {
            return hash('sha1', $media->path);
        });

        $mediasToSync = $mediasFromFileSystem->diffKeys($alreadySyncedMedias);

        return $mediasToSync;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    protected function getMediasFromFilesystem()
    {
        $mediaPath = Setting::get('media_path');

        if (! $this->filesystem->isDirectory($mediaPath)) {
            throw new Exception();
        }

        return Collection::make($this->filesystem->allFiles($mediaPath));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAlreadySyncedMedias()
    {
        return Song::all();
    }
}
