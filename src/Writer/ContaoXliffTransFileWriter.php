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

namespace Markocupic\ContaoPhp2Xliff\Writer;

use Markocupic\ContaoPhp2Xliff\String\XmlSanitizer;

class ContaoXliffTransFileWriter implements WriterInterface
{
    protected const XLIFF_VERSION = '1.1';
    protected const FILE_DATATYPE = 'php';

    protected string $sourceLanguage;
    protected string $targetLanguage;
    protected string $originalFilePath;
    protected string $targetFilePath;
    protected array $arrTranslations;

    public function __construct(string $sourceLanguage, string $targetLanguage, string $originalFilePath, string $targetFilePath, array $arrSourceLangTranslations, array $arrTargetLangTranslations)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        $this->originalFilePath = $originalFilePath;
        $this->targetFilePath = $targetFilePath;
        $this->arrSourceLangTranslations = $arrSourceLangTranslations;
        $this->arrTargetLangTranslations = $arrTargetLangTranslations;
    }

    /**
     * @throws \DOMException
     */
    public function export(): bool
    {
        // First create the Xml Document
        $dom = $this->createXmlDocument();

        // Create root node
        $bodyNode = $this->addRootNodes($dom);

        $intAppendedItems = 0;

        // Add translation items
        foreach (array_keys($this->arrSourceLangTranslations) as $translationId) {
            // Do not add empty or unsetted values
            if (!isset($this->arrSourceLangTranslations[$translationId])) {
                continue;
            }

            if ('' === trim((string) $this->arrSourceLangTranslations[$translationId])) {
                continue;
            }

            if (!isset($this->arrTargetLangTranslations[$translationId])) {
                continue;
            }

            if ('' === trim((string) $this->arrTargetLangTranslations[$translationId])) {
                continue;
            }

            // Get the source translation slug
            $valueSource = $this->arrSourceLangTranslations[$translationId];

            // Get the target translation slug
            $valueTarget = $this->arrTargetLangTranslations[$translationId];

            // Append item
            $bodyNode->appendChild($this->createTranslationNode($dom, $translationId, $valueSource, $valueTarget));
            ++$intAppendedItems;
        }

        $bytes = false;

        if ($intAppendedItems) {
            $pathNew = \dirname($this->targetFilePath).'/'.basename($this->targetFilePath, '.php').'.xlf';
            $bytes = file_put_contents($pathNew, $dom->saveXML());
        }

        return (bool) $bytes;
    }

    /**
     * Create a new xml document.
     */
    protected function createXmlDocument(): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        return $dom;
    }

    /**
     * Add root nodes to a document.
     *
     * @throws \DOMException
     */
    protected function addRootNodes(\DOMDocument $dom): \DOMNode
    {
        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->appendChild(new \DOMAttr('version', self::XLIFF_VERSION));

        $fileNode = $xliff->appendChild($dom->createElement('file'));
        $fileNode->appendChild(new \DOMAttr('datatype', self::FILE_DATATYPE));
        $fileNode->appendChild(new \DOMAttr('original', $this->originalFilePath));
        $fileNode->appendChild(new \DOMAttr('source-language', $this->sourceLanguage));

        if ($this->sourceLanguage !== $this->targetLanguage) {
            $fileNode->appendChild(new \DOMAttr('target-language', $this->targetLanguage));
        }

        return $fileNode->appendChild($dom->createElement('body'));
    }

    /**
     * Create a new trans-unit node.
     *
     * @throws \DOMException
     *
     * @return \DOMElement|false
     */
    protected function createTranslationNode(\DOMDocument $dom, string $translationId, string $valueSource, ?string $valueTarget)
    {
        $translationNode = $dom->createElement('trans-unit');
        $translationNode->appendChild(new \DOMAttr('id', $translationId));

        $source = $dom->createElement('source');
        $source->textContent = XmlSanitizer::sanitize($valueSource);
        $translationNode->appendChild($source);

        if ($this->sourceLanguage !== $this->targetLanguage) {
            if ($valueTarget && '' !== $valueTarget) {
                $target = $dom->createElement('target');
                $target->textContent = XmlSanitizer::sanitize($valueTarget);
                $translationNode->appendChild($target);
            }
        }

        return $translationNode;
    }
}
