<?php

namespace App\Providers;

use App\Services\Converter\ConverterInterface;
use App\Services\Converter\PDFConverter;
use App\Services\Repository\RepositoryInterface;
use App\Services\Repository\FileSystemRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ConverterInterface::class, PDFConverter::class);
        $this->app->bind(RepositoryInterface::class, FileSystemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
