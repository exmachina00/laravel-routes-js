<?php

namespace ExMachina\JSRoutes\Commands;

use ExMachina\JSRoutes\JSRoutes;

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

        $this->jsRouteService = new JSRoutes;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->jsRouteService->setRoutePatterns($this->option('routes'));

        if ($this->option('exclude')) $this->jsRouteService->excludePatterns();

        if ($this->option('dir')) {
            $this->jsRouteService->setPath($this->option('dir'));
        } else {
            $this->jsRouteService->setStorageDisk('resources');
        }

        /**
         * @todo : Append functionality
         */

        $this->jsRouteService->generate();
    }
}
