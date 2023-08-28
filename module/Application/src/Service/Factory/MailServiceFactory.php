<?php

namespace Application\Service\Factory;

use Application\Service\LoggerService;
use Application\Service\MailService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZendTwig\Renderer\TwigRenderer;

class MailServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new MailService(
            $container->get('config'),
            $container->get(TwigRenderer::class)
        );
    }
}
