<?php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\FencedCodeRenderer;
use App\Services\MetaBag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerMeta();
        $this->registerRepositories();
        $this->registerCommonmark();
    }

    public function boot(): void
    {
        Paginator::useTailwind();
    }

    public function registerMeta(): void
    {
        $this->app->singleton(MetaBag::class);

        View::share('meta', $this->app->make(MetaBag::class));
    }

    public function registerRepositories(): void
    {
        $this->app->singleton(PostRepository::class);
        $this->app->singleton(AuthorRepository::class);
        $this->app->singleton(CategoryRepository::class);
    }

    public function registerCommonmark(): void
    {
        $commonMark = $this->app->instance(
            CommonMarkConverter::class,
            $this->app->make(ConverterInterface::class)
        );
        $environment = $commonMark->getEnvironment();
        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer());
    }
}
