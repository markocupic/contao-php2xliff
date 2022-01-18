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

namespace Markocupic\ContaoPhp2Xliff\String;

use Contao\StringUtil;

class XmlSanitizer
{
    /**
     * Trim, Replace &quot; with "
     * Remove not allowed characters: & and <.
     */
    public static function sanitize(string $strString): string
    {
        $strString = trim($strString);
        $strString = html_entity_decode($strString, ENT_QUOTES);
        $strString = StringUtil::ampersand($strString);

        return str_replace('<', '&lt;', $strString);
    }
}
