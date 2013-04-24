<?php
/**
 * Container
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
namespace Devfw\DependencyInjection;

/**
 * Container
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class Container implements ContainerInterface{

    /**
     * @var DIContainerInterface
     *
     * @api
     */
    protected $container;

    /**
     * Sets the Container
     *
     * @param DIContainerInterface $container A DIContainerInterface instance
     */
    public function setContainer(DIContainerInterface $container = null){
        $this->container = $container;
    }
}
