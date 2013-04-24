<?php
/**
 * Container interface
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
namespace Devfw\DependencyInjection;

/**
 * Container interface
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
interface ContainerInterface {

	/**
     * Sets the Container
     *
     * @param DIContainerInterface $container A DIContainerInterface instance
     */
	function setContainer(DIContainerInterface $container = null);
}