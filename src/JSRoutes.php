<?php

namespace ExMachina\LaravelRouteJs;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class JSRoutes
{
    protected $path;

    protected $routeListFilename = 'laravel-routes.json';

    protected $actionFileName = 'laravel-routes.js';

    /**
     * List of which to include/exclude in json file
     * @var element type: string/regexp
     */
    protected $routePatterns = [];

    /**
     * Flag if the provided routePatterns is excluded
     * @var boolean
     */
    protected $excludePatterns = false;

    /**
     * Determine the generated files 
     * route list and action.js files will be generated by default.
     * @var boolean
     */
    protected $excludeActionJS = false;

    protected $append = false;

    /**
     * Instance of Illuminate\Support\Facades\Storage;
     */
    private $storageDisk = null;

    /**
     * Filesystem instance
     */
    private $file;

    public function __construct()
    {
        $this->file = new Filesystem;
    }

    public function generate()
    {
        $routes = $this->savedroutes();
        
        foreach (Route::getRoutes() as $route) {
            
            $name = $route->getName();

            if (empty($name) || $this->isRouteExcluded($name)) continue;

            $routes[$name] = [
                'url' => $route->uri(),
            ];

            if (count($route->parameterNames())) {
                $routes[$name]['parameters'] = $route->parameterNames();
            }
        }

        $this->writeRouteList(json_encode($routes, JSON_PRETTY_PRINT));

        if (! $this->excludeActionJS) $this->writeRouteAction();
    }

    /**
     * 
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param array $patterns
     * @return  $this
     */
    public function setRoutePatterns(array $patterns)
    {
        $this->routePatterns = $patterns;
        
        return $this;
    }

    public function excludePatterns()
    {
        $this->excludePatterns = true;

        return $this;
    }

    public function excludeActionJS()
    {
        $this->excludeActionJS = true;

        return $this;
    }

    /**
     * Set append for appending the saved routes when generating
     * @return $this
     */
    public function append()
    {
        $this->append = true;

        return $this;
    }

    /**
     * Sets the storage disk
     * 
     * @param string $storageDisk
     * @return $this
     */
    public function setStorageDisk(string $storageDisk)
    {
        $this->storageDisk = Storage::disk($storageDisk);

        return $this;
    }

    /**
     * Writes the route action in a file
     */
    public function writeRouteAction()
    {
        return $this->write($this->path, $this->actionFileName, $this->buildActionContent());
    }

    /**
     * Writes route list in a file
     * @param  string $routes 
     * @return boolean
     */
    protected function writeRouteList(string $routes)
    {
        return $this->write($this->path, $this->routeListFilename, $routes);
    }

    protected function write(string $path, string $fileName, string $contents)
    {
        $file = $path . DIRECTORY_SEPARATOR . $fileName;

        if (is_null($this->storageDisk)) {
            $this->file->ensureDirectoryExists($path);
            return $this->file->put($file, $contents);
        } else {
            return $this->storageDisk->put($file, $contents);
        }
    }

    protected function isRouteExcluded(string $name) : bool
    {
        if (empty($this->routePatterns)) return false;

        foreach ($this->routePatterns as $routePattern) {
            // check if routePattern is regexp
            if (preg_match('/^\/.*\/$/', $routePattern) && 
                preg_match($routePattern, $name)    
            ) {
                return $this->excludePatterns ? true : false;
            } elseif ($routePattern == $name) {
                return $this->excludePatterns ? true : false;
            }
        }

        /**
         * We need to do this to include any routes 
         * that doesn't satify the patterns
         * if excludePattern flag is set to true
         */
        return ! $this->excludePatterns;
    }

    /**
     * Get routes in generated route list json file if 
     * append set is set to true, otherwise empty array
     * @return array
     */
    protected function savedRoutes() : array
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->routeListFilename;

        return $this->append && file_exists($file) ? 
            json_decode(file_get_contents($file), true) : [];
    }

    private function buildActionContent()
    {
        $stub = __DIR__ . '/Stubs/js-routes.stub';
        
        $content = file_get_contents($stub);

        $content = str_replace('DummyRouteList', './' . $this->routeListFilename, $content);

        return $content;
    }
}
