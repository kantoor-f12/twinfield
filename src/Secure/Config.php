<?php
namespace PhpTwinfield\Secure;

/**
 * Config class
 *
 * Used to store the config information used to
 * generate a secure soap client and use the rest
 * of the application.
 *
 * There is no constructor.  You set all the values
 * with the method setCredentials();
 *
 * @since 0.0.1
 *
 * @package PhpTwinfield
 * @subpackage Secure
 * @author Leon Rowland <leon@rowland.nl>
 * @copyright (c) 2013, Leon Rowland
 * @version 0.0.1
 */
final class Config
{
    /**
     * @var string the address of the cluster. There is no need to set cluster in the scenario of logging in, the right
     * cluster will then be provided. In calls based on using existing credentials though, the cluster should be
     * provided.
     */
    public $cluster;

    /**
     * Holds all the login details for this
     * config object
     *
     * @access private
     * @var array
     */
    private $credentials = array(
        'user'         => '',
        'password'     => '',
        'organisation' => '',
        'office'       => ''
    );

    /**
     * Holds all the OAuth login details for this
     * config object
     *
     * @access private
     * @var array
     */
    private $oauthCredentials = array(
        'clientToken'     => '',
        'clientSecret' => ''
    );

    /**
     * Holds all the Credentials for OpenID login with a given refresh/accessToken for this
     * config object
     *
     * @access private
     * @var array
     */
    private $openIdDirectConnectCredentials = array(
        'clientId' => '',
        'clientSecret' => '',
        'refreshToken' => '',
        'accessToken' => '',
    );

    /**
     * Determines whether to use the built in login functionality or our own
     *
     * @var bool
     */
    private $legacyMode = true;

    /**
     * Holds all the OAuth class
     *
     * @access private
     */
    private $oauth = null;


    /**
     * Holds the optional soap client options
     *
     * @var array
     */
    private $soapClientOptions = array();

    /**
     * Sets the oAuth details for this config object.
     *
     * @access public
     * @param string $ct
     * @param string $cs
     * @param string $rURL
     * @param string $org
     * @param string $office
     * @param bool $autoRedirect
     * @param bool $clearSession
     * @return void
     */
    public function setOAuthParameters($ct, $cs, $rURL, $org, $office, bool $autoRedirect = false, bool $clearSession = false)
    {
        $this->oauthCredentials['clientToken'] = $ct;
        $this->oauthCredentials['clientSecret'] = $cs;
        $this->oauthCredentials['redirectURL'] = $rURL;
        $this->oauthCredentials['autoRedirect'] = $autoRedirect;
        $this->oauthCredentials['clearSession'] = $clearSession;
        $this->setOrganisationAndOffice($org, $office);
    }

    public function setOpenIdDirectConnectCredentials($clientId, $clientSecret, $refreshToken, $accessToken) {
        $this->openIdDirectConnectCredentials['clientId'] = $clientId;
        $this->openIdDirectConnectCredentials['clientSecret'] = $clientSecret;
        $this->openIdDirectConnectCredentials['refreshToken'] = $refreshToken;
        $this->openIdDirectConnectCredentials['accessToken'] = $accessToken;

        $this->legacyMode = false;
    }

    public function getOpenIdDirectConnectCredentials() {
        return $this->openIdDirectConnectCredentials;
    }

    /**
     * Gets the oAuth parameters of this config
     * object. It will create a new OAuth class
     * which will cause a redirect to twinfield
     * when $autoRedirect was set to true in
     * setOAuthParameters
     *
     * @since 0.0.1
     *
     * @access public
     * @return array
     */
    public function getOAuthParameters()
    {
        $this->oauth = new OAuth($this->oauthCredentials);
        return $this->oauth->getParameters();
    }
    /**
     * Sets the details for this config
     * object.
     *
     * @since 0.0.1
     *
     * @access public
     * @param string $username
     * @param string $password
     * @param string $organisation
     * @param int $office
     * @return void
     */
    public function setCredentials($username, $password, $organisation, $office)
    {
        $this->credentials['user']         = $username;
        $this->credentials['password']     = $password;
        $this->setOrganisationAndOffice($organisation, $office);
    }

    /**
     * Sets the organisation en office details for this config
     * object.
     *
     * @since 0.0.1
     *
     * @access public
     * @param string $organisation
     * @param int $office
     * @return void
     */

    protected function setOrganisationAndOffice($organisation, $office)
    {
        $this->credentials['organisation'] = $organisation;
        $this->credentials['office']       = $office;
    }

    /**
     * Returns the entire collection of details
     *
     * @since 0.0.1
     *
     * @access public
     * @return array
     */
    public function getCredentials()
    {
        if ($this->oauthCredentials['clientToken'] != '') {
            return $this->getOAuthParameters();
        } else {
            return $this->credentials;
        }
    }

    /**
     * Returns the set user
     *
     * @since 0.0.1
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->credentials['user'];
    }

    /**
     * Returns the set password
     *
     * @since 0.0.1
     *
     * @access public
     * @return string
     */
    public function getPassword()
    {
        return $this->credentials['password'];
    }

    /**
     * Returns the set organisation code
     *
     * @since 0.0.1
     *
     * @access public
     * @return string
     */
    public function getOrganisation()
    {
        return $this->credentials['organisation'];
    }

    /**
     * Returns the set office code
     *
     * @since 0.0.1
     *
     * @access public
     * @return string
     */
    public function getOffice()
    {
        return $this->credentials['office'];
    }

    /**
     * Returns the set clientToken
     *
     * @since 0.0.2
     *
     * @access public
     * @return string
     */
    public function getClientToken()
    {
        return $this->oauthCredentials['clientToken'];
    }

    /**
     * Returns the set clientSecret
     *
     * @since 0.0.2
     *
     * @access public
     * @return string
     */
    public function getClientSecret()
    {
        return $this->oauthCredentials['clientSecret'];
    }

    /**
     * Returns the set redirectURL
     *
     * @since 0.0.2
     *
     * @access public
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->oauthCredentials['redirectURL'];
    }

    public function getSoapClientOptions()
    {
        return $this->soapClientOptions;
    }


    public function isLegacyMode() {
        return $this->legacyMode;
    }

    /**
     * Set options that will be passed to the SoapClient constructor.
     *
     * Use this for setting a stream context or a proxy.
     *
     * @param array $options
     * @return $this
     */
    public function setSoapClientOptions(array $options)
    {
        $this->soapClientOptions = $options;
        return $this;
    }
}
