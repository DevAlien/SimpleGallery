<?php
/**
 * Flickr RandomImages provider
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */
namespace app\Lib\RandomImages;

use app\Lib\RandomImages\Http\RandomImagesHttp;
use app\Lib\RandomImages\RandomImagesInterface;
use app\Lib\RandomImages\Exceptions\CouldNotDownloadRadomImagesException;

/**
 * Flickr RandomImages provides 20 images from Flickr images
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @version 1.0
 */
class FlickrRandomImages extends RandomImagesHttp implements RandomImagesInterface {

    const URL = 'http://api.flickr.com/services/feeds/photos_public.gne?format=json';

    /**
     * Get Flickr Images from the given URL.
     *
     * @param string $url The URL where we want to make the request to.
     *
     * @return array
     */
    private function getImages($url){
        $response = $this->doRequest($url, self::GET);
        $response = json_decode(ereg_replace("^jsonFlickrFeed\((.*)\)$", "\\1", stripslashes(strip_tags($response))));

        $images = array();
        if(is_object($response)) {
            $items = $response->items;

            foreach($items as $item){
            $images[] = $item->media->m;
            }
            return $images;
        }
        
        throw new CouldNotDownloadRadomImagesException("Could not download images from Flickr", 1000);
    }

    /**
     * Get 20 Images from Flickr
     *
     * @return array
     */
    public function get() {
        return $this->getImages(self::URL);
    }
}