<?php

declare(strict_types=1);

namespace Application;

use Application\Entity\Users;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\EventInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Laminas\Authentication\AuthenticationService' => function ($serviceManager) {
                    return $serviceManager->get('doctrine.authenticationservice.orm_default');
                },
            ],
            SessionManager::class => function ($sm) {
                $config = $sm->get('config');
                if (isset($config['session'])) {
                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = $session['config']['class'] ?? Session\Config\SessionConfig::class;
                        $options = $session['config']['options'] ?? [];
                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        // class should be fetched from service manager since it will require constructor arguments
                        $sessionSaveHandler = $sm->get($session['save_handler']);
                    }

                    $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                } else {
                    $sessionManager = new SessionManager();
                }
                Session\Container::setDefaultManager($sessionManager);
                return $sessionManager;
            },
        ];
    }

    public function onBootstrap(EventInterface $event)
    {

        $app = $event->getApplication();
        $serviceManager = $app->getServiceManager();
        $eventManager = $event->getApplication()->getEventManager();
        $config = $serviceManager->get('config');

        $this->authorization($event);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) use ($config) {
        }, 100);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) use ($config) {
            /** @var \Laminas\View\Model\ViewModel $view */
            $view = $event->getResult();
            $view->setTerminal(true);
        }, 1);
    }

    private function authorization(EventInterface $event)
    {
        $event->getApplication()->getEventManager()->getSharedManager()->attach(
            '*', MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $app = $e->getApplication();
            $serviceManager = $app->getServiceManager();
            $config = $serviceManager->get('config');
            /**
             * @var AuthenticationService $authenticationService
             */
            $authenticationService = $serviceManager->get(AuthenticationService::class);
            $response = $e->getResponse();
            $url = $e->getRequest()->getUri()->getScheme() . '://' . $e->getRequest()->getUri()->getHost();
            $controller = $e->getRouteMatch()->getParam('controller');
            $action = $e->getRouteMatch()->getParam('action');
            $actual_link = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
            if (
                $controller != 'Application\Controller\AuthController'
                && $app->getRequest()->isXmlHttpRequest()
                && !$authenticationService->hasIdentity()
            ) {
                $response = $app->getResponse();
                $response->setStatusCode(403);
                $response->setContent('Session expired');
                $response->sendHeaders();
                exit();
            }
            if ($authenticationService->hasIdentity()) {
                /**
                 * @var Users $user
                 */
                $user = $authenticationService->getIdentity();

                if ($user->isChangePasswordRequired()) {
                    if ($controller != 'Application\Controller\AuthController' && $action != 'change-password') {
                        $response->getHeaders()->addHeaderLine('Location', $url . '/auth/change-password');
                        $response->setStatusCode(302);
                    }
                } else {
                    $updatedAt = $user->getUpdatedAt()->modify("+ " . $config['daysAfterChangePasswordRequired'] . " day");

                    if ($updatedAt->getTimestamp() <= time()) {
                        if ($controller != 'Application\Controller\AuthController' && $action != 'change-password') {
                            $response->getHeaders()->addHeaderLine('Location', $url . '/auth/change-password');
                            $response->setStatusCode(302);
                        }
                    }


                }
            }


            if ($controller != 'Application\Controller\AuthController' && !$authenticationService->hasIdentity()) {
                $redirectPath = '/auth/login';
                $response->getHeaders()->addHeaderLine('Referer', $actual_link);
                $requestUri = $app->getRequest()->getRequestUri();
                if ($requestUri) {
                    $redirectPath .= '?return=' . $requestUri;
                }
                $response->getHeaders()->addHeaderLine('Location', $url . $redirectPath);
                $response->setStatusCode(302);
                return $response;
            }

        }, 1000);

    }

}
