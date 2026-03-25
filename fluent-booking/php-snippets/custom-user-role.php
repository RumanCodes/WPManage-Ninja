<?php
function add_custom_membership_roles() {
    // Bronze Role
    add_role(
        'bronze_member',
        'Bronze Member',
        [
            'read' => true,
        ]
    );

    // Silver Role
    add_role(
        'silver_member',
        'Silver Member',
        [
            'read' => true,
        ]
    );

    // Gold Role
    add_role(
        'gold_member',
        'Gold Member',
        [
            'read' => true,
        ]
    );
}
add_action('init', 'add_custom_membership_roles');