<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Siberian_Exporter::register('catalog', 'Catalog_Model_Category');
};
