<?php
/**
 *
 * Schema definition for 'form2_collection'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['form2_collection'] = [
    'collection_id' => [
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
    // Carbon copy of the field as it was at submit time!
    'field_type' => [
        'type' => 'varchar(64)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'text',
    ],
    'field_options' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'field_position' => [
        'type' => 'int(11)',
    ],
    'field_is_required' => [
        'type' => 'int(1)',
    ],
    'content' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ]
];