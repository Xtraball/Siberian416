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
            'words' => [
                'deleteTitle' => __('A confirmation is required!'),
                'deleteText' => __("You are about to remove the role #ROLE# and all it's subsequent roles<br />All users using any of the deleted roles will be re-assigned the current <b>default role</b> immediatly."),
                'confirmDelete' => __('Yes, delete it!'),
                'cancelDelete' => __('No, go back.'),
            ],
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