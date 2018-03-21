<?php
/**
 * @param $bootstrap
 */
$init = function ($bootstrap) {
    // Exporter!
    Siberian_Exporter::register(
        'folder',
        'Folder_Model_Folder',
        [
            'all' => __('Export all folder tree & subsequent features'),
            'tree-only' => __('Export the folder tree only'),
        ]
    );
};
