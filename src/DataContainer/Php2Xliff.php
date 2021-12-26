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

class Php2Xliff
{
    private RequestStack $requestStack;

    private XliffFromPhp $xliffFromPhp;

    private string $projectDir;

    private string $php2XliffSourceLang;

    public function __construct(RequestStack $requestStack, XliffFromPhp $xliffFromPhp, string $projectDir, string $php2XliffSourceLang)
    {
        $this->requestStack = $requestStack;
        $this->xliffFromPhp = $xliffFromPhp;
        $this->projectDir = $projectDir;
        $this->php2XliffSourceLang = $php2XliffSourceLang;
    }

    /**
     * Onload callback.
     *
     * @Callback(table="tl_php2xliff", target="config.onload")
     */
    public function onloadCallback(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('convertphp2xliff' === $request->query->get('key')) {
            if (null !== ($php2xliffModel = Php2xliffModel::findByPk($dc->id))) {
                $targetLang = $php2xliffModel->targetLanguage;

                if ('' !== $php2xliffModel->languagePath && '' !== $targetLang) {
                    $path = sprintf(
                        '%s/%s/%s',
                        $this->projectDir,
                        rtrim($php2xliffModel->languagePath, '/'),
                        $targetLang,
                    );

                    $finder = new Finder();
                    $finder->in($path)->depth(0)->files()->name('*.php');

                    if ($finder->hasResults()) {
                        foreach ($finder as $targetLangFile) {
                            $targetLangFileBasename = $targetLangFile->getBasename();
                            $sourceLangFilePath = \dirname($targetLangFile->getRealPath(), 2).'/en/'.$targetLangFileBasename;

                            if (!is_file($sourceLangFilePath)) {
                                Message::addError(
                                    sprintf(
                                        'Skipped the %s translation of %s, because we cold not find the source lang file in "%s".',
                                        $targetLang,
                                        $targetLangFileBasename,
                                        str_replace(
                                            $this->projectDir.'/',
                                            '',
                                            $sourceLangFilePath
                                        )
                                    )
                                );
                                continue;
                            }

                            $targetLangFile = new File(str_replace($this->projectDir.'/', '', $targetLangFile->getRealPath()));
                            $sourceLangFile = new File(str_replace($this->projectDir.'/', '', $sourceLangFilePath));

                            $this->xliffFromPhp->generate($this->php2XliffSourceLang, $sourceLangFile, $targetLang, $targetLangFile);
                        }
                    } else {
                        Message::addInfo('Did not find any php language files to convert to the xliff format. Please check your folder- and language settings.');
                    }
                }
            }

            $href = Url::removeQueryString(['key']);
            Controller::redirect($href);
        }
    }

    /**
     * Load callback.
     *
     * @Callback(table="tl_php2xliff", target="fields.sourceLanguage.load")
     */
    public function sourceLanguageLoadCallback(string $varValue, DataContainer $dc)
    {
        return $this->php2XliffSourceLang;
    }

    /**
     * Buttons callback.
     *
     * @Callback(table="tl_php2xliff", target="fields.targetLanguage.options")
     */
    public function targetLanguageOptionsCallback(DataContainer $dc)
    {
        $arrOptions = [];

        if ($dc->id) {
            if ('' !== $dc->activeRecord->path) {
                if (null !== ($php2xliffModel = Php2xliffModel::findByPk($dc->id))) {
                    if ('' !== $php2xliffModel->languagePath) {
                        $path = rtrim($php2xliffModel->languagePath, '/');

                        // Search for language folders
                        $finder = new Finder();
                        $finder
                            ->in($this->projectDir.'/'.$path)
                            ->directories()
                        ;

                        if ($finder->hasResults()) {
                            foreach ($finder as $folder) {
                                $basename = $folder->getBasename();

                                if ($basename === $this->php2XliffSourceLang) {
                                    $arrOptions[$basename] = $basename.' (source)';
                                } else {
                                    $arrOptions[$basename] = $basename;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrOptions;
    }
}
