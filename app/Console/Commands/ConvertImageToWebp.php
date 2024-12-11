<?php

declare(strict_types=1);


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class ConvertImageToWebp extends Command
{
    const BASE_PATH = '/var/www/zagorodnaz/zagorodna.com/public';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ts:image:convert {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert a JPEG images from articles table to WebP format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->enableQueryLogging();

        $id = $this->argument('id');
        $article = null;

        if ($id) {
            $article = DB::table('articles')->where('id', $id)->first();
        } else {
            $article = DB::table('articles')
                ->where('image', 'like', '%.jpg')
                ->inRandomOrder()
                ->first();
        }

        if (!$article) {
            $this->error('No article found.');
            return 1;
        }

        $imagePath = $article->image;
        if (!str_starts_with($imagePath, '/')) {
            $imagePath = '/' . $imagePath;
        }

        $this->info("Jpeg image path: {$imagePath}");
        $imageFullPath = self::BASE_PATH . $imagePath;
        $this->info("Jpeg image full path: {$imageFullPath}");
        $webpPath = preg_replace('/\.jpg$/', '.webp', $imagePath);
        $this->info("Image webp path: {$webpPath}");
        $webpFullPath = preg_replace('/\.jpg$/', '.webp', $imageFullPath);
        $this->info("Image webp full path: {$webpFullPath}");

        // Run the cwebp command
        $command = sprintf(
            '/usr/bin/cwebp -q 80 %s -o %s',
            escapeshellarg($imageFullPath),
            escapeshellarg($webpFullPath)
        );
        $this->info("Shell Command: {$command}");

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Conversion failed: ' . implode("\n", $output));
            return $returnVar;
        }

        // Update the image field in the database for the current article
        DB::table('articles')
            ->where('id', $article->id)
            ->update(['image' => $webpPath]);

        // Update all articles that use the same image
        $updatedRows = DB::update(
            'UPDATE `articles` SET `image` = ? WHERE `image` = ?',
            [$webpPath, $imagePath]
        );

        $this->info('Conversion successful!');
        $this->info('Command Output: ' . implode("\n", $output));
        $this->info("Images folder base path: " . self::BASE_PATH);
        $this->info("Image field updated to: $webpPath");
        $this->info("Total articles updated: $updatedRows");

        return 0;
    }

    /**
     * Enable query logging and listen for queries to display them.
     */
    private function enableQueryLogging()
    {
        DB::listen(function ($query) {
            $sql = $query->sql;
            $bindings = json_encode($query->bindings);
            $time = $query->time;
            $this->info("SQL: $sql");
            $this->info("Bindings: $bindings");
            $this->info("Execution Time: {$time}ms");
        });
    }
}
