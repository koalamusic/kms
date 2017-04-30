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
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

        $medias = $this->filesystem->allFiles($mediaPath);

        $count = count($medias);

        $this->info("Building collection ({$count}) ...");

        $medias = Collection::make($medias)->transform(function (SplFileInfo $file) {
            return $this->getFileHash($file);
        });

        $this->info('End building collection ...');

        dd($medias);
    }

    /**
     * @param \SplFileInfo $file
     * @return string
     */
    protected function getFileHash(SplFileInfo $file)
    {
        $process = new Process("sha1sum '{$file->getRealPath()}'");
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = Str::words($process->getOutput(), 1, '');

        return $output;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAlreadySyncedMedias()
    {
        return Song::all()->keyBy('id');
    }
}
