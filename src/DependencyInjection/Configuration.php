<?php

declare(strict_types=1);

/*
 * This file is part of Contao PHP2XLIFF Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-php2xliff
 */

namespace Markocupic\ContaoPhp2Xliff\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'markocupic_contao_php2xliff';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('sourceLanguage')
            ->defaultValue('en')
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
