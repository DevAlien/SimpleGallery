<?php
/**
 * Loader is in chart to load the Controllers
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

namespace Devfw;

use Devfw\Controller;

/**
 * Loader is in chart to load the Controllers
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class Loader
{

    private $container;

    /**
     * Constructor sets the container
     *
     * @param \libs\DepencencyInjectionDiContainer $container The container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Runs the loader
     */
    public function run()
    {
        $this->loadController($this->container->get('router')->getController(), $this->container->get('router')->getAction(), $this->container->get('router')->getParams(), $this->container->get('router')->getModule());
    }

    /**
     * Runs the system
     *
     * @param string $class class that needs to be loaded
     * @param string $action action of the class that needs to be called
     * @param aray $params parameters, if there are.
     */
    public function loadController($class, $action, $params = null)
    {
        try {
            $c = '\\app\\Controller\\' . $class;
            $controller = new $c();
            $controller->setParams($params);
            $controller->setContainer($this->container);
            call_user_func_array(array($controller, $action), array());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}