<?php

/**
 * Class Facebook_Model_Api_Credentials
 */
class Facebook_Model_Api_Credentials extends Core_Model_Default
{
    /**
     * @var string
     */
    public $endpoint = 'https://graph.facebook.com/v3.0';

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var null|string
     */
    private $accessToken = null;

    /**
     * @param Application_Model_Application $application
     * @return Facebook_Model_Api_Credentials
     */
    public static function getForApplication (Application_Model_Application $application)
    {
        $instance = new self();

        $instance->clientId = $application->getFacebookId();
        $instance->clientSecret = $application->getFacebookKey();

        return $instance;
    }

    /**
     * @return null|string
     * @throws Siberian_Exception
     */
    public function getAccessToken ()
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $response = Siberian_Request::get($this->endpoint . '/oauth/access_token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        try {
            $result = Siberian_Json::decode($response);

            $this->accessToken = $result['access_token'];

            return $this->accessToken;
        } catch (Exception $e) {
            throw new Siberian_Exception(__('An error occurred while retrieving Facebook token. %s', $e->getMessage()));
        }
    }
}
