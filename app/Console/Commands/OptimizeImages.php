<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\OptimizeImage;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize images';
    private $public_folder = '/var/www/zagorodnaz/zagorodna.com/public/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = Storage::disk('common')->allFiles('files');
        $files = array_filter($files, function($item) {
            return !strpos($item, '.cache') && (strpos($item, '.jpg') || strpos($item, '.png'));
        });
        
        foreach($files as $file) {
            OptimizeImage::dispatch($this->public_folder . $file)->onQueue('optimizing');
        }

        $files = Storage::disk('common')->allFiles('img');
        $files = array_filter($files, function($item) {
            return !strpos($item, '.cache') && (strpos($item, '.jpg') || strpos($item, '.png'));
        });
        
        foreach($files as $file) {
            OptimizeImage::dispatch($this->public_folder . $file)->onQueue('optimizing');
        }

        $files = Storage::disk('common')->allFiles('uploads');
        $files = array_filter($files, function($item) {
            return !strpos($item, '.cache') && (strpos($item, '.jpg') || strpos($item, '.png'));
        });
        
        foreach($files as $file) {
            OptimizeImage::dispatch($this->public_folder . $file)->onQueue('optimizing');
        }
    }
}
