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

namespace Markocupic\ContaoPhp2Xliff;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\File;
use Contao\Message;
use Markocupic\ContaoPhp2Xliff\Parser\PhpParser;
use Markocupic\ContaoPhp2Xliff\Writer\ContaoXliffTransFileWriter;
use Symfony\Contracts\Translation\TranslatorInterface;

class XliffFromPhp
{
    private array|null $targetTransArray;
    private array|null $sourceTransArray;
    private File|null $sourceLangFile;
    private File|null $targetLangFile;
    private string|null $sourceLang;
    private string|null $targetLang;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly TranslatorInterface $translator,
        private readonly string $projectDir,
    ) {
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

        // Do not generate the source lang twice
        if ($regenerateSourceTransFile || $sourceLang === $targetLang) {
            $this->regenerateSourceFile();
        }

        if ($sourceLang !== $targetLang) {
            $this->generateTargetFile();
        }
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

        $writer = new ContaoXliffTransFileWriter($sLang, $tLang, $origFilePath, $targetFilePath, $sTransArray, $tTransArray);

        if ($writer->export()) {
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

        $writer = new ContaoXliffTransFileWriter($sLang, $tLang, $origFilePath, $targetFilePath, $sTransArray, $tTransArray);

        if ($writer->export()) {
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
