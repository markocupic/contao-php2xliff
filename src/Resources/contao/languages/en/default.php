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

/*
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['convertphp2xliffConfirm'] = 'Are you sure that you want to proceed? If yes, XLF translation files will be generated from PHP language files. Already existing XLF translation files will be overridden.';

/*
 * Messages
 */
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['regenerateSourceSuccess'] = 'Regenerated the %s .xlf version of "%s" and wrote it successfully to the language folder.';
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['regenerateSourceFail'] = 'Couldn\'t regenerate the %s .xlf version of "%s".';
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['generateSourceSuccess'] = 'Generated the %s .xlf version of "%s" and wrote it successfully to the language folder.';
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['generateSourceFail'] = 'Couldn\'t generate the %s .xlf version of "%s".';
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['sourceLangFileMissing'] = 'Skipped the %s translation of %s, because we cold not find the source lang file in "%s".';
$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['noPHPLangFilesFound'] = 'Couldn\'t find any PHP language files to convert to the xliff format. Please check your folder- and language settings.';
