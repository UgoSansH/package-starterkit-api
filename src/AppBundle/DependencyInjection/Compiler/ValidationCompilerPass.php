<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\DirectoryResource;


class ValidationCompilerPass implements CompilerPassInterface
{
    const VALIDATOR_DIR = __DIR__ . '/../../../../app/config/validation';

    public function process(ContainerBuilder $container)
    {
        $validatorBuilder = $container->getDefinition('validator.builder');
        $finder           = new Finder();
        $validatorFiles   = [];
        $files            = $finder->files()->in(self::VALIDATOR_DIR);

        if ($files) {
            foreach ($files as $file) {
                $validatorFiles[] = $file->getRealPath();
            }
        }

        if (!empty($validatorFiles)) {
            $validatorBuilder->addMethodCall('addYamlMappings', array($validatorFiles));
        }

        $container->addResource(new DirectoryResource(self::VALIDATOR_DIR));
    }

}
