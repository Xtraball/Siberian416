<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Siberian_Exporter::register('contact', 'Contact_Model_Contact', [
        'all' => __('All data'),
        'safe' => __('Clean-up sensible data'),
    ]);
};

