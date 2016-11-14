<?php

namespace App\Helpers;

/**
 * @author Dariel de Jesus <darieldejesus@gmail.com>
 * @version 0.1
 *
 * This Helper contains all necessary functions to get coordinates from an IP.
 */
class IPLocator
{
    /**
     * @var string Contain the domain this helper uses to make the requests.
     * @see http://ip-api.com/ Web API which provide IP Info.
     */
    protected static $apiDomain = 'http://ip-api.com/json/';

    /**
     * Given a valid IP address, it would execute a remote API
     * and return an array with coordinate and location info.
     *
     * @param string $ipAddress Current IP address to be consulted.
     * @return boolean|Object If IP is not valid, FALSE. Otherwise, the object.
     */
    public static function getCoordinateFromIP($ipAddress) {
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return FALSE;
        }
        $command = self::$apiDomain . $ipAddress;
        $jsonString = self::executeCommand($command);
        if (!$jsonString) {
            return FALSE;
        }
        return json_decode($jsonString);
    }

    /**
     * Given a command, request it using cURL.
     *
     * @param string $command Command to be requested/executed.
     * @return boolean|string FALSE if command is empty or cURL error.
     *                        Otherwise, string would be returned.
     */
    private static function executeCommand($command) {
        if (empty($command)) {
            return FALSE;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $command);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        if ($error) {
            return FALSE;
        }
        curl_close($ch);
        return $response;
    }
}
