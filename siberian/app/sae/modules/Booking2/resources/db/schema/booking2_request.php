<?php
/**
 *
 * Schema definition for 'booking_store'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['booking2_request'] = [
    'request_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'store_id' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'KEY_BOOKING2_REQUEST_ID',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'values' => [
        'type' => 'mediumtext',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
];