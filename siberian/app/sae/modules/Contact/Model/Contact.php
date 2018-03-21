<?php
class Contact_Model_Contact extends Core_Model_Default {

    /**
     * @var array
     */
    public $cache_tags = array(
        "feature_contact",
    );

    protected $_is_cacheable = true;

    public function __construct($params = []) {
        parent::__construct($params);
        $this->_db_table = 'Contact_Model_Db_Table_Contact';
        return $this;
    }

    /**
     * @return string full,none,partial
     */
    public function availableOffline() {
        return "partial";
    }

    /**
     * @param $option_value
     * @return bool
     */
    public function getEmbedPayload($option_value) {

        $payload = false;

        if($this->getId()) {

            $cover_b64 = null;
            if($this->getCoverUrl()) {
                $cover_path = Core_Model_Directory::getBasePathTo($this->getCoverUrl());
                $image = Siberian_Image::open($cover_path)->cropResize(720);
                $cover_b64 = $image->inline($image->guessType());
            }

            $payload = array(
                "contact" => array(
                    "name"          => $this->getName(),
                    "cover_url"     => $cover_b64,
                    "street"        => $this->getStreet(),
                    "postcode"      => $this->getPostcode(),
                    "city"          => $this->getCity(),
                    "description"   => $this->getDescription(),
                    "phone"         => $this->getPhone(),
                    "email"         => $this->getEmail(),
                    "form_url"      => __path("contact/mobile_form/index", array("value_id" => $option_value->getId())),
                    "website_url"   => $this->getWebsite(),
                    "facebook_url"  => $this->getFacebook(),
                    "twitter_url"   => $this->getTwitter()
                ),
                "page_title" => $option_value->getTabbarName()
            );

            if($this->getLatitude() && $this->getLongitude()) {
                $payload['contact']["coordinates"] = array(
                    "latitude"      => $this->getLatitude(),
                    "longitude"     => $this->getLongitude()
                );
            }

        }

        return $payload;

    }


    /**
     * @return array
     */
    public function getInappStates($value_id) {

        $in_app_states = array(
            array(
                "state" => "contact-view",
                "offline" => true,
                "params" => array(
                    "value_id" => $value_id,
                ),
                "childrens" => array(
                    array(
                        "label" => __("Form"),
                        "state" => "contact-form",
                        "offline" => true,
                        "params" => array(
                            "value_id" => $value_id,
                        ),
                    ),
                    array(
                        "label" => __("Map"),
                        "state" => "contact-map",
                        "offline" => false,
                        "params" => array(
                            "value_id" => $value_id,
                        ),
                    ),
                ),
            ),
        );

        return $in_app_states;
    }

    /**
     * @param $option_value
     * @return array
     */
    public function getFeaturePaths($option_value) {
        return array();
        /**if(!$this->isCacheable()) {
            return array();
        }

        $value_id = $option_value->getId();
        $cache_id = "feature_paths_valueid_{$value_id}";
        if(!$result = $this->cache->load($cache_id)) {

            $paths = array();
            $paths[] = $option_value->getPath("find", array("value_id" => $option_value->getId()), false);

            $this->cache->save($paths, $cache_id,
                $this->cache_tags + array(
                "feature_paths",
                "feature_paths_valueid_{$value_id}"
            ));
        } else {
            $paths = $result;
        }

        return $paths;*/
    }

    /**
     * @param $option_value
     * @return array
     */
    public function getAssetsPaths($option_value) {
        if(!$this->isCacheable()) {
            return array();
        }

        $paths = array();

        $value_id = $option_value->getId();
        $cache_id = "assets_paths_valueid_{$value_id}";
        if(!$result = $this->cache->load($cache_id)) {

            if($cover = $this->getCoverUrl()) {
                $paths[] = $cover;
            }

            $matches = array();
            $regex_url = "/((?:http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?:\/[^\s\"]*)\.(?:png|gif|jpeg|jpg)+)+/";
            preg_match_all($regex_url, $this->getDescription(), $matches);

            $matches = call_user_func_array('array_merge', $matches);

            if($matches && count($matches) > 1) {
                unset($matches[0]);
                $paths = array_merge($paths, $matches);
            }

            $this->cache->save($paths, $cache_id,
                $this->cache_tags + array(
                "assets_paths",
                "assets_paths_valueid_{$value_id}"
            ));
        } else {
            $paths = $result;
        }

        return $paths;
    }

    public function getCoverUrl() {
        $cover_path = Application_Model_Application::getImagePath().$this->getCover();
        $base_cover_path = Application_Model_Application::getBaseImagePath().$this->getCover();
        if($this->getCover() AND file_exists($base_cover_path)) {
            return $cover_path;
        }
        return '';
    }


    /**
     * @param $option
     * @param null $exportType
     * @param null $request
     * @return string
     * @throws Exception
     */
    public function exportAction($option, $exportType = null, $request = null)
    {
        if ($option && $option->getId()) {

            $currentOption = $option;

            $contact = (new Contact_Model_Contact())
                ->find($currentOption->getId(), 'value_id');

            $contactData = $contact->getData();
            switch ($exportType) {
                case 'safe':
                        $contactData['name'] = 'John DOE';
                        $contactData['description'] = 'A fictitious name used in legal documents for an unknown or anonymous male person.';
                        $contactData['facebook'] = 'https://www.facebook.com/john.doe';
                        $contactData['twitter'] = 'https://twitter.com/john.doe';
                        $contactData['website'] = 'https://john-doe.com';
                        $contactData['cover'] = null;
                        $contactData['email'] = 'john.doe@example.com';
                        $contactData['civility'] = 'Mr.';
                        $contactData['firstname'] = 'John';
                        $contactData['street'] = 'Paradise Avenue';
                        $contactData['postcode'] = '1337';
                        $contactData['city'] = 'Nowhere';
                        $contactData['latitude'] = null;
                        $contactData['longitude'] = null;
                        $contactData['phone'] = '0987654321';
                    break;
            }

            $dataset = [
                'option' => $currentOption->forYaml(),
                'contact' => $contactData,
            ];

            try {
                $result = Siberian_Yaml::encode($dataset);
            } catch(Exception $e) {
                throw new Exception("#CONTACT-01: An error occured while exporting dataset to YAML.");
            }

            return $result;

        } else {
            throw new Exception("#CONTACT-02: Unable to export the feature, non-existing id.");
        }
    }

    /**
     * @param string $pathOrRawData
     * @throws Exception
     */
    public function importAction($pathOrRawData)
    {
        if (is_file($pathOrRawData)) {
            $content = file_get_contents($pathOrRawData);
        } else {
            $content = $pathOrRawData;
        }

        try {
            $dataset = Siberian_Yaml::decode($content);
        } catch(Exception $e) {
            throw new Exception("#CONTACT-03: An error occured while importing YAML dataset '$pathOrRawData'.");
        }

        $application = $this->getApplication();
        $applicationOption = new Application_Model_Option_Value();

        if (isset($dataset['option'])) {

            $realOption = (new Application_Model_Option())
                ->find($dataset['option']['code'], 'code');

            $newApplicationOption = $applicationOption
                ->setData($dataset['option'])
                ->setData('option_id', $realOption->getId())
                ->unsData('value_id')
                ->unsData('id')
                ->setData('app_id', $application->getId())
                ->save();

                if (isset($dataset['contact'])) {
                    (new Contact_Model_Contact())
                        ->setData($dataset['contact'])
                        ->unsData('contact_id')
                        ->unsData('value_id')
                        ->unsData('id')
                        ->setData('value_id', $newApplicationOption->getId())
                        ->save();
                }
            ;
        } else {
            throw new Exception("#CONTACT-04: Missing option, unable to import data.");
        }
    }

}
