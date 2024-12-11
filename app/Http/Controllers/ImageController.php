<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function filePath(Filesystem $filesystem, $path)
    {
	    $filesystem = Storage::disk('files');
        //dd(Storage::disk('local'));
        
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'img',
        ]);

		//dd($filesystem);
        return $server->getImageResponse($path, request()->all());
    }
    
    public function imagePath(Filesystem $filesystem, $path)
    {
	    $filesystem = Storage::disk('images');
	    //dd(Storage::disk('local'));
	    
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'img',
        ]);

		//dd($filesystem);
        return $server->getImageResponse($path, request()->all());
    }    

    public function commonPath(Filesystem $filesystem, $path)
    {
	    $filesystem = Storage::disk('common');
	    //dd(Storage::disk('local'));
	    
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'img',
        ]);

		//dd($filesystem);
        return $server->getImageResponse($path, request()->all());
    }    
}