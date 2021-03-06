<?php

namespace App\Providers;

use App\Fileentry;
use App\Task;
use App\Counter;
use App\Person;
use App\Passpack;
use App\Note;
use App\Warranty;
use App\Tag;
use App\Countercategory;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $router->model('task', Task::class);
        $router->model('counter', Counter::class);
        $router->model('person', Person::class);
        $router->model('passpack', Passpack::class);
        $router->model('note', Note::class);
        $router->model('tag', Tag::class);
        $router->model('countercategory', Countercategory::class);
        $router->model('fileentry', Fileentry::class);
        $router->model('warranty', Warranty::class);

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }
}
