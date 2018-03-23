<?php

/**
 * Class Maps_Model_Maps
 */
class Maps_Model_Maps extends Core_Model_Default
{
    /**
     * Maps_Model_Maps constructor.
     * @param array $params
     */
    public function __construct($params = []) {
        parent::__construct($params);
        $this->_db_table = 'Maps_Model_Db_Table_Maps';
        return $this;
    }

    /**
     * @param integer $valueId
     * @return array
     */
    public function getInappStates($valueId) {
        $inAppStates = [
            [
                'state' => 'maps-view',
                'offline' => false,
                'params' => [
                    'value_id' => $valueId,
                ],
            ],
        ];

        return $inAppStates;
    }

    /**
     * @param $option_value
     * @return bool
     */
    public function getEmbedPayload($option_value) {

        $payload = array(
            "collection"    => array(),
            "page_title"    => $option_value->getTabbarName(),
            "icon_url"      => Core_Model_Lib_Image::sGetImage("maps/")
        );

        if($this->getId()) {

            /** Fallback/Fix for empty lat/lng */
            $lat = $this->getLatitude();
            $lng = $this->getLongitude();
            if(empty($lat) && empty($lng)) {
                $geo = Siberian_Google_Geocoding::getLatLng($this->getAddress());
                $this->setLatitude($geo[0]);
                $this->setLongitude($geo[1]);
            }

            $payload["collection"] = $this->getData();
        }

        return $payload;

    }

    /**
     * @deprecated
     *
     * @param $option
     * @return $this
     */
    public function copyTo($option)
    {
        $this
            ->setId(null)
            ->setValueId($option->getId())
            ->save();
        return $this;
    }

    /**
     * @param $option_value
     * @return array|string[]
     */
    public function getFeaturePaths($option_value)
    {
        $paths = parent::getFeaturePaths($option_value);

        $maps_icons = array("car.png", "walk.png", "bus.png", "error.png");
        $color = str_ireplace("#", "", $this->getApplication()->getBlock("list_item")->getColor());

        foreach($maps_icons as $maps_icon) {
            $btoa_image = base64_encode(Core_Model_Directory::getDesignPath(false) . '/images/maps/' . $maps_icon);

            $params = array(
                "color" => $color,
                "path" => $btoa_image
            );
            $paths[] = $this->getPath("/template/block/colorize/", $params);
        }

        return $paths;
    }

    /**
     * @param Application_Model_Option_Value $option
     * @param null $exportType
     * @param null $request
     * @return string
     * @throws Exception
     */
    public function exportAction(Application_Model_Option_Value $option, $exportType = null, $request = null)
    {
        if ($option && $option->getId()) {
            $currentOption = $option;
            $valueId = $currentOption->getId();

            // Events!
            $map = (new Maps_Model_Maps())
                ->find($valueId, 'value_id');

            if ($map->getId()) {
                $dataMap = $map->getData();
            }

            $dataset = [
                'option' => $currentOption->forYaml(),
                'map' => $dataMap,
            ];

            try {
                $result = Siberian_Yaml::encode($dataset);
            } catch(Exception $e) {
                throw new Exception("#MAPS-00: An error occured while exporting dataset to YAML.");
            }

            return $result;

        } else {
            throw new Exception("#MAPS-02: Unable to export the feature, non-existing id.");
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
            throw new Exception("#MAPS-03: An error occured while importing YAML dataset '$pathOrRawData'.");
        }

        $application = $this->getApplication();
        $applicationOption = new Application_Model_Option_Value();

        if (isset($dataset['option'])) {
            $option = $dataset['option'];
            $newApplicationOption = $applicationOption
                ->setData($option)
                ->unsData('value_id')
                ->unsData('id')
                ->setData('app_id', $application-> getId())
                ->save();

            $newApplicationOption
                ->_setBackgroundImage($option['background_image'], $newApplicationOption)
                ->_setBackgroundLandscapeImage($option['background_landscape_image'], $newApplicationOption)
                ->save();

            $newValueId = $newApplicationOption->getId();

            // Create the map!
            if (isset($dataset['map']) && $newValueId) {
                $newMap = (new Maps_Model_Maps())
                    ->setData($dataset['map'])
                    ->unsData('maps_id')
                    ->unsData('id')
                    ->setValueId($newValueId)
                    ->save();
            }

        } else {
            throw new Exception("#MAPS-04: Missing option, unable to import data.");
        }
    }

}
