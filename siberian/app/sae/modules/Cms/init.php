<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    Siberian_Cache_Design::overrideCoreDesign('Cms');

    // Exporter!
    Siberian_Exporter::register('custom_page', 'Cms_Model_Application_Page', [
        'all' => __('All data'),
        'safe' => __('Clean-up sensible data'),
    ]);
};
