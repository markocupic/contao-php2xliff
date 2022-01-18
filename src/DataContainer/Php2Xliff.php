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

namespace Markocupic\ContaoPhp2Xliff\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\File;
use Contao\Message;
use Haste\Util\Url;
use Markocupic\ContaoPhp2Xliff\Model\Php2xliffModel;
use Markocupic\ContaoPhp2Xliff\XliffFromPhp;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class Php2Xliff
{
    private RequestStack $requestStack;

    private XliffFromPhp $xliffFromPhp;

    private string $projectDir;

    private string $php2XliffSourceLang;

    public function __construct(RequestStack $requestStack, XliffFromPhp $xliffFromPhp, TranslatorInterface $translator, string $projectDir, string $php2XliffSourceLang)
    {
        $this->requestStack = $requestStack;
        $this->xliffFromPhp = $xliffFromPhp;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
        $this->php2XliffSourceLang = $php2XliffSourceLang;
    }

    /**
     * Onload callback.
     *
     * @Callback(table="tl_php2xliff", target="config.onload")
     *
     * @throws \Exception
     */
    public function onloadCallback(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('convertphp2xliff' !== $request->query->get('key')) {
            return;
        }

        if (null === ($php2xliffModel = Php2xliffModel::findByPk($dc->id))) {
            return;
        }

        if ('' === ($targetLang = $php2xliffModel->targetLanguage)) {
            return;
        }

        if ('' === $php2xliffModel->languagePath || '' === $targetLang) {
            return;
        }

        $path = sprintf(
            '%s/%s/%s',
            $this->projectDir,
            $php2xliffModel->languagePath,
            $targetLang,
        );

        // Search for php translation files in vendor/vendorname/bundlename/src/Resources/contao/languages
        $finder = new Finder();
        $finder->in($path)->depth(0)->files()->name('*.php');

        if (!$finder->hasResults()) {
            Message::addInfo($this->translator->trans('CONVERT_PHP_2_XLIFF.noPHPLangFilesFound', [], 'contao_default'));
        }

        foreach ($finder as $targetLangFile) {
            $targetLangFileBasename = $targetLangFile->getBasename();
            $sourceLangFilePath = \dirname($targetLangFile->getRealPath(), 2).'/'.$this->php2XliffSourceLang.'/'.$targetLangFileBasename;

            if (!is_file($sourceLangFilePath)) {
                Message::addError(
                    $this->translator->trans('CONVERT_PHP_2_XLIFF.sourceLangFileMissing', [
                        $targetLang,
                        $targetLangFileBasename,
                        str_replace($this->projectDir.'/', '', $sourceLangFilePath),
                    ], 'contao_default')
                );
                continue;
            }

            $targetLangFile = new File(str_replace($this->projectDir.'/', '', $targetLangFile->getRealPath()));
            $sourceLangFile = new File(str_replace($this->projectDir.'/', '', $sourceLangFilePath));

            $this->xliffFromPhp->generate($this->php2XliffSourceLang, $sourceLangFile, $targetLang, $targetLangFile, (bool) $php2xliffModel->regenerateSourceTransFile);
        }

        $href = Url::removeQueryString(['key']);
        Controller::redirect($href);
    }

    /**
     * Load callback.
     *
     * @Callback(table="tl_php2xliff", target="fields.sourceLanguage.load")
     */
    public function sourceLanguageLoadCallback(string $varValue, DataContainer $dc): string
    {
        return $this->php2XliffSourceLang;
    }

    /**
     * Save callback.
     *
     * @Callback(table="tl_php2xliff", target="fields.sourceLanguage.save")
     */
    public function sourceLanguageSaveCallback(string $varValue, DataContainer $dc): string
    {
        return $this->php2XliffSourceLang;
    }

    /**
     * Buttons callback.
     *
     * @Callback(table="tl_php2xliff", target="fields.targetLanguage.options")
     */
    public function targetLanguageOptionsCallback(DataContainer $dc): array
    {
        $arrOptions = [];

        if (!$dc->id) {
            return [];
        }

        if ('' === $dc->activeRecord->path) {
            return [];
        }

        if (null === ($php2xliffModel = Php2xliffModel::findByPk($dc->id))) {
            return [];
        }

        if ('' === $php2xliffModel->languagePath) {
            return [];
        }

        if (false !== strpos( $php2xliffModel->languagePath, '#vendorname#/#bundlename#'))
        {
            Message::addInfo($this->translator->trans('CONVERT_PHP_2_XLIFF.addValidLanguagePathFolder', [], 'contao_default'));
            return [];
        }

        if (!is_dir($this->projectDir . '/' . $php2xliffModel->languagePath))
        {
            Message::addInfo($this->translator->trans('CONVERT_PHP_2_XLIFF.addValidLanguagePathFolder', [], 'contao_default'));
            return [];
        }



        $path = rtrim($php2xliffModel->languagePath, '/');

        // Search for language folders
        $finder = new Finder();
        $finder->in($this->projectDir.'/'.$path)
            ->directories()
        ;

        if (!$finder->hasResults()) {
            return [];
        }

        foreach ($finder as $folder) {
            $basename = $folder->getBasename();

            if ($basename === $this->php2XliffSourceLang) {
                $arrOptions[$basename] = $basename.' (source)';
            } else {
                $arrOptions[$basename] = $basename;
            }
        }

        return $arrOptions;
    }
}
