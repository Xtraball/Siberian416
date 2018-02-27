<?php

// Update ACL with levels
try {
    $this->query('UPDATE acl_role SET parent_id = NULL WHERE code = "Admin";');
    $this->query('UPDATE acl_role SET parent_id = (SELECT role_id FROM acl_role WHERE code = "Admin") WHERE code != "Admin";');
} catch (Exception $e) {
    // Do nothing!
}