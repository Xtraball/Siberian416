<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Siberian_Exporter::register('maps', 'Maps_Model_Maps');
};
