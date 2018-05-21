<?php

/**
 * Class Facebook_Model_Page
 */
class Facebook_Model_Page
{
    /**
     * @var int
     */
    public static $perPage = 25;

    /**
     * @var string
     */
    public $endpoint = 'https://graph.facebook.com/v3.0';

    /**
     * @var Application_Model_Application
     */
    public $application;

    /**
     * @var Facebook_Model_Api_Credentials
     */
    private $apiCredentials;

    /**
     * Facebook_Model_Page constructor.
     *
     * @param Application_Model_Application $application
     */
    public function __construct(Application_Model_Application $application)
    {
        $this->application = $application;
        $this->apiCredentials = Facebook_Model_Api_Credentials::getForApplication($this->getApplication());

        return $this;
    }

    /**
     * @param Application_Model_Application $application
     */
    public function setApplication (Application_Model_Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application_Model_Application
     */
    public function getApplication ()
    {
        return $this->application;
    }

    /**
     * Get the page informations from Graph API
     *
     * @param $pageIdentifier
     * @return array
     */
    public function get ($pageIdentifier)
    {
        $page = $this->_getPage($pageIdentifier);
        $posts = $this->_getPosts($pageIdentifier);

        return [
            'page' => $page,
            'posts' => $posts,
        ];
    }

    /**
     * @param $pageIdentifier
     * @param null $after
     * @return array|mixed
     * @throws Siberian_Exception
     */
    public function getPosts ($pageIdentifier, $after = null)
    {
        return $this->_getPosts($pageIdentifier, $after);
    }

    /**
     * @param $pageIdentifier
     * @return array|mixed
     * @throws Siberian_Exception
     */
    private function _getPage ($pageIdentifier)
    {
        $params = [
            'fields' => join(',', [
                'id',
                'link',
                'about',
                'name',
                'genre',
                'cover',
                'fan_count',
                'likes',
                'talking_about_count',
                'page_token',
            ]),
            'access_token' => $this->apiCredentials->getAccessToken()
        ];

        $endpoint = $this->endpoint . '/' . $pageIdentifier;
        $response = Siberian_Request::get($endpoint, $params);

        try {
            $page = Siberian_Json::decode($response);
        } catch (Exception $e) {
            throw new Siberian_Exception('Facebook_Page::001' .
                __('Something went wrong while retrieving your page informations.'));
        }

        return $page;
    }

    /**
     * @param $pageIdentifier
     * @param null $after
     * @return array|mixed
     * @throws Siberian_Exception
     */
    private function _getPosts ($pageIdentifier, $after = null)
    {
        $params = [
            'fields' => join(',', [
                'from',
                'message',
                'full_picture',
                'picture',
                'created_time',
                'likes.summary(true)',
                'comments.summary(true)',
                'type',
                'object_id',
                'name',
                'link',
            ]),
            'limit' => self::$perPage,
            'access_token' => $this->apiCredentials->getAccessToken()
        ];

        if ($after !== null) {
            $params['after'] = $after;
        }

        $endpoint = $this->endpoint . '/' . $pageIdentifier . '/posts';
        $response = Siberian_Request::get($endpoint, $params);

        try {
            $page = Siberian_Json::decode($response);
        } catch (Exception $e) {
            throw new Siberian_Exception('Facebook_Page::002' .
                __('Something went wrong while retrieving your page posts & comments.'));
        }

        return $page;
    }
}
