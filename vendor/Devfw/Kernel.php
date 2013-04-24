<?php
/**
 * The kernel is in charge of the system, it loads the components and prepare the Dependency Injection Container
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright anecms.com (C) 2008-2012
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

namespace Devfw;

use Devfw\DependencyInjection\DIContainer;
use Devfw\Config;
use Devfw\Spyc;
use Devfw\Router;
use Devfw\Loader;
use Devfw\Logger\KLogger;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * The kernel is in charge of the system, it loads the components and prepare the Dependency Injection Container
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright anecms.com (C) 2008-2012
 * @version 1.0
 */
class Kernel {
    /**
     * Debug flag
     *
     * @var boolean
     */
    private $debug;

    /**
     * Logger
     *
     * @var libs\logger\KLogger
     */
    private $log;

    /**
     * DIContainer object which will contain all the components
     *
     * @var DIContainer
     */
    private $container;
    
    /**
     * Runs the system
     *
     * @param boolean $debug if we are in debug mod or not.
     */
    public function __construct($debug = false){
        $this->log = new KLogger(ROOT . '/log/', KLogger::DEBUG);
        
        $this->debug = (Boolean) $debug;

        $this->init();
    }
    
    /**
     * starts the loading system
     *
     * @return void
     */
    private function init(){
        $this->boot();
    }

    /**
     * Boots the initialization of the components
     *
     * @return void
     */
    private function boot(){
        $this->initializeDIContainer();
        $this->initializeBasicComponents();
        
    }

    /**
     * Initialize the Dependency Injection Container
     *
     * @return void
     */
    private function initializeDIContainer() {
        $this->container = new DIContainer();
        $this->container->set('logger', $this->log);
        $this->container->set('kernel', $this);
        $this->container->set('yaml', function ($c) { return new Spyc(); });
    }

    /**
     * Loads the basic components into the Container
     *
     * @return void
     */
    private function initializeBasicComponents(){
        $this->initializeConfig();
        $this->loadDoctrine();
        $this->container->set('router', $this->container->share(function ($c) { return new Router($c->get('config')->get('base'),$c->get('config')->get('base_dir')); }));
        $this->container->set('template', $this->container->share(function ($c) { 
            $template = new Template();
            $template->setConfig($c->get('config'));
            return $template;
        }));
        $this->container->set('loader', $this->container->share(function ($c) { return new Loader($c); }));
    }

    /**
     * Starts the routing
     *
     * @return void
     */
    private function loadRouting(){
        $routings = $this->container->get('yaml')->loadRoutingFile('app/config/' . $this->container->get('config')->get('routing') . '.routing.yaml');
        $router = $this->container->get('router');
        $router->setRoutings($routings);
        $router->init();
    }

    /**
     * Starts the Loader and loads the right object in base of the routing
     *
     * @return void
     */
    private function initializeConfig(){
        $this->container->set('config.fileLocation', 'app/config/config.yaml');
        $this->container->set('config.parsed', $this->container->get('yaml')->loadConfigFile($this->container->get('config.fileLocation')));
        $this->container->set('config', $this->container->share(function ($c) { return new Config($c->get('config.parsed')); }));
    }

    /**
     * Loads doctrine if db_type is set in the configuration file
     *
     * @return void
     */
    private function loadDoctrine()
    {
        if(!$this->container->get('config')->exists('db_type'))
            return;

        $this->container->set('em', 
            $this->container->share(function ($c) { 
                if($c->get('config')->exists('db_entities_location'))
                    $paths = array(__DIR__ . '/../../' . $c->get('config')->get('db_entities_location'));
                else
                    $paths = array(__DIR__ . '/../../app/Entity');

                $isDevMode = false;

                $dbParams = array(
                    'driver'   => 'pdo_' . $c->get('config')->get('db_type'),
                    'user'     => $c->get('config')->get('db_username'),
                    'password' => $c->get('config')->get('db_password'),
                    'dbname'   => $c->get('config')->get('db_name'),
                    'host'     => $c->get('config')->get('db_host')
                );

                $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                $config->addEntityNamespace($c->get('config')->get('db_namespace'), 'app\Entity');

                return EntityManager::create($dbParams, $config);
            }
        ));
    }

    /**
     * Starts the Loader and loads the right object in base of the routing
     *
     * @return void
     */
    public function run(){
        $this->loadRouting();
        $this->container->get('loader')->run();
    }

    public function getContainer() {
        return $this->container;
    }
}