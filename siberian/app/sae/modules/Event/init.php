<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Siberian_Exporter::register('calendar', 'Event_Model_Event');
};
