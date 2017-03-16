<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mes\Security\CryptoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your
 * app/config files.
 *
 * To learn more see {@link * http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mes_crypto');

        $rootNode
          ->children()
            ->arrayNode('key')
                ->addDefaultsIfNotSet()
                ->children()
                        ->scalarNode('path')->defaultNull()->end()
                        ->scalarNode('key')->defaultNull()->end()
                ->end()
                ->info('Encoded key or path to .crypto file. If key is null, it generates a different random key for each request.')
                ->beforeNormalization()
                    ->ifString()
                    ->then(function ($v) {
                        if (is_file($v) && is_readable($v)) {
                            return array('path' => $v);
                        }

                        return array('key' => $v);
                    })
                ->end()
                ->validate()
                ->ifArray()
                    ->then(function ($v) {
                        $path = $v['path'];
                        $key = $v['key'];

                        if (null !== $path) {
                            if (!('crypto' === pathinfo($path, PATHINFO_EXTENSION))) {
                                throw new \InvalidArgumentException(sprintf('Invalid ".%s" file extension. ".crypto" is required.', pathinfo($path, PATHINFO_EXTENSION)));
                            }

                            return $v;
                        }

                        if (null !== $key) {
                            if (!ctype_print($key)) {
                                throw new \InvalidArgumentException(sprintf('Key %s is not valid value. A printable format is required.', $key));
                            }
                        }

                        return $v;
                    })
                ->end()
            ->end()

            ->scalarNode('secret')
                ->info('Secret value to generate a key protected by password.')
                ->defaultNull()
            ->end()

            ->booleanNode('external_secret')
                ->info('Load secret value from the .crypto file to generate a key protected by password')
                ->defaultNull()
            ->end()

            ->scalarNode('key_storage')
                ->info('your_key_storage_service_id. Default: Mes KeyStorage.')
                ->defaultNull()
            ->end()

            ->scalarNode('key_generator')
                ->info('your_key_generator_service_id. Default: Mes KeyGenerator.')
                ->defaultNull()
            ->end()

            ->scalarNode('encryption')
                ->info('your_encryption_service_id. Default: Mes Encryption.')
                ->defaultNull()
            ->end()
          ->end()
        ;

        return $treeBuilder;
    }
}
