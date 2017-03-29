<?php

namespace APM\AchatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class APMAchatExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        /*valider les achat uniquement en Afrique*/
        if (isset($config['nombre1'])) {
            if (isset($config['nombre2'])) {
                $container->setParameter('val1', $config['nombre1']);
                $container->setParameter('val2', $config['nombre2']);
            } else {
                throw new LogicException("Veuillez Declarer le nombre>> Yannick.");
            }
        } else {
            throw new LogicException("Veuillez Declarer le nombre>> Yannick.");
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');


        $this->addClassesToCompile(array(//definir les classes à compiler une fois pour besoin de performance(reduire les chargements E/S répétés).
        ));
    }
}
