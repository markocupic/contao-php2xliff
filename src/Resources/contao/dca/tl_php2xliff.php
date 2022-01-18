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

/*
 * Table tl_php2xliff
 */
$GLOBALS['TL_DCA']['tl_php2xliff'] = [
    // Config
    'config'   => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'     => [
        'sorting'           => [
            'mode'        => 2,
            'fields'      => ['title'],
            'flag'        => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'             => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy'             => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete'           => [
                'href'       => 'act=delete',
                'icon'       => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'             => [
                'href'       => 'act=show',
                'icon'       => 'show.svg',
                'attributes' => 'style="margin-right:3px"',
            ],
            'convertphp2xliff' => [
                'href'       => 'key=convertphp2xliff',
                'icon'       => 'bundles/markocupiccontaophp2xliff/icons/convertphpxliff.svg',
                'attributes' => 'data-icon="op-icon" onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['CONVERT_PHP_2_XLIFF']['convertphp2xliffConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,sourceLanguage,targetLanguage,languagePath,regenerateSourceTransFile',
    ],
    // Fields
    'fields'   => [
        'id'                        => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'                    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'                     => [
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'sourceLanguage'            => [
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'readonly' => true,
                'tl_class' => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'targetLanguage'            => [
            'inputType' => 'select',
            'exclude'   => true,
            'eval'      => [
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'languagePath'              => [
            'inputType' => 'text',
            'exclude'   => true,
            'eval'      => [
                'mandatory'     => true,
                'maxlength'     => 255,
                'trailingSlash' => false,
                'tl_class'      => 'w50',
            ],
            'sql'       => "varchar(512) NOT NULL default 'vendor/#vendorname#/#bundlename#/src/Resources/contao/languages'",
        ],
        'regenerateSourceTransFile' => [
            'inputType' => 'checkbox',
            'exclude'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
    ],
];
