<?php
/**
 *
 * Schema definition for 'form2'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['form2'] = [
    'form2_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'form2_id_aov_valueid',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'form2_id_aov_valueid_index',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'card_design' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'version' => [
        'type' => 'int(1)',
        'default' => '1',
    ],
];