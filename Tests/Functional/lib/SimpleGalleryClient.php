<?php
/**
 * SimpleGalleryGallery is a client for the SimpleGallery project
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

include __DIR__ . '/http.php';

/**
 * SimpleGalleryGallery is a client for the SimpleGallery project
 *
 * All the occurent to use the SimpleGalleryApis
 * @version 1.0
 */
class SimpleGalleryClient extends Http {

    const RANDOM_GOOGLE = 'Google';
    const RANDOM_FLICKR = 'Flickr';

    /**
     * construct which sets the API url
     */
    public function __construct($url) {

        $this->setUrl($url);
    }

    /**
     * Get Random images from the API
     *
     * @param string $source From which source you want to load the images from
     * @return mixed;
     */
    public function getRandomImages($source = self::RANDOM_GOOGLE) {
        $response = $this->doRequest('/images/' . $source, Http::GET);
        $response = json_decode($response);

        if(is_object($response))
            return false;

        return $response;
    }

    /**
     * Get Favourites from APIs
     *
     * @return mixed
     */
    public function getFavourites() {
        $response = $this->doRequest('/favourites', Http::GET);
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Add a Favourite image via url
     *
     * @param string $url The URL of the image that you want to add to favourites
     * @return boolean
     */
    public function addFavourite($url = false) {
        $data = array();

        if($url !== false)
            $data = array('url' => $url);

        $response = $this->doRequest('/favourite', Http::POST, http_build_query($data));
        $response = json_decode($response, true);

        return $response;
    }
    
    /**
     * Edit a favourite image, you can modify the description
     *
     * @param string $id Id of the image that you want to delete
     * @param string $description The new description of the image
     * @return boolean
     */
    public function editFavourite($id, $description) {
        $response = $this->doRequest('/favourite/' . $id, Http::PUT, http_build_query(array('description' => $description)));
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Delete an image from Favourite
     *
     * @param string $id Id of the image that you want to delete
     * @return boolean
     */
    public function deleteFavourite($id) {
        $response = $this->doRequest('/favourite/' . $id, Http::DELETE);
        $response = json_decode($response, true);

        return $response;
    }
}