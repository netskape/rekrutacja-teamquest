<?php


namespace Application\Forms\Factory;


use Application\Forms\ChangePasswordForm;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ChangePasswordFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ChangePasswordForm($container->get(ObjectManager::class));
    }
}