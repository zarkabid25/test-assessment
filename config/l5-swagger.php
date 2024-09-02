<?php

return [
    'default' => [
        'info' => [
            'title' => 'Role-Based API',
            'description' => 'API documentation for the role-based application',
            'version' => '1.0.0',
        ],
        'paths' => [
            'docs' => 'api/documentation',
            'annotations' => [
                base_path('app'),
                base_path('routes/api.php'),
            ],
        ],
    ],
];

