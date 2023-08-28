<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'doctrine' => [
        'connection' => [
            // default connection name
            'orm_default' => [
                'driverClass' => Doctrine\DBAL\Driver\PDO\MySQL\Driver::class,
                'params' => [
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'rekrutacja',
                    'password' => 'xwa5wA4',
                    'dbname'   => 'rekrutacja',
                    'charset'  => 'utf8mb4',
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [

                'table_storage' => [
                    'table_name' => 'doctrine_migrations',
                    'version_column_name' => 'version',
                    'version_column_length' => 1024,
                    'executed_at_column_name' => 'executedAt',
                    'execution_time_column_name' => 'executionTime',
                ],
                'migrations_paths' => [
                    'Application\Migrations' => 'data/migrations',
                ],

            ],
        ],
    ],
    'daysAfterChangePasswordRequired' => 1,
    'session_config' => [
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cache_expire' => 60*60*24*30,
        // Session cookie will expire in 12 hours.
        'cookie_lifetime' => 60*60*12,
        // Session data will be stored on server maximum for 30 days.
        'gc_maxlifetime'     => 60*60*24*30,
        'remember_me_seconds' => 60 * 60 * 24 * 7,
        'rememberMeSeconds' => 60 * 60 * 24 * 7 // na tydzień, domyślnie jest 2 tygodnie
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionArrayStorage::class,
    ],


    'smtp' => [
        //wykorzytsanie sendmail na ubuntu
        'from' => [
            'email' =>'rekrutacja@teamquest.pl',
            'name'  => 'rekrutacja',
        ],
        'host' => 'localhost',
        'port' => 25,

        //w przypadku chęci uzycia rzeczywistego smtp odkomentowac i uzupełnić
//        'host' => '',
//        'name' => '',
//        'port' => 25,
//        'connection_time_limit' => 10,
//        'connection_class'  => 'login',
//        'connection_config' => [
//            'username' => '',
//            'password' => '',
//        ],
    ],
];
