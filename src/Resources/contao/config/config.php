<?php

declare(strict_types=1);

/*
 * This file is part of Contao PHP language file to XLIFF.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-php2xliff
 */

use Markocupic\ContaoPhp2Xliff\Model\Php2xliffModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD']['php2xliff_modules']['php2xliff'] = [
    'tables' => ['tl_php2xliff'],
];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_php2xliff'] = Php2xliffModel::class;
