<?php

declare(strict_types=1);

/*
 * This file is part of Contao PHP2XLIFF Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-php2xliff
 */

namespace Markocupic\ContaoPhp2Xliff\Parser;

use Contao\File;

class PhpParser
{
    public const PHP2XLIFF_LANG_KEY = 'Php2XliffLangKey';

    private string $content = '';

    private ?array $langArray = null;

    private ?array $dotKeyLangArray = null;

    private ?string $clonePath = null;

    /**
     * @throws \Exception
     */
    public function getFromFile(File $file): array
    {
        /*
         * Do not change this order
         */
        return $this
            ->setContent($file->getContent())
            ->prepare()
            ->writeToTmpFile()
            ->getTranslationAsArray()
            ->array2DotKey()
            ->getDotKeyArray()
            ;
    }

    /**
     * Save file content as array.
     */
    private function getTranslationAsArray(): self
    {
        include $this->clonePath;

        $this->langArray = $GLOBALS[self::PHP2XLIFF_LANG_KEY] ?? null;

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return $this
     */
    private function writeToTmpFile(): self
    {
        $tmpDir = sys_get_temp_dir();

        if (!is_dir($tmpDir)) {
            throw new \Exception('Temporary directory not found.');
        }

        $this->clonePath = $tmpDir.'/'.md5(microtime()).'php';

        if (false === file_put_contents($this->clonePath, $this->content)) {
            throw new \Exception(sprintf('Can not write to the temp file. "%s".', $this->clonePath));
        }

        return $this;
    }

    /**
     * Return the dot-key array.
     */
    private function getDotKeyArray(): array
    {
        return $this->dotKeyLangArray;
    }

    /**
     * Convert array to dot-key array.
     */
    private function array2DotKey(): self
    {
        $result = [];

        if ($this->langArray) {
            $ritit = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->langArray));

            foreach ($ritit as $leafValue) {
                $keys = [];

                foreach (range(0, $ritit->getDepth()) as $depth) {
                    $keys[] = $ritit->getSubIterator($depth)->key();
                }
                $result[implode('.', $keys)] = $leafValue;
            }
        }

        $this->dotKeyLangArray = $result;

        return $this;
    }

    private function setContent(string $strValue): self
    {
        $this->content = $strValue;

        return $this;
    }

    /**
     * Replace the key 'TL_LANG' with.
     */
    private function prepare(): self
    {
        $this->content = str_replace('TL_LANG', self::PHP2XLIFF_LANG_KEY, $this->content);

        return $this;
    }
}
