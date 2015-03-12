<?php
/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          http://recaptcha.net/api/getkey
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * RSDirectory reCAPTCHA helper.
 *
 * @abstract
 */
abstract class RSDirectoryReCAPTCHA
{
    const API_SERVER = 'http://www.google.com/recaptcha/api';
    const API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
    const VERIFY_SERVER = 'www.google.com';
        
    /**
     * Encodes the given data into a query string format.
     *
     * @access public
     *
     * @static
     * 
     * @param $data - array of string elements to be encoded
     * 
     * @return string - encoded request
     */
    public static function qsencode($data)
    {
        $req = '';
            
        foreach ($data as $key => $value)
        {
            $req .= "$key=" . urlencode( stripslashes($value) ) . '&';
        }
            
        // Cut the last '&'.
        $req = substr( $req, 0, strlen($req) - 1 );
            
        return $req;
    }
        
    /**
     * Submits an HTTP POST to a reCAPTCHA server.
     *
     * @access public
     *
     * @static
     * 
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * 
     * @return array response
     */
    public static function post($host, $path, $data, $port = 80)
    {
        $req = self::qsencode ($data);
            
        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;
            
        $response = '';
            
        if ( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) )
        {
            JError::raiseWarning(500, "RSDirectory! Pro ReCAPTCHA: Could not open socket. Please check that your server can connect to $host");
            return false;
        }
            
        fwrite($fs, $http_request);
            
        while ( !feof($fs) )
        {
            $response .= fgets($fs, 1160); // One TCP-IP packet
        }
            
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);
            
        return $response;
    }
        
    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * 
     * This is called from the browser, and the resulting reCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     *
     * @access public
     *
     * @static
     * 
     * @param string $public_key A public key for reCAPTCHA
     * @param string $error The error given by reCAPTCHA (optional, default is null)
     * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
     *
     * @return string - The HTML to be embedded in the user's form.
     */
    public static function getHTML($public_key, $theme = 'Blackglass', $error = null, $use_ssl = false)
    {
        // Initialize the return string.
        $return = '';
        
        if (!$public_key)
        {
            $return .= 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>';
            return;
        }
            
        $return .= '<script type="text/javascript">var RecaptchaOptions = { theme : \'' . $theme . '\' };</script>';
            
        $jconfig = new JConfig();
            
        $use_ssl = $jconfig->force_ssl == 2;
            
        if ($use_ssl)
        {
            $server = self::API_SECURE_SERVER;
        }
        else
        {
            $server = self::API_SERVER;
        }
            
        $errorpart = '';
            
        if ($error)
        {
           $errorpart = "&amp;error=$error";
        }
            
        $return .= '<script type="text/javascript" src="' . $server . '/challenge?k=' . $public_key . $errorpart . '"></script>
                    <noscript>
                        <iframe src="' . $server . '/noscript?k=' . $public_key . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br />
                        <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                        <input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
                    </noscript>';
                        
        return $return;
    }
        
    /**
      * Calls an HTTP POST function to verify if the user's guess was correct.
      *
      * @access public
      *
      * @static
      * 
      * @param string $private_key
      * @param string $remoteip
      * @param string $challenge
      * @param string $response
      * @param array $extra_params an array of extra variables to post to the server
      * 
      * @return ReCaptchaResponse
      */
    public static function checkAnswer( $private_key, $remoteip, $challenge, $response, $extra_params = array() )
    {
        if (!$private_key)
        {
            JError::raiseWarning(500, 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>');
            return false;
        }
            
        if (!$remoteip)
        {
            JError::raiseWarning(500, 'For security reasons, you must pass the remote IP to reCAPTCHA. We could not detect your IP.');
            return false;
        }
            
        //discard spam submissions
        if (!$challenge || !$response)
            return (object)array(
                'is_valid' => false,
                'error' => 'incorrect-captcha-sol',
            );
            
            
        $response = self::post( self::VERIFY_SERVER, '/recaptcha/api/verify',
            array (
                'privatekey' => $private_key,
                'remoteip' => $remoteip,
                'challenge' => $challenge,
                'response' => $response
            ) + $extra_params
        );
            
        $answers = explode("\n", $response[1]);
        $recaptcha_response = new stdClass();
            
        if ( trim($answers[0]) == 'true' )
        {
            return (object)array(
                'is_valid' => true,
                'error' => '',
            );
        }
        else
        {
            return (object)array(
                'is_valid' => false,
                'error' => isset($answers[1]) ? $answers[1] : '',
            );
        }
    }
}