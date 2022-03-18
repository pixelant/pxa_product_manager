<?php

namespace Pixelant\PxaProductManager\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/*
 *
 *  Copyright notice
 *
 *  (c) 2016 Andrii Pozdieiev <andriy.p@pixelant.se>, Mosa Al-Husseini <mosa@pixelant.se>, Pixelant AB
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */
class SvgViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('source', 'string', 'Specifies the source file', false);
        $this->registerArgument('src', 'string', 'Specifies the source file', false);
        $this->registerArgument(
            'fileExtension',
            'string',
            'Specifies the file extension to display a file icon for, if not found fallbacks to default.svg',
            false
        );
        $this->registerArgument('class', 'string', 'Specifies an alternate class for the svg', false);
        $this->registerArgument('width', 'float', 'Specifies a width for the svg', false);
        $this->registerArgument('height', 'float', 'Specifies a height for the svg', false);
        $this->registerArgument('aria-label', 'string', 'Specifies an aria-label for the svg', false);
        $this->registerArgument('aria-hidden', 'bool', 'Specifies aria-hidden for the svg', false);
        $this->registerArgument('role', 'string', 'Specifies role attr for the svg', false);
        $this->registerArgument('focusable', 'string', 'Specifies focusable for the svg', false);
    }

    /**
     * Prepare svg output.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string svg content
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $relativeSrc = $arguments['src'] ?? ltrim($arguments['source'], '/') ?? '';
        $extension = $arguments['fileExtension'] ?? '';

        if (empty($relativeSrc) && empty($extension)) {
            return '<!-- nothing to render -->';
        }

        if (!empty($relativeSrc)) {
            try {
                $absoluteSrc = self::getValidatedAbsoluteFile($relativeSrc);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }

        if (empty($absoluteSrc) && !empty($extension)) {
            try {
                $absoluteSrc = self::getValidatedAbsoluteFile(
                    sprintf(
                        'EXT:pxa_product_manager/Resources/Public/Icons/FileIcons/%s.svg',
                        $extension
                    )
                );
            } catch (\Throwable $th) {
                $absoluteSrc = self::getValidatedAbsoluteFile(
                    'EXT:pxa_product_manager/Resources/Public/Icons/FileIcons/default.svg'
                );
            }
        }

        return self::getInlineSvg($absoluteSrc, $arguments);
    }

    /**
     * Get inline svg by absolute url.
     *
     * @param string $absoluteSrc
     * @param array $arguments
     * @return string
     * @throws \Exception
     */
    protected static function getInlineSvg(string $absoluteSrc, array $arguments): string
    {
        // Load svg content.
        $svgContent = GeneralUtility::getUrl($absoluteSrc);

        // Try and remove script tags.
        $svgContent = preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $svgContent);

        // Disables the functionality to allow external entities to be loaded when parsing the XML, must be kept.
        // Not needed since PHP 8.0 and libxml 2.9.0 entity substitution is disabled by default.
        if (PHP_VERSION_ID < 80000) {
            /** @codingStandardsIgnoreLine */
            $previousValueOfEntityLoader = libxml_disable_entity_loader(true);
        };

        $svgElement = simplexml_load_string($svgContent, 'SimpleXMLElement', LIBXML_NOENT);

        if (PHP_VERSION_ID < 80000) {
            /** @codingStandardsIgnoreLine */
            libxml_disable_entity_loader($previousValueOfEntityLoader);
        }

        // remove xml version tag
        $domXml = dom_import_simplexml($svgElement);

        self::setElementAttributes($domXml, $arguments);

        return $domXml->ownerDocument->saveXML($domXml->ownerDocument->documentElement);
    }

    /**
     * Set attributes on DOMElement.
     *
     * @param \DOMElement $domXml
     * @param array $arguments
     * @return void
     */
    protected static function setElementAttributes(\DOMElement $domXml, array $arguments): void
    {
        if (!empty($arguments['class'])) {
            if (empty($domXml->getAttribute('class'))) {
                $domXml->setAttribute('class', $arguments['class']);
            } else {
                $domXml->setAttribute('class', $domXml->getAttribute('class') . ' ' . $arguments['class']);
            }
        }
        if (isset($arguments['width'])) {
            $domXml->setAttribute('width', $arguments['width']);
        }
        if (isset($arguments['height'])) {
            $domXml->setAttribute('height', $arguments['height']);
        }
        if (!empty($arguments['role'])) {
            $domXml->setAttribute('role', $arguments['role']);
        }
        if (!empty($arguments['aria-label'])) {
            $domXml->setAttribute('aria-label', $arguments['aria-label']);
        }
        if (!empty($arguments['focusable'])) {
            $domXml->setAttribute('focusable', $arguments['focusable']);
        }
        if (!empty($arguments['aria-hidden']) && $arguments['aria-hidden'] === true) {
            $domXml->setAttribute('aria-hidden', 'true');
        }
    }

    /**
     * Get absoulute file name if file exists, and path is allowed and type is correct.
     *
     * @param string $relativeSrc
     * @return string
     * @throws \Exception
     */
    protected static function getValidatedAbsoluteFile(string $relativeSrc): string
    {
        $absoluteSrc = GeneralUtility::getFileAbsFileName($relativeSrc);

        if (!file_exists($absoluteSrc)) {
            throw new \Exception('<!-- unable to render file: ' . $relativeSrc . ' (missing) -->', 1647596185);
        }

        if (!GeneralUtility::isAllowedAbsPath($absoluteSrc)) {
            throw new \Exception('<!-- unable to render file: ' . $relativeSrc . ' (disallowed) -->', 1647596185);
        }

        $finfo = \mime_content_type($absoluteSrc);
        if (!in_array($finfo, ['image/svg+xml', 'image/svg'])) {
            throw new \Exception('<!-- unable to render file: ' . $relativeSrc . ' (' . $finfo . ') -->', 1647596185);
        }

        return $absoluteSrc;
    }
}
