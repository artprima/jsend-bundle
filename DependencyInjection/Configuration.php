<?php

/*
 * This file is part of the Artprima Jsend package.
 *
 * (c) Denis Voytyuk <ask@artprima.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Artprima\Bundle\JsendBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Denis Voytyuk <ask@artprima.cz>
 *
 * @package Artprima\Bundle\JsendBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /**
         * @var NodeBuilder $root
         */
        $root = $treeBuilder->root('artprima_jsend', 'array');

        return $treeBuilder;
    }
} 