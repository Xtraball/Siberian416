<?php

/**
 * Class Cms_Model_Application_Page_Block_Video
 *
 * @method string getTypeId()
 * @method $this setTypeId(string $typeId)
 * @method string getDescription()
 * @method $this setDescription(string $description)
 * @method $this setImage(string $imagePath)
 * @method integer getVideoId()
 */
class Cms_Model_Application_Page_Block_Video extends Cms_Model_Application_Page_Block_Abstract {

    /**
     * @var array
     */
    protected $_types = [
        1 => 'link',
        2 => 'youtube',
        3 => 'podcast',
    ];

    /**
     * @var Cms_Model_Application_Page_Block_Video_*
     */
    protected $_type_instance;

    /**
     * Cms_Model_Application_Page_Block_Video constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = "Cms_Model_Db_Table_Application_Page_Block_Video";
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function populate($data = [])
    {
        $this->setTypeId($data['type']);

        if ($this->getTypeInstance()) {
            if ($data['type'] === 'link') {
                $data['image'] = $this->saveImage($data['cover_image']);
                $this->setImage($data['image']);
                $this->setDescription($data['description']);
            }
            $this
                ->getTypeInstance()
                ->setOptionValue($this->option_value)
                ->populate($data);
        }

        return $this;
    }

    /**
     * @param mixed $id
     * @param null $field
     * @return $this|null
     */
    public function find($id, $field = null)
    {
        parent::find($id, $field);

        return $this;
    }

    /**
     * Return if the instance is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getTypeInstance() ?
            $this->getTypeInstance()->isValid() : false;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        $local = $this->getData('image');
        if (empty($local)) {
            $local = $this
                ->getTypeInstance()
                ->getData('image');
        }

        return $local;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        if ($this->isValid()) {
            return $this
                ->getTypeInstance()
                ->getImageUrl();
        }
        return '';
    }

    /**
     * @return null
     */
    public function getTypeInstance()
    {
        if (!$this->_type_instance) {
            $type = $this->getTypeId();
            if (in_array($type, $this->_types)) {
                $class = 'Cms_Model_Application_Page_Block_Video_'.ucfirst($type);

                $this->_type_instance = (new $class())
                    ->find($this->getId());
                $this->_type_instance
                    ->addData($this->getData());
            }
        }

        return !empty($this->_type_instance) ?
            $this->_type_instance : null;
    }

    /**
     * @deprecated
     *
     * @param $search
     * @param null $id
     * @return mixed
     */
    public function getList($search, $id = null)
    {
        return $this->getTypeInstance()->getList($search, $id);
    }

    public function save_v2()
    {
        parent::save();

        if ($this->getTypeInstance()) {
            $this
                ->getTypeInstance()
                ->setVideoId($this->getId())
                ->save();
        }

        return $this;
    }

    /**
     * @deprecated should be replaced with save_v2/renamed
     * @return $this
     */
    public function save()
    {
        parent::save();
        if (!$this->getIsDeleted()) {
            if ($this->getTypeInstance()->getId()) {
                $this
                    ->getTypeInstance()
                    ->delete();
            }
            $this
                ->getTypeInstance()
                ->setData($this->_getTypeInstanceData())
                ->setVideoId($this->getId())
                ->save();
        }

        return $this;
    }

    /**
     * @deprecated
     * @return $this
     */
    protected function _addTypeDatas()
    {
        if ($this->getTypeInstance() AND $this->getTypeInstance()->getId()) {
            $this
                ->addData($this->getTypeInstance()->getData());
        }

        return $this;
    }

    /**
     * @deprecated
     * @return array
     */
    protected function _getTypeInstanceData()
    {
        $fields = $this->getTypeInstance()->getFields();
        $datas = [];
        foreach ($fields as $field) {
            $datas[$field] = $this->getData($field);
        }

        return $datas;
    }

    /**
     * @return array
     */
    public function forYaml ()
    {
        $yamlData = $this->getData();

        if (isset($yamlData['image'])) {
            $yamlData['image'] = (new Application_Model_Option_Value())
                ->__getBase64Image($yamlData['image']);
        }

        // Fetch the type!
        switch ($this->getTypeId()) {
            case 'link':
                    $link = (new Cms_Model_Application_Page_Block_Video_Link())
                        ->find($this->getVideoId(), 'video_id');

                    $data = $link->getData();

                    if (isset($data['image'])) {
                        $data['image'] = (new Application_Model_Option_Value())
                            ->__getBase64Image($data['image']);
                    }

                break;
            case 'podcast':
                    $podcast = (new Cms_Model_Application_Page_Block_Video_Podcast())
                        ->find($this->getVideoId(), 'video_id');
                    $data = $podcast->getData();
                break;
            case 'youtube':
                    $youtube = (new Cms_Model_Application_Page_Block_Video_Youtube())
                        ->find($this->getVideoId(), 'video_id');
                    $data = $youtube->getData();
                break;
        }

        $yamlData['block_type'] = [
            'type' => $this->getTypeId(),
            'block' => $data
        ];

        return $yamlData;
    }

}