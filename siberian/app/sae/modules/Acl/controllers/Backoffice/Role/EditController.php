<?php

/**
 * Class Acl_Backoffice_Role_EditController
 */
class Acl_Backoffice_Role_EditController extends Backoffice_Controller_Default {

    /**
     * 
     */
    public function loadAction() {
        $payload = [
            'title' => __('Role'),
            'icon' => 'fa-lock',
            'words' => [
                'deleteTitle' => __('A confirmation is required!'),
                'deleteText' => __("You are about to remove the role %ROLE% and all it's subsequent roles") .
                    '<br />' .
                    __("All users using any of the deleted roles will be re-assigned the current <b>default role</b> immediatly."),
                'confirmDelete' => __('Yes, delete it!'),
                'cancelDelete' => __('No, go back.'),
            ],
        ];

        $this->_sendJson($payload);
    }

    /**
     * 
     */
    public function findAction() {
        $resourcesData = [];

        $parentRoles = (new Acl_Model_Role())
            ->findAll();

        $parentRolesList = [];
        foreach ($parentRoles as $parentRole) {
            $parentRolesList[$parentRole->getId()] = [
                'id' => $parentRole->getId(),
                'label' => __($parentRole->getLabel()),
                'code' => __($parentRole->getCode())
            ];
        }

        if ($this->getRequest()->getParam("role_id")) {
            $role = (new Acl_Model_Role())
                ->find($this->getRequest()->getParam("role_id"));
            
            $roleResources = (new Acl_Model_Resource())
                ->findResourcesByRole($this->getRequest()->getParam("role_id"));

            foreach($roleResources as $roleResource) {
                $resourcesData[] = $roleResource;
            }

            $dataTitle = __("Edit %s role", $role->getCode());
            $role = [
                'id' => (integer) $role->getId(),
                'code' => $role->getCode(),
                'label' => $role->getLabel(),
                'parent_id' => (integer) $role->getParentId(),
                'default' => $role->isDefaultRole()
            ];

            // Remove current role from List!
            unset($parentRolesList[$role['id']]);
        } else {
            $dataTitle = __("Create a new role");
            $role = [
                'code' => '',
                'label' => '',
                'parent_id' => (integer) 1
            ];
        }

        $payload = [
            'title' => $dataTitle,
            'role' => $role,
            'parentRoles' => $parentRolesList
        ];

        $resource = new Acl_Model_Resource();
        $payload['resources'] = $resource->getHierarchicalResources($resourcesData);

        $this->_sendJson($payload);
    }

    public function getresourcehierarchicalAction() {
        $resource = new Acl_Model_Resource();
        $hierarchical_resources = $resource->getHierarchicalResources();
        $this->_sendHtml($hierarchical_resources);
    }

    public function saveAction() {

        if ($param = Siberian_Json::decode($this->getRequest()->getRawBody())) {

            try {

                $role = new Acl_Model_Role();
                if (empty($param['role']) || !is_array($param['role'])) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $roleData = $param['role'];
                $resourcesData = !empty($param['resources']) ? $param['resources'] : [];

                if (isset($roleData['id'])) {
                    $role->find($roleData['id']);
                }

                $resourcesData = (new Acl_Model_Resource())
                    ->flattenedResources($resourcesData);

                $role
                    ->setResources($resourcesData)
                    ->setLabel($roleData['label'])
                    ->setParentId($roleData['parent_id'])
                    ->setCode($roleData['code'])
                    ->save();

                $config = (new System_Model_Config())
                    ->find(Acl_Model_Role::DEFAULT_ADMIN_ROLE_CODE, 'code');
                $defaultRoleId = $config->getValue();
                $newDefaultRoleId = null;
                
                if ($defaultRoleId === $role->getId() && !$roleData['default']) {
                    $newDefaultRoleId = Acl_Model_Role::DEFAULT_ROLE_ID;
                } else if($roleData['default']) {
                    if (__getConfig('is_demo')) {
                        // Demo version
                        throw new Siberian_Exception(__("This is a demo version, you are not allowed to change the default role."));
                    }

                    $newDefaultRoleId = $role->getId();
                }

                if (!empty($newDefaultRoleId)) {
                    $config
                        ->setValue($newDefaultRoleId)
                        ->save();
                }

                $payload = [
                    'success' => true,
                    'message' => __("Your role has been successfully saved")
                ];

            } catch(Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }

            $this->_sendJson($payload);

        }
    }

    public function deleteAction() {
        if($this->getRequest()->getParam("role_id")) {
            $role = new Acl_Model_Role();
            $role->find($this->getRequest()->getParam("role_id"));
            $role->delete();
            $data = array(
                "success" => true,
                "message" => $this->_("Your role has been successfully deleted")
            );
        } else {
            $data = array(
                "error" => true,
                "message" => $this->_("An error occurred while deleting your role. please try again later")
            );
        }
        $this->_sendHtml($data);
    }

}