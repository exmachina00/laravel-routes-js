<?php

namespace ExMachina\LaravelRouteJs\Commands;

use ExMachina\LaravelRouteJs\JSRoutes;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CreateJsRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'js-route:generate 
        {--routes=* : Specify routes(string or regexp) to be generated in json file.}
        {--dir= : Directory where generated files will be written.}
        {--exclude : Exclude routes specified in ROUTES parameter.}
        {--exclude-js : Prevent the generation of action(JS) file.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create JS and JSON files of routes';

    /**
     * JSRoute instance
     */
    private $jsRouteService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(JSRoutes $jsRoute)
    {
        parent::__construct();

        $this->jsRouteService = $jsRoute;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->jsRouteService->setRoutePatterns(
            $this->option('routes') ?: config('route-js.route.patterns')
        );

        $this->jsRouteService->setPath($this->option('dir') ?: config('route-js.dir'));

        if ($this->option('exclude') || config('route-js.routes.exclude')) {
            $this->jsRouteService->excludePatterns();
        }

        if ($this->option('exclude-js')) $this->jsRouteService->excludeActionJS();

        /**
         * @todo : Append functionality
         */

        $this->jsRouteService->generate();
    }
}
