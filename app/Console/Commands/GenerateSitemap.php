<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use App\Sitemap\CustomUrl;
use Aimix\Shop\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Category as ArticleCategory;
use Backpack\NewsCRUD\app\Models\Article;
use Psr\Http\Message\UriInterface;
use Aimix\Shop\app\Models\Product;
use Aimix\Shop\app\Models\Brand;
use Aimix\Shop\app\Models\BrandCategory;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $start = Carbon::now();
        $this->info("Command " . $this->signature . " started at: " . $start->toDateTimeString());

        (new ArticleCategory)->clearGlobalScopes();

        $excluded_regions = \App\Region::where('is_active', 0)->pluck('region_id');
        $article_category = ArticleCategory::where('is_active', 1)->orderBy('updated_at', 'desc')->first();
        $article_ru = Article::where('language_abbr', 'ru')->where('status', 'PUBLISHED')->orderBy('updated_at', 'desc')->first();
        $article_uk = Article::where('language_abbr', 'uk')->where('status', 'PUBLISHED')->orderBy('updated_at', 'desc')->first();
        $product = Product::where('is_active', 1)->orderBy('updated_at', 'desc')->first();
        $firm = Brand::where('category_id', '!=', null)->where('is_active', 1)->orderBy('updated_at', 'desc')->first();

        $sitemap = Sitemap::create();

        $sitemap->add(Url::create(url('/'))
            ->setChangeFrequency('daily')
            ->setPriority(1));

        $sitemap->add(Url::create(url('/uk'))
            ->setChangeFrequency('daily')
            ->setPriority(1));

        $sitemap->add(Url::create(url('sitemap_categories.xml'))
            ->setLastModificationDate($article_category->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0));

        $sitemap->add(Url::create(url('sitemap_posts_ru.xml'))
            ->setLastModificationDate($article_ru->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0));

        $sitemap->add(Url::create(url('sitemap_posts_uk.xml'))
            ->setLastModificationDate($article_uk->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0));

        $sitemap->add(Url::create(url('sitemap_products.xml'))
            ->setLastModificationDate($product->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0));

        $sitemap->add(Url::create(url('sitemap_firms.xml'))
            ->setLastModificationDate($firm->updated_at)
            ->setChangeFrequency('daily')
            ->setPriority(0));

        $sitemap->writeToFile(public_path('sitemap.xml'));


        $article_categories = ArticleCategory::where('is_active', 1)->where('parent_id', null)->get();

        $sitemap = Sitemap::create();

        foreach ($article_categories as $category) {
            $sitemap->add(Url::create($category->link)
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.8));
        }

        $article_categories = ArticleCategory::where('is_active', 1)->where('parent_id', '!=', null)->get();

        foreach ($article_categories as $category) {
            $sitemap->add(Url::create($category->link)
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.7));
        }

        $sitemap->writeToFile(public_path('sitemap_categories.xml'));

        $articles = Article::where('language_abbr', 'ru')->where('status', 'PUBLISHED')->get();

        $sitemap = Sitemap::create();

        foreach ($articles as $article) {
            if (!$article->link)
                continue;

            $sitemap->add(Url::create($article->link)
                ->setLastModificationDate($article->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.6));
        }

        $sitemap->writeToFile(public_path('sitemap_posts_ru.xml'));

        $articles = Article::where('language_abbr', 'uk')->where('status', 'PUBLISHED')->get();

        $sitemap = Sitemap::create();

        foreach ($articles as $article) {
            if (!$article->link)
                continue;

            $sitemap->add(Url::create($article->link)
                ->setLastModificationDate($article->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.6));
        }

        $sitemap->writeToFile(public_path('sitemap_posts_uk.xml'));

        $products = Product::where('is_active', 1)->whereNotIn('products.address->region', $excluded_regions)->get();

        $sitemap = Sitemap::create();

        foreach ($products as $product) {
            $sitemap->add(Url::create($product->link)
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.6));
        }

        $sitemap->writeToFile(public_path('sitemap_products.xml'));

        $firms = Brand::where('category_id', '!=', null)
            ->where('is_active', 1)
            ->whereHas('category', function ($q) {
                $q->where('is_active', 1);
            })
            ->whereNotIn('brands.address->region', $excluded_regions)
            ->get();

        $firms_categories = BrandCategory::where('is_active', 1)->get();

        $sitemap = Sitemap::create();

        foreach ($firms_categories as $category) {
            $sitemap->add(Url::create($category->link)
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.8));
        }

        foreach ($firms as $firm) {
            $sitemap->add(Url::create($firm->link)
                ->setLastModificationDate($firm->updated_at)
                ->setChangeFrequency('daily')
                ->setPriority(0.6));
        }

        $sitemap->writeToFile(public_path('sitemap_firms.xml'));

        $finish = Carbon::now();
        $this->info("Command " . $this->signature . " finished at: " . $finish->toDateTimeString());
    }
}
