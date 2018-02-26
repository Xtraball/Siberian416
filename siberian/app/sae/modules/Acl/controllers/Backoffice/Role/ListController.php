<?php

/**
 * Class Acl_Backoffice_Role_ListController
 */
class Acl_Backoffice_Role_ListController extends Backoffice_Controller_Default{

    /**
     *
     */
    public function loadAction() {
        $payload = [
            'title' => __('Roles'),
            'icon' => 'fa-lock',
        ];

        $this->_sendJson($payload);
    }

    /**
     *
     */
    public function findallAction() {
        try {
            $roles = (new Acl_Model_Role())
                ->findAll();

            $defaultRole = (new Acl_Model_Role())
                ->findDefaultRoleId();

            $payload = [];
            foreach($roles as $role) {
                $isDefaultRole = false;
                if ($role->getId() == $defaultRole) {
                    $isDefaultRole = true;
                }

                $payload[] = [
                    'id' => $role->getId(),
                    'code' => $role->getCode(),
                    'label' => $role->getLabel(),
                    'level' => $role->getLevel(),
                    'default' => $isDefaultRole
                ];
            }
        } catch(Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
}