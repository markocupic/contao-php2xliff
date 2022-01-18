<?php

declare(strict_types=1);

/*
 * This file is part of Contao Php2Xliff.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-php2xliff
 */

namespace Markocupic\ContaoPhp2Xliff;

use Markocupic\ContaoPhp2Xliff\DependencyInjection\MarkocupicContaoPhp2XliffExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MarkocupicContaoPhp2Xliff.
 */
class MarkocupicContaoPhp2Xliff extends Bundle
{
    public function getContainerExtension(): MarkocupicContaoPhp2XliffExtension
    {
        return new MarkocupicContaoPhp2XliffExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
