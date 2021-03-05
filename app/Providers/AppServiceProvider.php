<?php

namespace App\Providers;

use App\Entities\Customer;
use App\Http\Resources\CustomerShortResourceCollection;
use App\Repository\CustomerRepository;
use App\Repository\CustomerRepositoryInterface;
use App\Services\RandomUserImporter;
use App\Services\RandomUserImporterInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind(
            CustomerRepositoryInterface::class,
            function ($app) {
                return new CustomerRepository(
                    $app['em'],
                    $app['em']->getClassMetaData(Customer::class)
                );
            }
        );

        $this->app->bind(RandomUserImporterInterface::class, RandomUserImporter::class);

        CustomerShortResourceCollection::withoutWrapping();
    }
}
