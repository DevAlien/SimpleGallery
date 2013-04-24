<?php
/**
 * Controller Interface interface
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */
namespace Devfw\Controller;

use Devfw\DependencyInjection\Container;
use Devfw\Loader;
use Devfw\Router;

/**
 * Controller Interface interface
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class Controller extends Container{

    /**
     * Params set by the Router
     *
     * @var array
     */
    private $params = array();

    /**
     * Container containing all the needed dependencies
     *
     * @var libs\DependencyInjection\DIContainer
     */
    protected $container;

    /**
     * Setter for the params
     *
     * @param array $params array of the params
     */
    public function setParams($params) {
        $this->params = $params;
    }

    /**
     * Getter for the params
     *
     * @param string $param The key param to get
     */
    public function getParam($param) {
        return $this->params[$param];
    }

    /**
     * Gets a service by id.
     *
     * @param  string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}