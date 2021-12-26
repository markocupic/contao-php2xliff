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

namespace Markocupic\ContaoPhp2Xliff;

use Contao\File;
use Contao\Message;
use Contao\StringUtil;
use Markocupic\ContaoPhp2Xliff\Parser\PhpParser;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class XliffFromPhp
{
    private TwigEnvironment $twig;

    private ?array $targetLangArray;
    private ?array $sourceLangArray;
    private ?File $sourceLangFile;
    private ?File $targetLangFile;
    private ?string $sourceLang;
    private ?string $targetLang;

    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws \Exception
     */
    public function generate(string $sourceLang, File $sourceLangFile, string $targetLang, File $targetLangFile): void
    {
        if (isset($GLOBALS[PhpParser::PHP2XLIFF_LANG_KEY])) {
            unset($GLOBALS[PhpParser::PHP2XLIFF_LANG_KEY]);
        }

        $this->sourceLangFile = $sourceLangFile;
        $this->targetLangFile = $targetLangFile;

        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;

        $parser = new PhpParser();
        $this->targetLangArray = $parser->getFromFile($targetLangFile);

        $parser = new PhpParser();
        $this->sourceLangArray = $parser->getFromFile($sourceLangFile);

        $this->generateSourceFile($this->getSourceFileContent());
        $this->generateTarget($this->getTargetFileContent());
        //die($this->targetLangFile->path);
        Message::addInfo(
            sprintf(
                'Generated the %s .xlf version of %s and wrote it to "%s".',
                $this->targetLangArray,
                $this->targetLangFile->path,
                \dirname((string) $this->targetLangFile->path),
            )
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getSourceFileContent(): string
    {
        $items = [];

        foreach (array_keys($this->sourceLangArray) as $k) {
            $items[] = [
                'id' => $k,
                'source' => isset($this->sourceLangArray[$k]) ? StringUtil::specialchars(StringUtil::restoreBasicEntities($this->sourceLangArray[$k])) : '',
                'target' => null,
            ];
        }

        return $this->twig->render(
            '@MarkocupicContaoPhp2Xliff/templ.xml.twig',
            [
                'original' => $this->sourceLangFile->path,
                'source_lang' => $this->sourceLang,
                'target_lang' => null,
                'items' => $items,
            ]
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getTargetFileContent(): string
    {
        $items = [];

        foreach (array_keys($this->sourceLangArray) as $k) {
            $items[] = [
                'id' => $k,
                'source' => isset($this->sourceLangArray[$k]) ? StringUtil::specialchars(StringUtil::restoreBasicEntities($this->sourceLangArray[$k])) : '',
                'target' => isset($this->targetLangArray[$k]) ? StringUtil::specialchars(StringUtil::restoreBasicEntities($this->targetLangArray[$k])) : '',
            ];
        }

        return $this->twig->render(
            '@MarkocupicContaoPhp2Xliff/templ.xml.twig',
            [
                'original' => $this->sourceLangFile->path,
                'source_lang' => $this->sourceLang,
                'target_lang' => $this->targetLang,
                'items' => $items,
            ]
        );
    }

    /**
     * @param $strContent
     *
     * @throws \Exception
     */
    private function generateSourceFile($strContent): void
    {
        $strNewPath = \dirname($this->sourceLangFile->path).'/'.$this->sourceLangFile->filename.'.xlf';

        $file = new File($strNewPath);
        $file->truncate();
        $file->append($strContent);
        $file->close();
    }

    /**
     * @param $strContent
     *
     * @throws \Exception
     */
    private function generateTarget($strContent): void
    {
        $strNewPath = \dirname($this->targetLangFile->path).'/'.$this->targetLangFile->filename.'.xlf';

        $file = new File($strNewPath);
        $file->truncate();
        $file->append($strContent);
        $file->close();
    }
}
