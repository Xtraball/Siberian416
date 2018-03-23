<?php

/**
 * Class Cms_Model_Application_Page_Block_Image
 */
class Cms_Model_Application_Page_Block_Image extends Cms_Model_Application_Page_Block_Image_Abstract {

    /**
     * Cms_Model_Application_Page_Block_Image constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Cms_Model_Db_Table_Application_Page_Block_Image';
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->getLibraryId()) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function forYaml ()
    {
        $yamlData = $this->getData();

        // Fetch all images
        $images = (new Cms_Model_Application_Page_Block_Image_Library())
            ->findAll(
                [
                    'library_id' => $this->getLibraryId()
                ],
                'image_id ASC'
            );

        $dataImages = [];
        foreach ($images as $image) {
            $dataImage = $image->getData();

            if (isset($dataImage['image_url'])) {
                $dataImage['image_url'] = (new Application_Model_Option_Value())
                    ->__getBase64Image($dataImage['image_url']);
            }

            if (isset($dataImage['image_fullsize_url'])) {
                $dataImage['image_fullsize_url'] = (new Application_Model_Option_Value())
                    ->__getBase64Image($dataImage['image_fullsize_url']);
            }

            $dataImages[] = $dataImage;
        }

        $yamlData['images'] = $dataImages;

        return $yamlData;
    }

}