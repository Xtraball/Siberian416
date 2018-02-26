<?php

// Update ACL with levels
try {
    $this->query('UPDATE acl_role SET level = 1 WHERE code = "Admin";');
} catch (Exception $e) {
    // Do nothing!
}