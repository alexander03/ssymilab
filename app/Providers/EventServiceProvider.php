<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\ActiveIngredient;
use App\Models\BranchOffice;
use App\Models\Brand;
use App\Models\Card;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\Company;
use App\Models\Currency;
use App\Models\CustomerPrice;
use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\DocumentPayment;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\MenuCategory;
use App\Models\MenuOption;
use App\Models\Operation;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Reason;
use App\Models\TransferMethod;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserType;
use App\Observers\BinnacleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Action::observe(BinnacleObserver::class);
        ActiveIngredient::observe(BinnacleObserver::class);
        BranchOffice::observe(BinnacleObserver::class);
        Brand::observe(BinnacleObserver::class);
        Card::observe(BinnacleObserver::class);
        Category::observe(BinnacleObserver::class);
        Checkout::observe(BinnacleObserver::class);
        Company::observe(BinnacleObserver::class);
        CustomerPrice::observe(BinnacleObserver::class);
        Document::observe(BinnacleObserver::class);
        DocumentDetail::observe(BinnacleObserver::class);
        DocumentPayment::observe(BinnacleObserver::class);
        ExchangeRate::observe(BinnacleObserver::class);
        MenuCategory::observe(BinnacleObserver::class);
        MenuOption::observe(BinnacleObserver::class);
        Payment::observe(BinnacleObserver::class);
        Person::observe(BinnacleObserver::class);
        Product::observe(BinnacleObserver::class);
        ProductUnit::observe(BinnacleObserver::class);
        Reason::observe(BinnacleObserver::class);
        TransferMethod::observe(BinnacleObserver::class);
        Unit::observe(BinnacleObserver::class);
        User::observe(BinnacleObserver::class);
        UserType::observe(BinnacleObserver::class);
    }
}
