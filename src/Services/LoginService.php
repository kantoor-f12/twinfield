<?php

namespace PhpTwinfield\Services;

use PhpTwinfield\Exception;
use PhpTwinfield\Exceptions\AuthenticationException;
use PhpTwinfield\Secure\Config;

class LoginService extends BaseService
{
    /**
 * Login based on the config.
 *
 * @param Config $config
 * @throws Exception
 * @return string[]
 */
    public function getSessionIdAndCluster(Config $config): array
    {
        // Process logon
        if (!empty($config->getClientToken())) {
            $response = $this->OAuthLogon($config->getCredentials());
            $result = $response->OAuthLogonResult;
        } else {
            $response = $this->Logon($config->getCredentials());
            $result = $response->LogonResult;
        }

        // Check response is successful
        if ($result !== 'Ok') {
            throw new Exception("Failed logging in using the credentials.");
        }

        // Response from the logon request
        $loginResponse = $this->__getLastResponse();

        // Make a new DOM and load the response XML
        $envelope = new \DOMDocument();
        $envelope->loadXML($loginResponse);

        // Gets SessionID
        $sessionIdElements = $envelope->getElementsByTagName('SessionID');
        $sessionId = $sessionIdElements->item(0)->textContent;

        // Gets Cluster URL
        $clusterElements = $envelope->getElementsByTagName('cluster');
        $cluster = $clusterElements->item(0)->textContent;

        return [$sessionId, $cluster];
    }

    /**
     * Login based on the config.
     *
     * @param Config $config
     * @throws Exception
     * @return string[]
     */
    public function getRefreshAndAccessToken(Config $config): array
    {
        $configClientId = $config->getOpenIdDirectConnectCredentials()['clientId'];
        $configClientSecret = $config->getOpenIdDirectConnectCredentials()['clientSecret'];
        $configRefreshToken = $config->getOpenIdDirectConnectCredentials()['refreshToken'];

        $url = 'https://login.twinfield.com/auth/authentication/connect/token';
        $authString = base64_encode("$configClientId:$configClientSecret");

        // Setup cURL
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $authString",
                'Content-Type: application/x-www-form-urlencoded',
                'host: login.twinfield.com'
            ),
            CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=$configRefreshToken"
        ));

        // Send the request
        $response = curl_exec($ch);

        // Check for errors
        if($response === FALSE){
            die(curl_error($ch));
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);
        $refreshToken = $responseData['refresh_token'];
        $accessToken = $responseData['access_token'];

        return [$refreshToken, $accessToken];
    }

    public function getClusterAndExpire(string $accessToken): array {
        $url = "https://login.twinfield.com/auth/authentication/connect/accesstokenvalidation";

        // Setup cURL
        $ch = curl_init("$url?token=$accessToken");
        curl_setopt_array($ch, array(
            CURLOPT_POST => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'host: login.twinfield.com'
            )
        ));

        // Send the request
        $response = curl_exec($ch);

        // Check for errors
        if($response === FALSE){
            var_dump($response);
            throw new AuthenticationException("Something went wrong while retrieving the Cluster and AccessToken expire time from Twinfield");
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);

        $cluster = $responseData['twf.clusterUrl'];
        $expire = $responseData['exp'];

        return [$cluster, $expire];
    }

    protected function WSDL(): string
    {
        return "https://login.twinfield.com/webservices/session.asmx?wsdl";
    }
}