<?php
/**
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Siberian_Exporter::register('fanwall', 'Comment_Model_Comment');
    Siberian_Exporter::register('newswall', 'Comment_Model_Comment');
};
