<?php

/**
 * Class Booking2_Model_Booking
 */
class Booking2_Model_Booking extends Core_Model_Default
{

    /**
     * Booking2_Model_Booking constructor.
     * @param array $params
     * @throws Zend_Exception
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Booking_Model_Db_Table_Booking';
        return $this;
    }

    /**
     * @return array
     */
    public function getInappStates($valueId)
    {
        $inAppStates = [
            [
                "state" => "booking-view",
                "offline" => false,
                "params" => [
                    "value_id" => $valueId,
                ],
            ],
        ];

        return $inAppStates;
    }

    /**
     * @param $optionValue
     * @return bool
     */
    public function getEmbedPayload($optionValue)
    {

        $payload = [
            "stores" => [],
            "page_title" => $optionValue->getTabbarName()
        ];

        if ($this->getId()) {
            $store = new Booking_Model_Store();
            $stores = $store->findAll([
                "booking_id" => $this->getId()
            ]);

            foreach ($stores as $store) {
                $payload["stores"][] = [
                    "id" => $store->getId(),
                    "name" => $store->getStoreName()
                ];
            }
        }

        return $payload;
    }

    /**
     * @param $optionValue
     * @param $design
     * @param $category
     * @return bool|void
     */
    public function createDummyContents($optionValue, $design, $category)
    {

        $dummy_content_xml = $this->_getDummyXml($design, $category);

        $this->setValueId($optionValue->getId())->save();

        foreach ($dummy_content_xml->children() as $content) {
            $store = new Booking_Model_Store();

            foreach ($content->children() as $key => $value) {
                $store->addData((string)$key, (string)$value);
            }

            $store->setBookingId($this->getId())
                ->save();
        }
    }

    /**
     * @param $option
     * @return $this
     */
    public function copyTo($option)
    {
        $store = new Booking_Model_Store();
        $stores = $store->findAll(['booking_id' => $this->getId()]);

        $this->setId(null)
            ->setValueId($option->getId())
            ->save();

        foreach ($stores as $store) {
            $store->setId(null)
                ->setBookingId($this->getId())
                ->save();
        }

        return $this;
    }

    /**
     * @param $option Application_Model_Option_Value
     * @return string
     * @throws Exception
     */
    public function exportAction($option, $export_type = null)
    {
        if ($option && $option->getId()) {

            $current_option = $option;
            $valueId = $current_option->getId();

            $booking_model = new Booking_Model_Booking();
            $booking = $booking_model->find($valueId, "value_id");

            $store_model = new Booking_Model_Store();

            $stores = $store_model->findAll([
                "booking_id = ?" => $booking->getId(),
            ]);

            $stores_data = [];
            foreach ($stores as $store) {
                $store_data = $store->getData();

                if ($export_type === "safe") {
                    $store_data["store_name"] = "Praesent sed neque.";
                    $store_data["email"] = "test@lorem-ipsum.test";
                }

                $stores_data[] = $store_data;
            }

            $dataset = [
                "option" => $current_option->forYaml(),
                "booking" => $booking->getData(),
                "stores" => $stores_data,
            ];

            try {
                $result = Siberian_Yaml::encode($dataset);
            } catch (Exception $e) {
                throw new Exception("#100-00: An error occured while exporting dataset to YAML.");
            }

            return $result;

        } else {
            throw new Exception("#100-02: Unable to export the feature, non-existing id.");
        }
    }

    /**
     * @param $path
     * @throws Exception
     */
    public function importAction($path)
    {
        $content = file_get_contents($path);

        try {
            $dataset = Siberian_Yaml::decode($content);
        } catch (Exception $e) {
            throw new Exception("#100-03: An error occured while importing YAML dataset '$path'.");
        }

        $application = $this->getApplication();
        $application_option = new Application_Model_Option_Value();

        if (isset($dataset["option"])) {
            $new_application_option = $application_option
                ->setData($dataset["option"])
                ->unsData("value_id")
                ->unsData("id")
                ->setData('app_id', $application->getId())
                ->save();

            $new_value_id = $new_application_option->getId();

            $new_booking = new Booking_Model_Booking();
            $new_booking
                ->setData($dataset["booking"])
                ->unsData("booking_id")
                ->unsData("id")
                ->save();

            /** Create Stores */
            if (isset($dataset["stores"]) && $new_value_id && $new_booking->getId()) {

                foreach ($dataset["stores"] as $store) {

                    $new_store = new Booking_Model_Store();
                    $new_store
                        ->setData($store)
                        ->unsData("store_id")
                        ->unsData("id")
                        ->setData("booking_id", $new_booking->getId())
                        ->save();
                }

            }

        } else {
            throw new Exception("#100-04: Missing option, unable to import data.");
        }
    }
}
