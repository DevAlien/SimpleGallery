<?php
/**
 * Simple HTTP class to make calls to REST APIs
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @copyright Goncalo Margalho (C) 2013
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */

/**
 * Simple HTTP class to make calls to REST APIs
 *
 * @version 1.0
 */
abstract class Http {

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
    final protected function doRequest($parameters, $type = Http::GET, $postfield = '', $authtoken = null) {
        $curlOptions = array();
        $url = $this->url;

        if (!empty($parameters))
            $url .= $parameters;

        $curlOptions += array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'SimpleGalleryHTTPClient',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 600
        );
        
        if (!empty($authtoken)){
            $curlOptions += array(CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded','Content-Length: ' . strlen($postfield), "Authorization: GoogleLogin auth=".$authtoken));
        }
        
        switch ($type) {
            case Http::DELETE:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                $curlOptions[CURLOPT_POSTFIELDS] = '';
                break;
            case Http::PUT:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $curlOptions[CURLOPT_POSTFIELDS] = $postfield;
                break;
            case Http::PATCH:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                $curlOptions[CURLOPT_POSTFIELDS] = '';
                break;
            case Http::POST:
                $curlOptions[CURLOPT_POSTFIELDS] = $postfield;
                break;
        }

        $response = $this->doCurlCall($curlOptions);
        if (!in_array($response['headers']['http_code'], array(0, 200, 201))) {
            echo 'error: ' . $response['headers']['http_code'];
        }

        if ($response['errorNumber'] != '') {
            //print_r ($response);
        }

        return $response['response'];
    }

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

    final protected function setUrl($url) {
        $this->url = $url;
    }

    final protected function getUrl() {
        return $this->url;
    }

}