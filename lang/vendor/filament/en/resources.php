<?php

return [
    'navigation_groups' => [
        'Accounting' => 'Accounting',
        'Project Management' => 'Project Management',
        'Administration' => 'Administration',
        'Company Management' => 'Company Management',
    ],
    
    'sections' => [
        'task_details' => 'Task Details',
        'schedule_assignment' => 'Schedule & Assignment',
        'financial_details' => 'Financial Details',
    ],
    
    'fields' => [
        'title' => 'Title',
        'project' => 'Project',
        'due_date' => 'Due Date',
        'is_completed' => 'Completed',
        'completed' => 'Completed',
        'price' => 'Price',
        'cost_percentage' => 'Cost Percentage',
        'priority' => 'Priority',
    ],
    
    'resources' => [
        'AccountResource' => [
            'label' => 'Account',
            'plural' => 'Accounts',
        ],
        'AccountGroupResource' => [
            'label' => 'Account Group',
            'plural' => 'Account Groups',
        ],
        'CompanyResource' => [
            'label' => 'Company',
            'plural' => 'Companies',
        ],
        'ProjectResource' => [
            'label' => 'Project',
            'plural' => 'Projects',
        ],
        'TransactionResource' => [
            'label' => 'Transaction',
            'plural' => 'Transactions',
        ],
        'TransactionGroupResource' => [
            'label' => 'Transaction Group',
            'plural' => 'Transaction Groups',
        ],
        'UserResource' => [
            'label' => 'User',
            'plural' => 'Users',
        ],
        'RoleResource' => [
            'label' => 'Role',
            'plural' => 'Roles',
        ],
        'PermissionResource' => [
            'label' => 'Permission',
            'plural' => 'Permissions',
        ],
        'TaskResource' => [
            'label' => 'Task',
            'plural' => 'Tasks',
        ],
    ],
];
