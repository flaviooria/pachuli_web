<?php

namespace Pachuli\Web\Core;

use Exception;
use Pachuli\Web\Http\Kernel;
use Pachuli\Web\Http\Router;

require_once __DIR__ . '/../Config/constants.php';

class Application
{
    protected ?Router $router;
    private Kernel $kernel;

    /**
     * @throws Exception
     */
    public function __construct(?Router $router = null)
    {
        $this->router = null;

        if (!$router) {
            $routesFile = ROUTES . 'web.php';

            if (!file_exists($routesFile)) {
                throw new Exception("Routes not defined");
            }

            require_once $routesFile;

            $definedVars = get_defined_vars(); // Obtiene todas las variables definidas
            foreach ($definedVars as $value) {
                if ($value instanceof Router) {
                    // Asignamos la variable de tipo Router
                    $this->router = $value;
                    break;
                }
            }

            if (!$this->router) {
                throw new Exception("No Router instance found in web.php");
            }
        } else {
            $this->router = $router;
        }

        $this->kernel = new Kernel($this->router);

    }

    public function run(): void
    {
        $this->kernel->resolve();
    }

}