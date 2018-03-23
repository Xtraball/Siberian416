<?php

/**
 * Class Cms_Model_Application_Page_Block_File
 *
 * @method string getName()
 * @method $this setLabel(string $label)
 * @method $this setName(string $name)
 * @method $this setOriginalName(string $originalName)
 */
class Cms_Model_Application_Page_Block_File extends Cms_Model_Application_Page_Block_Abstract
{
    /**
     * Cms_Model_Application_Page_Block_File constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Cms_Model_Db_Table_Application_Page_Block_File';
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (boolean) $this->getName();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function populate($data = [])
    {
        $file = Siberian_Feature::saveFileForOption($this->option_value, $data['file']);

        $this
            ->setLabel($data['label'])
            ->setOriginalName($data['original_name'])
            ->setName($file);

        return $this;
    }

    /**
     * @return array
     */
    public function forYaml ()
    {
        $yamlData = $this->getData();

        if (isset($yamlData['name'])) {
            $path = Core_Model_Directory::getBasePathTo('/images/application' . $yamlData['name']);
            $yamlData['name'] = fileToBase64($path);
        }

        return $yamlData;
    }

}