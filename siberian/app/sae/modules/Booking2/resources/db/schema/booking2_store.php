<?php
/**
 *
 * Schema definition for 'booking_store'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['booking2_store'] = [
    'store_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'booking_id' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'KEY_BOOKING2_BOOKING_ID',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'name' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'description' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'address' => [
        'type' => 'varchar(1024)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'email' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'picture' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'thumbnail' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'latitude' => [
        'type' => 'float',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'longitude' => [
        'type' => 'float',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'store_fields' => [
        'type' => 'mediumtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
];