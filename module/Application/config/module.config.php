<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\AuthController;
use Application\Controller\Factory\AuthControllerFactory;
use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\IndexController;
use Application\Forms\ChangePasswordForm;
use Application\Forms\Factory\ChangePasswordFormFactory;
use Application\Forms\Factory\CsvFormFactory;
use Application\Forms\Factory\LoginFormFactory;
use Application\Forms\LoginForm;
use Application\Forms\CsvForm;
use Application\Service\Factory\MailServiceFactory;
use Application\Service\MailService;
use Application\View\Twig\PhpMissingFunctionsExtension;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\FormElementManager;
use Laminas\Form\FormElementManagerFactory;
use Laminas\Mvc\Plugin\FilePrg\FilePostRedirectGet;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Twig\Extension\DebugExtension;
use ZendTwig\Service\TwigExtensionFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'auth' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/auth',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,

                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'login' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' =>  Controller\AuthController::class,
                                'action' => 'login',
                            ]
                        ],
                    ],
                    'logout' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' =>  Controller\AuthController::class,
                                'action' => 'logout',
                            ]
                        ],
                    ],
                    'change-password' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/change-password',
                            'defaults' => [
                                'controller' =>  Controller\AuthController::class,
                                'action' => 'changePassword',
                            ]
                        ],
                    ],
                ]
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
            AuthController::class => AuthControllerFactory::class
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            FilePostRedirectGet::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            //twig
            PhpMissingFunctionsExtension::class => TwigExtensionFactory::class,
            FormElementManager::class => FormElementManagerFactory::class,
            MailService::class => MailServiceFactory::class
        ],
        'aliases' => [
            ObjectManager::class => 'doctrine.entitymanager.orm_default',
        ]
    ],
    'zend_twig' => [
        'extensions' => [
            PhpMissingFunctionsExtension::class,
            DebugExtension::class
        ]
    ],
    'twig' => [
        'debug' => true, // Włącz debugowanie Twig'a
        // ...
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.twig',
            'application/index/index' => __DIR__ . '/../view/application/index/index.twig',
            'error/404'               => __DIR__ . '/../view/error/404.twig',
            'error/index'             => __DIR__ . '/../view/error/500.twig',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'form_elements' => [
        'factories' => [
            LoginForm::class => LoginFormFactory::class,
            CsvForm::class => CsvFormFactory::class,
            ChangePasswordForm::class => ChangePasswordFormFactory::class
        ],
    ],
    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity',
                    __DIR__ . '/../src/Repository',

                ]
            ],
        ],
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Application\Entity\Users',
                'identity_property' => 'email',
                'credential_property' => 'password',
                'credential_callable' => 'Application\Controller\AuthController::verifyCredential'
            ],
        ],

    ]
];
