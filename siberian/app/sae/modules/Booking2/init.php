<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    # Exporter
    \Siberian_Exporter::register("booking2", "Booking2_Model_Booking", [
        "all" => __("All data"),
        "safe" => __("Clean-up sensible data"),
    ]);
};

