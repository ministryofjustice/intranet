<?php
/**
 * G O V . U K   N O T I F Y
 * -.-.-.-.-.-.-.-.-.-.-.-.-
 * Template configuration for WordPress system
 * Templates can be found on the GovUK Notify website:
 * https://www.notifications.service.gov.uk/
 */
return [

    /**
     * Email configuration
     ************************/

    'email' => [

        'default' => [
            'id' => 'b6057ac4-fc18-49ef-be6d-4b9759ef1fb1',
            'personalisation' => [
                'subject' => '<subject>',
                'message' => '<message>'
            ]
        ],

        'comment-registration' => [
            'id' => 'ab5e3dbb-4bda-4323-86ca-a200fa3d6689',
            'personalisation' => [
                'name' => '<name>',
                'reply_link' => '<reply_link>'
            ]
        ],

        'comment-deletion' => [
            'id' => '99c82592-b7fe-42f0-ac0a-acad5d2fe19d',
            'personalisation' => [
                'name' => '<name>',
                'delete_link' => '<delete_link>'
            ]
        ],

        'wrong-with-this-page' => [
            'id' => '29cc5601-662e-463b-b04a-47fd7f18efd2',
            'personalisation' => [
                'date' => '',
                'name' => '',
                'email_creator' => '',
                'message' => '',
                'agency' => '',
                'page_url' => '',
                'user_agent' => ''
            ]
        ],

        'wrong-with-this-page-confirmation' => [
            'id' => '183a0daf-18ab-4426-920a-fcb3831a0ade',
            'personalisation' => [
                'date' => '',
                'name' => '',
                'agency_name' => ''
            ]
        ],

        'auto-response' => [
            'id' => '4f4639e0-9673-4221-9f42-fe5e543f1718'
        ]
    ],

    /**
     * SMS configuration
     **********************/

    'sms' => []
];
