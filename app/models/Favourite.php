<?php
/**
 * Favourite Model, contains the fields of it and it valids the fields throwing exceptions
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

namespace app\models;

use app\models\Exceptions\InvalidFieldException;

/**
 * Favourite Model, contains the fields of it and it valids the fields throwing exceptions
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class Favourite {

    /**
     * ID of the Favourite
     *
     * @var int
     */
    private $id;

    /**
     * url of the Favourite
     *
     * @var string
     */
    private $url;

    /**
     * Description of the Favourite
     *
     * @var string
     */
    private $description;

    /**
     * Getter for the Id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Setter for the ID
     *
     * @param int $id The ID of the Favourite
     *
     * @return \app\models\Favourite
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter for the URL
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Setter for the URL
     *
     * @param string $url The URL of the Favourite
     *
     * @return \app\models\Favourite
     */
    public function setUrl($url) {
        if(!$this->isValidUrl($url))
            throw new InvalidFieldException('The URL provided is not a valid URL', 2000);
        
        $this->url = $url;

        return $this;
    }

    /**
     * Getter for the Description
     *
     * @return \app\models\Favourite
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Setter for the ID
     *
     * @param string $description The description of the Favourite
     *
     * @return \app\models\Favourite
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Check if the URL is a valid URL
     *
     * @param string $url The URL of the Favourite
     *
     * @return boolean
     */
    private function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}