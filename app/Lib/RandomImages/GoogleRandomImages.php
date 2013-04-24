<?php
/**
 * Google RandomImages provider
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
namespace app\Lib\RandomImages;

use app\Lib\RandomImages\Http\RandomImagesHttp;
use app\Lib\RandomImages\RandomImagesInterface;
use app\Lib\RandomImages\Exceptions\CouldNotDownloadRadomImagesException;

/**
 * Google RandomImages provides 20 images from Google images
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class GoogleRandomImages extends RandomImagesHttp implements RandomImagesInterface {

    const URL = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=';
    
    /**
     * Some words to search, this is because google images doesn't have a random search, so I search in this words and in different pages.
     *
     * @var array
     */
    private $words = array("Kuala Lumpur", "Petronas Tower", "Zurich", "London", "New York", "Rome", "Miami", "Tokyo", "Milan", "Sydney", "Paris", "Berlin");

    /**
     * Get Images from the given URL.
     *
     * @param string $url The URL where we want to make the request to.
     * @param int $start The page for google images that we want to get
     * @param string $keyword The Keyword that we want to search
     *
     * @return array
     */
    private function getGoogleImages($url, $start, $keyword) {
        $response = $this->doRequest($url . $keyword . '&start=' . $start, self::GET);
        $response = json_decode($response);

        if(is_object($response)) {
            $images = $response->{'responseData'}->{'results'};
            return $images;
        }

        throw new CouldNotDownloadRadomImagesException("Could not download images from Google", 1000);
    }
    
    /**
     * Get Images from Gofle
     *
     * @param string $url The URL where we want to make the request to.
     * @param string $keyword The Keyword that we want to search
     *
     * @return array
     */
    private function getImages($url, $keyword) { 
        $images = array();
        for ($x = 0;$x < 5;$x++) {
             $images = array_merge($images,$this->getGoogleImages($url, ($x*4), $keyword));
        }

       	$pictures = array();
        for ($x = 0;$x < count($images);$x++) {
            $pictures[] = $images[$x]->{'unescapedUrl'};
        }
        return $pictures;
    }

    /**
     * Get 20 Images from Google
     *
     * @return array
     */
    public function get() {
        $key = array_rand($this->words, 1);
        $keyword = $this->words[$key];
        return $this->getImages(self::URL, $keyword);
    }
}