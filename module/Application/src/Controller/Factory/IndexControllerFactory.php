<?php


namespace Application\Controller\Factory;


use Application\Controller\IndexController;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        return new IndexController(
            $container->get(FormElementManager::class),
            $container->get(ObjectManager::class),
        );
    }
}