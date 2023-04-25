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
     ***********************/

    'email' => [

        'default' => [
            'id' => 'b6057ac4-fc18-49ef-be6d-4b9759ef1fb1',
            'personalisation' => [
                'subject' => '<subject>',
                'message' => '<message>'
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
