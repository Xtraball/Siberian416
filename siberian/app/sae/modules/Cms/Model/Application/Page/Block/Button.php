<?php

/**
 * Class Cms_Model_Application_Page_Block_Button
 *
 * @method $this setTypeId(string $typeId);
 * @method $this setLabel(string $label);
 * @method $this setHideNavbar(boolean $hide);
 * @method $this setUseExternalApp(boolean $useExternalApp);
 * @method $this setIcon(string $iconPath);
 * @method string getcontent()
 * @method $this setcontent(string $content)
 */
class Cms_Model_Application_Page_Block_Button extends Cms_Model_Application_Page_Block_Abstract
{

    /**
     * Cms_Model_Application_Page_Block_Button constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Cms_Model_Db_Table_Application_Page_Block_Button';
        return $this;
    }

    /**
     * @return bool|mixed
     */
    public function isValid()
    {
        return (boolean) $this->getContent();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function populate($data = [])
    {
        $icon = Siberian_Feature::saveImageForOptionDelete($this->option_value, $data['icon']);

        $this
            ->setTypeId($data['type'])
            ->setLabel($data['label'])
            ->setHideNavbar($data['hide_navbar'])
            ->setUseExternalApp($data['use_external_app'])
            ->setIcon($icon);

        switch ($data['type']) {
            case 'phone':
                    $this->setContent($data['phone']);
                break;
            case 'link':
                    $this->setContent($data['link']);
                break;
            case 'email':
                    $this->setContent($data['email']);
                break;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function forYaml ()
    {
        $yamlData = $this->getData();

        if (isset($yamlData['icon'])) {
            $yamlData['icon'] = (new Application_Model_Option_Value())
                ->__getBase64Image($yamlData['icon']);
        }

        return $yamlData;
    }
    
}