<?php

namespace App\Enums;

class AdminPermission
{
    const ALL = [
        // Operators
        'operators.view' => 'View Operators',
        'operators.approve' => 'Approve/Reject Operators',
        'operators.suspend' => 'Suspend/Reactivate Operators',
        'operators.edit-tier' => 'Change Operator Tier',
        'operators.edit-commission' => 'Change Commission Rate',

        // Bookings
        'bookings.view' => 'View Bookings',
        'bookings.edit-status' => 'Change Booking Status',
        'bookings.add-notes' => 'Add Admin Notes',

        // Financial
        'revenue.view' => 'View Revenue Dashboard',
        'statements.view' => 'View Statements',
        'statements.manage' => 'Manage Payouts',

        // Quality
        'disputes.view' => 'View Disputes',
        'disputes.resolve' => 'Resolve Disputes',
        'issues.view' => 'View Trip Issues',

        // Users
        'users.view' => 'View Users',
        'users.manage' => 'Activate/Deactivate Users',

        // System
        'settings.view' => 'View Settings',
        'settings.edit' => 'Edit Settings',
        'fleet-types.manage' => 'Manage Fleet Types',
        'admin-users.view' => 'View Admin Users',
        'admin-users.manage' => 'Create/Edit Admin Users',
        'admin-roles.manage' => 'Manage Roles & Permissions',
    ];

    // Permission groups for display in the UI
    const GROUPS = [
        'Operators' => [
            'operators.view',
            'operators.approve',
            'operators.suspend',
            'operators.edit-tier',
            'operators.edit-commission',
        ],
        'Bookings' => [
            'bookings.view',
            'bookings.edit-status',
            'bookings.add-notes',
        ],
        'Financial' => [
            'revenue.view',
            'statements.view',
            'statements.manage',
        ],
        'Quality' => [
            'disputes.view',
            'disputes.resolve',
            'issues.view',
        ],
        'Users' => [
            'users.view',
            'users.manage',
        ],
        'System' => [
            'settings.view',
            'settings.edit',
            'fleet-types.manage',
            'admin-users.view',
            'admin-users.manage',
            'admin-roles.manage',
        ],
    ];

    // Pre-defined role permissions
    const ADMIN = [
        'operators.view',
        'operators.approve',
        'operators.suspend',
        'operators.edit-tier',
        'operators.edit-commission',
        'bookings.view',
        'bookings.edit-status',
        'bookings.add-notes',
        'disputes.view',
        'disputes.resolve',
        'issues.view',
        'users.view',
        'users.manage',
        'fleet-types.manage',
    ];

    const FINANCE = [
        'operators.view',
        'bookings.view',
        'revenue.view',
        'statements.view',
        'statements.manage',
    ];

    const SUPPORT = [
        'operators.view',
        'bookings.view',
        'bookings.edit-status',
        'bookings.add-notes',
        'disputes.view',
        'disputes.resolve',
        'issues.view',
        'users.view',
    ];
}
