<?php
/**
 * Simple HTTP class to make calls to REST APIs
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

namespace app\Lib\RandomImages\Http;

/**
 * Simple HTTP class to make calls to REST APIs
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */
abstract class RandomImagesHttp {

    /**
     * the END point of the api
     *
     * @var string
     */
    private $url = '';

    const DELETE = 'DELETE';
    const PUT = 'PUT';
    const GET = 'GET';
    const PATCH = 'PATCH';
    const POST = 'POST';

    /**
     * Send a request to the server, receive a response
     *
     * @param  string   $path          Request url
     * @param  array    $parameters    Parameters
     * @param  string   $httpMethod    HTTP method to use
     * @param  array    $options       Request options
     *
     * @return string   HTTP response
     */
    final protected function doRequest($parameters, $type = RandomImagesHttp::GET, $postfield = '', $authtoken = null) {
        $this->url = '';
        $curlOptions = array();

        if (!empty($parameters))
            $this->url .= $parameters;

        $curlOptions += array(
            CURLOPT_URL => $this->url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6000
        );

        if (!empty($authtoken)){
            $curlOptions += array(CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded','Content-Length: ' . strlen($postfield), "Authorization: GoogleLogin auth=".$authtoken));
        }
        
        switch ($type) {
            case RandomImagesHttp::DELETE:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                $curlOptions[CURLOPT_POSTFIELDS] = '';
                break;
            case RandomImagesHttp::PUT:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $curlOptions[CURLOPT_POSTFIELDS] = $postfield;
                break;
            case RandomImagesHttp::PATCH:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                $curlOptions[CURLOPT_POSTFIELDS] = '';
                break;
            case RandomImagesHttp::POST:
                $curlOptions[CURLOPT_POSTFIELDS] = $postfield;
                break;
        }

        $response = $this->doCurlCall($curlOptions);

        if (!in_array($response['headers']['http_code'], array(0, 200, 201))) {
            //httperror
        }

        if ($response['errorNumber'] != '') {
            //errornumbere
        }

        return $response['response'];
    }

    /**
     * Make the actual CURL call and return the status
     *
     * @param array $curlOptions Array with the CURL options for this call
     *
     * @return array
     */
    final protected function doCurlCall(array $curlOptions) {
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt_array($curl, $curlOptions);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return compact('response', 'headers', 'errorNumber', 'errorMessage');
    }

    /**
     * Setter for url
     *
     * @param string $url The base url to make the calls to
     */
    final protected function setUrl($url) {
        $this->url = $url;
    }

}

?>