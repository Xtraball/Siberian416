<?php
/**
 *
 * Schema definition for 'acl_role'
 *
 * Last update: 2018-02-27
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['acl_role'] = [
    'role_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'code' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'label' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'parent_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'foreign_key' => [
            'table' => 'acl_role',
            'column' => 'role_id',
            'name' => 'FK_ROLE_PARENT_ID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'FK_ROLE_PARENT_ID',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
];