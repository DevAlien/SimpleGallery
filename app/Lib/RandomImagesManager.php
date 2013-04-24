<?php
/**
 * The manager for the RandomImages, its in charge of loading the selected service and return the data
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */
namespace app\Lib;

use app\Lib\RandomImages\Exceptions\CouldNotFindSourceRadomImagesException;
use app\Lib\RandomImages\FlickrRandomImages;
use app\Lib\RandomImages\GoogleRandomImages;

/**
 * The manager for the RandomImages, its in charge of loading the selected service and return the data
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class RandomImagesManager {

    /**
     * Loads the right class, and get's the images
     *
     * @param string $source The source where you want the image from, Ex: Flickr
     */
    public function get($source) {
        $class = $this->getClass($source);
        $classFile = str_replace('\\', '/', $class) . '.php';
        if(file_exists($classFile)) {
            $randomImages = new $class();
            return $randomImages->get();
        }

        throw new CouldNotFindSourceRadomImagesException('Could not find ' . $source . ' in the available sources to download images', 1001);
    }

    /**
     * Given a source it return the class with the namespace
     *
     * @param string $source The source where you want the image from, Ex: Flickr
     */
    private function getClass($source) {
        return 'app\\Lib\\RandomImages\\' . ucfirst($source) . 'RandomImages';
    }
}