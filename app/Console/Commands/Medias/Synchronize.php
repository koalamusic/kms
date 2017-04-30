<?php

namespace App\Console\Commands\Medias;

use App\Jobs\SynchronizeMedia;
use App\Libraries\MediaFileParser\MediaFile;
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
     * @return Collection
     * @throws \Exception
     */
    protected function getMediasFromFilesystem()
    {
        $mediaPath = Setting::get('media_path');

        if (! $this->filesystem->isDirectory($mediaPath)) {
            throw new Exception();
        }

        $this->info('Reading media files from file system ...');

        $medias = $this->filesystem->allFiles($mediaPath);

        $this->info('Building collection ...');

        Collection::make($medias)->keyBy(function (SplFileInfo $file) {
            return spl_object_hash($file);
        });

        $this->info('End building collection ...');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAlreadySyncedMedias()
    {
        return Song::all()->keyBy('id');
    }
}
