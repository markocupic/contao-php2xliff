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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\File;
use Contao\Message;
use Markocupic\ContaoPhp2Xliff\Parser\PhpParser;
use Markocupic\ContaoPhp2Xliff\Writer\ContaoXliffTransFileWriter;
use Symfony\Contracts\Translation\TranslatorInterface;

class XliffFromPhp
{
    private ContaoFramework $framework;
    private TranslatorInterface $translator;
    private string $projectDir;
    private ?array $targetTransArray;
    private ?array $sourceTransArray;
    private ?File $sourceLangFile;
    private ?File $targetLangFile;
    private ?string $sourceLang;
    private ?string $targetLang;

    public function __construct(ContaoFramework $framework, TranslatorInterface $translator, string $projectDir)
    {
        $this->framework = $framework;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
    }

    /**
     * @throws \Exception
     */
    public function generate(string $sourceLang, File $sourceLangFile, string $targetLang, File $targetLangFile, bool $regenerateSourceTransFile = false): void
    {
        if (isset($GLOBALS[PhpParser::PHP2XLIFF_LANG_KEY])) {
            unset($GLOBALS[PhpParser::PHP2XLIFF_LANG_KEY]);
        }

        $this->sourceLangFile = $sourceLangFile;
        $this->targetLangFile = $targetLangFile;

        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;

        // Get source translation from php lang file parser
        $parser = new PhpParser();
        $this->sourceTransArray = $parser->getFromFile($sourceLangFile);

        // Get target translation from php lang file parser
        $parser = new PhpParser();
        $this->targetTransArray = $parser->getFromFile($targetLangFile);

        if ($regenerateSourceTransFile) {
            $this->regenerateSourceFile();
        }

        $this->generateTargetFile();
    }

    protected function regenerateSourceFile(): void
    {
        $sLang = $this->sourceLang;
        $tLang = $this->sourceLang;
        $origFilePath = $this->sourceLangFile->path;
        $targetFilePath = $this->projectDir.'/'.$this->sourceLangFile->path;
        $sTransArray = $this->sourceTransArray;
        $tTransArray = $this->sourceTransArray;

        $messageAdapter = $this->framework->getAdapter(Message::class);

        if (new ContaoXliffTransFileWriter($sLang, $tLang, $origFilePath, $targetFilePath, $sTransArray, $tTransArray)) {
            $strMsg = $this->translator->trans(
                'CONVERT_PHP_2_XLIFF.regenerateSourceSuccess',
                [
                    $this->sourceLang,
                    basename($this->sourceLangFile->path),
                ],
                'contao_default',
            );
            $messageAdapter->addInfo($strMsg);
        } else {
            $strMsg = $this->translator->trans(
                'CONVERT_PHP_2_XLIFF.regenerateSourceFail',
                [
                    $this->sourceLang,
                    basename($this->sourceLangFile->path),
                ],
                'contao_default',
            );
            $messageAdapter->addError($strMsg);
        }
    }


    protected function generateTargetFile(): void
    {
        $sLang = $this->sourceLang;
        $tLang = $this->targetLang;
        $origFilePath = $this->sourceLangFile->path;
        $targetFilePath = $this->projectDir.'/'.$this->targetLangFile->path;
        $sTransArray = $this->sourceTransArray;
        $tTransArray = $this->targetTransArray;

        $messageAdapter = $this->framework->getAdapter(Message::class);

        if (new ContaoXliffTransFileWriter($sLang, $tLang, $origFilePath, $targetFilePath, $sTransArray, $tTransArray)) {
            $strMsg = $this->translator->trans(
                'CONVERT_PHP_2_XLIFF.generateSourceSuccess',
                [
                    $this->targetLang,
                    basename($this->sourceLangFile->path),
                ],
                'contao_default',
            );
            $messageAdapter->addInfo($strMsg);
        } else {
            $strMsg = $this->translator->trans(
                'CONVERT_PHP_2_XLIFF.generateSourceFail',
                [
                    $this->targetLang,
                    basename($this->sourceLangFile->path),
                ],
                'contao_default',
            );
            $messageAdapter->addError($strMsg);
        }
    }
}
