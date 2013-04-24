<?php
/**
 * The router
 *
 * The router will take the URL and will make sure to give the right route to the requested page.
 *
 * @version 1.0
 */

namespace Devfw;

use Devfw\tools\StringTools;

/**
 * The router
 *
 * The router will take the URL and will make sure to give the right route to the requested page.
 *
 * @version 1.0
 */
class Router
{
    /**
     * The selected controller after that the router processed our route
     *
     * @var string
     */
    public $controller;

    /**
     * The selected module after that the router processed our route
     *
     * @var string
     */
    public $module;

    /**
     * The selected action after that the router processed our route
     *
     * @var string
     */
    public $action;

    /**
     * The GET parameters parsed in the route, you can add them using ":name" in the routing file
     *
     * @var array
     */
    public $params;

    /**
     * Contains all the rules extracted from the routing file
     *
     * @var array
     */
    public $rules;

    /**
     * Base of the URL (normally is the domain)
     *
     * @var string
     */
    public $base;

    /**
     * Base directory, if your index.php is in a subfolder you should change it.
     *
     * @var string
     */
    public $basedir;

    /**
     * Set the base, basedir and initialize the array
     *
     * @param string $base The base URL
     * @param string $basedir The base directory where the index is located.
     */
    public function __construct($base, $basedir)
    {
        $this->base = $base;
        $this->basedir = $basedir;
        $this->rules = array();
        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            parse_str(file_get_contents("php://input"),$_POST);
    }

    /**
     * redirects the user to a selected routing
     *
     * @TODO give the possibility to pass a parameters array 
     * @param string $routing the name of the routing where you want to be redirected to
     */
    public function redirect($routing) 
    {
        if (key_exists($routing, $this->routings)) {
            header('Location:  ' . $this->base . $this->basedir . $this->routings[$routing]['url']);
            die();
        }
    }

    /**
     * Add the rules into the rules property to have them already there when it will use them
     *
     * @param array $routings Routings from the routing file
     */
    public function setRoutings($routings) 
    {
        $this->routings = $routings;
        foreach ($routings as $routing)
            $this->addRule($routing['url'], $routing['param']);
    }

    /**
     * Cleans the array removing the empty ones 
     *
     * @param array $array The array to clean
     */
    private function arrayClean($array) 
    {
        foreach ($array as $key => $value) {
            if (strlen($value) == 0)
                unset($array[$key]);
        }
    }

    /**
     * Match the rule with the given url
     *
     * @param string $rule Rule to be checked
     * @param string $data URL to be compared to
     *
     * @return mixed False or Array with the params if it's true 
     */
    private function ruleMatch($rule, $data)
    {
        $ruleItems = explode('/', $rule);
        $this->arrayClean($ruleItems);
        $dataItems = explode('/', $data);
        $this->arrayClean($dataItems);

        if (count($ruleItems) == count($dataItems)) {
            $result = array();

            foreach ($ruleItems as $ruleKey => $ruleValue) {
                if (preg_match('/^:[\w]{1,}$/', $ruleValue)) {
                    $ruleValue = substr($ruleValue, 1);
                    $result[$ruleValue] = $dataItems[$ruleKey];
                } else {
                    if (strcmp($ruleValue, $dataItems[$ruleKey]) != 0) {
                        return false;
                    }
                }
            }

            if (count($result) > 0)
                return $result;
            unset($result);
        }
        return false;
    }

    /**
     * Easy routing system, will just route you in the path that you give /controller/action/pa/ra/ms
     *
     * @param string $url The URL
     */
    private function defaultRoutes($url)
    {
        $items = explode('/', $url);

        foreach ($items as $key => $value) {
            if (strlen($value) == 0)
                unset($items[$key]);
        }

        if (count($items)) {
            $this->controller = array_shift($items);
            $this->action = array_shift($items);
            $this->params = $items;
        }
    }

    /**
     * This will process the routing in base of the given routings array and the REQUEST_URI
     *
     * @return boolean True if the route is found or die
     */
    public function init()
    {
        $url = ($this->startsWith($_SERVER['REQUEST_URI'], $this->basedir)) ? substr_replace($_SERVER['REQUEST_URI'], '', 0, strlen($this->basedir)) : $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $isCustom = false;
        $realURL = explode('?', $url);
        if (count($this->rules)) {
            foreach ($this->rules as $routing) {
                foreach ($routing as $ruleKey => $ruleData) {
                    if (isset($ruleData['method']) && strtoupper($ruleData['method']) != $method)
                        continue;

                    $params = $this->ruleMatch($ruleKey, $url);
                    if ($params) {
                        $this->controller = $ruleData['controller'];
                        $this->action = $ruleData['action'];
                        $this->module = (key_exists('module', $ruleData) ? $ruleData['module'] : null);
                        $this->params = ($params === true) ? array() : $params;
                        $isCustom = true;
                        
                        return true;
                    }
                    if ($realURL[0] == $ruleKey) {
                        $this->controller = $ruleData['controller'];
                        $this->action = $ruleData['action'];
                        $this->module = (key_exists('module', $ruleData) ? $ruleData['module'] : null);
                        $isCustom = true;

                        return true;
                    }
                }
            }
        }

        if (!$isCustom && strlen($url) > 1)
            die('No Rooting found');
    }

    /**
     * Add a rule in the rules property
     *
     * @param string $rule Name of the routing
     * @param array $target Infos to know where route if the rule is matched
     */
    public function addRule($rule, $target)
    {
        $this->rules[][$rule] = $target;
    }

    /**
     * Get choosed controller
     *
     * @return string Name of the controller to load
     */
    public function getController()
    {
        return ucfirst($this->controller) . 'Controller';
    }

    /**
     * Get choosed action
     *
     * @return string Name of the action to load
     */
    public function getAction()
    {
        return $this->action . 'Action';
    }

    /**
     * Get choosed module
     *
     * @return string Name of the module to load
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get found parameters
     *
     * @return array All the parameters found
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get one parameter by key
     *
     * @return string value of the choosen parameter
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }
}