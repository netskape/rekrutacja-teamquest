<?php


namespace Application\Controller\Factory;


use Application\Controller\AuthController;
use Application\Service\MailService;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use ZendTwig\Renderer\TwigRenderer;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        return new AuthController(
            $container->get('doctrine.authenticationservice.orm_default'),
            $container->get(FormElementManager::class),
            $container->get(ObjectManager::class),
            $container->get(SessionManager::class),
            $container->get(TwigRenderer::class),
            $container->get(MailService::class)
        );
    }
}