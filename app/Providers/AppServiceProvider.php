<?php

namespace App\Providers;

use App\Repositories\Billboard\BillboardRepository;
use App\Repositories\Billboard\BillboardRepositoryInterface;
use App\Repositories\BillboardType\BillboardTypeRepository;
use App\Repositories\BillboardType\BillboardTypeRepositoryInterface;
use App\Repositories\BillboardFace\BillboardFaceRepository;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;
use App\Repositories\BillboardStructure\BillboardStructureRepository;
use App\Repositories\BillboardStructure\BillboardStructureRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\City\CityRepository;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\Dashboard\DashboardRepository;
use App\Repositories\Dashboard\DashboardRepositoryInterface;
use App\Repositories\DigitalBillboardPlan\DigitalBillboardPlanRepository;
use App\Repositories\DigitalBillboardPlan\DigitalBillboardPlanRepositoryInterface;
use App\Repositories\Organization\OrganizationRepository;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\People\PeopleRepository;
use App\Repositories\People\PeopleRepositoryInterface;
use App\Repositories\Person\PersonRepository;
use App\Repositories\Person\PersonRepositoryInterface;
use App\Repositories\Quote\QuoteRepository;
use App\Repositories\Quote\QuoteRepositoryInterface;
use App\Repositories\QuoteRequest\QuoteRequestRepository;
use App\Repositories\QuoteRequest\QuoteRequestRepositoryInterface;
use App\Repositories\Rental\RentalRepository;
use App\Repositories\Rental\RentalRepositoryInterface;
use App\Repositories\Request\RequestRepository;
use App\Repositories\Request\RequestRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Zone\ZoneRepository;
use App\Repositories\Zone\ZoneRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            BillboardFaceRepositoryInterface::class,
            BillboardFaceRepository::class
        );

        $this->app->bind(
            BillboardRepositoryInterface::class,
            BillboardRepository::class
        );

        $this->app->bind(
            BillboardTypeRepositoryInterface::class,
            BillboardTypeRepository::class
        );

        $this->app->bind(
            CityRepositoryInterface::class,
            CityRepository::class
        );

        $this->app->bind(
            OrganizationRepositoryInterface::class,
            OrganizationRepository::class
        );

        $this->app->bind(
            QuoteRepositoryInterface::class,
            QuoteRepository::class
        );

        $this->app->bind(
            RentalRepositoryInterface::class,
            RentalRepository::class
        );

        $this->app->bind(
            PersonRepositoryInterface::class,
            PersonRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            RequestRepositoryInterface::class,
            RequestRepository::class
        );

        $this->app->bind(
            QuoteRequestRepositoryInterface::class,
            QuoteRequestRepository::class
        );

        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
        );

        $this->app->bind(
            BillboardStructureRepositoryInterface::class,
            BillboardStructureRepository::class
        );

        $this->app->bind(
            DigitalBillboardPlanRepositoryInterface::class,
            DigitalBillboardPlanRepository::class
        );

        $this->app->bind(
            ZoneRepositoryInterface::class,
            ZoneRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }
}
