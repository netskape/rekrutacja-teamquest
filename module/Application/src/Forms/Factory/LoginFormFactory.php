<?php


namespace Application\Forms\Factory;


use Application\Forms\LoginForm;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoginFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LoginForm($container->get(ObjectManager::class));
    }
}