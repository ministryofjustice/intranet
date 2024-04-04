<?php

namespace MOJ\Intranet;

// Do not allow access outside WP
defined('ABSPATH') || exit;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Auth {

    private $publicKey = '';
    private $privateKey = '';
    private $jwt = null;

    public function __construct() {
        $this->publicKey = $_ENV['JWT_PUBLIC_KEY'];
        $this->privateKey = $_ENV['JWT_PRIVATE_KEY'];
        // Clear JWT_PUBLIC_KEY & JWT_PRIVATE_KEY from memory. 
        // They're not required elsewhere in the app.
        unset($_ENV['JWT_PUBLIC_KEY']);
        unset($_ENV['JWT_PRIVATE_KEY']);

        $this->handlePageRequest();
    }

    /**
     * Checks if a given IP address matches the specified CIDR subnet/s
     * 
     * @see https://gist.github.com/tott/7684443?permalink_comment_id=2108696#gistcomment-2108696
     * 
     * @param string $ip The IP address to check
     * @param mixed $cidrs The IP subnet (string) or subnets (array) in CIDR notation
     * @param string $match optional If provided, will contain the first matched IP subnet
     * @return boolean TRUE if the IP matches a given subnet or FALSE if it does not
     */
    public function ipMatch($ip, $cidrs, &$match = null) {
        foreach((array) $cidrs as $cidr) {
            $parts = explode('/', $cidr);
            $subnet = $parts[0];
            $mask = $parts[1] ?? 32;
            if(((ip2long($ip) & ($mask = ~ ((1 << (32 - $mask)) - 1))) == (ip2long($subnet) & $mask))) {
                $match = $cidr;
                return true;
            }
        }
        return false;
    }

    public function ipAddressIsAllowed() 
    {

        if(empty($_ENV['ALLOWED_IPS']) || empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        $allowedIps = array_map('trim', explode(',', $_ENV['ALLOWED_IPS']) );

        error_log($_SERVER['REMOTE_ADDR']);
        error_log(print_r($allowedIps, true));

        return $this->ipMatch($_SERVER['REMOTE_ADDR'], $allowedIps);
    }

    public function handlePageRequest() {
        // Is there a valid JWT token in the request?

        // Maybe extend the JWT token expiry time.

        // If not, is the IP address allowed?
        if($this->ipAddressIsAllowed()) {
            // Set a cookie with the JWT token.
            $this->setJwt();
            error_log('IP address allowed');
            return;
        } 

        error_log('IP address not allowed');


        // Redirect to login page

    }

    public function setJwt() {
        // Set a cookie with the JWT token

        $payload = [
            'iss' => 'example.org',
            'aud' => 'example.com',
            'iat' => 1356999524,
            'nbf' => 1357000000
        ];

        $this->jwt = JWT::encode($payload, $this->privateKey, 'RS256');

        header('Set-Cookie: jwt=' . $this->jwt . '; path=/; secure; HttpOnly');

    }



    // public function jwt() {
    //     $publicKey = $_ENV['JWT_PUBLIC_KEY'];
    //     $privateKey = $_ENV['JWT_PRIVATE_KEY'];

    //     $payload = [
    //         'iss' => 'example.org',
    //         'aud' => 'example.com',
    //         'iat' => 1356999524,
    //         'nbf' => 1357000000
    //     ];

    //     $jwt = JWT::encode($payload, $privateKey, 'RS256');
    //     error_log("Encode:\n" . print_r($jwt, true));

    //     $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));

    //     /*
    //      NOTE: This will now be an object instead of an associative array. To get
    //      an associative array, you will need to cast it as such:
    //     */

    //     $decoded_array = (array) $decoded;
    //     error_log("Decode:\n" . print_r($decoded_array, true) );
    // }


}

$auth = new Auth();
// $auth->handlePageRequest();



// $publicKey = $_ENV['JWT_PUBLIC_KEY'];
// $privateKey = $_ENV['JWT_PRIVATE_KEY'];

// $payload = [
//     'iss' => 'example.org',
//     'aud' => 'example.com',
//     'iat' => 1356999524,
//     'nbf' => 1357000000
// ];

// $jwt = JWT::encode($payload, $privateKey, 'RS256');
// error_log("Encode:\n" . print_r($jwt, true));

// $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));

// /*
//  NOTE: This will now be an object instead of an associative array. To get
//  an associative array, you will need to cast it as such:
// */

// $decoded_array = (array) $decoded;
// error_log("Decode:\n" . print_r($decoded_array, true) );