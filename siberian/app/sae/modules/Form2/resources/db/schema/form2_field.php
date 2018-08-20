<?php
/**
 *
 * Schema definition for 'form2_field'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['form2_field'] = [
    'field_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'form2_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'form2_id_form2_id',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'form2_id_form2_id_index',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'type' => [
        'type' => 'varchar(64)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'text',
    ],
    'options' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'position' => [
        'type' => 'int(11)',
        'default' => '1',
    ],
    'is_required' => [
        'type' => 'int(1)',
        'default' => '0',
    ],
];