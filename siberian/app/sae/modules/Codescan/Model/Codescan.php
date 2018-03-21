<?php
class Codescan_Model_Codescan extends Core_Model_Default {

	protected $_is_cacheable = true;
	
    public function __construct($params = array()) {
        parent::__construct($params);
        return $this;
    }

    /**
     * @return array
     */
    public function getInappStates($value_id) {

        $in_app_states = array(
            array(
                "state" => "codescan",
                "offline" => false,
                "params" => array(
                    "value_id" => $value_id,
                ),
            ),
        );

        return $in_app_states;
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

            $dataset = [
                'option' => $currentOption->forYaml(),
            ];

            try {
                $result = Siberian_Yaml::encode($dataset);
            } catch(Exception $e) {
                throw new Exception("#CODESCAN-01: An error occured while exporting dataset to YAML.");
            }

            return $result;

        } else {
            throw new Exception("#CODESCAN-02: Unable to export the feature, non-existing id.");
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
            throw new Exception("#CODESCAN-03: An error occured while importing YAML dataset '$pathOrRawData'.");
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
                ->save()
            ;
        } else {
            throw new Exception("#CODESCAN-04: Missing option, unable to import data.");
        }
    }

}
