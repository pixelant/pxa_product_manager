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
        $this->registerArgument('source', 'string', 'File path', true);
        $this->registerArgument('class', 'string', 'Specifies an alternate class for the svg', false);
        $this->registerArgument('width', 'int', 'Specifies a width for the svg', false);
        $this->registerArgument('height', 'int', 'Specifies a height for the svg', false);
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
        $arguments['source'] = ltrim($arguments['source'], '/');
        $sourceAbs = GeneralUtility::getFileAbsFileName($arguments['source']);

        if (!file_exists($sourceAbs)) {
            return 'no SVG file on /' . $arguments['source'];
        }

        return self::getInlineSvg($sourceAbs, $arguments);
    }

    /**
     * @param string $source
     * @param array $arguments
     * @return string
     */
    protected static function getInlineSvg($source, $arguments)
    {
        $svgContent = file_get_contents($source);
        $svgContent = preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $svgContent);
        // Disables the functionality to allow external entities to be loaded when parsing the XML, must be kept
        $previousValueOfEntityLoader = @libxml_disable_entity_loader(true);
        $svgElement = simplexml_load_string($svgContent);
        @libxml_disable_entity_loader($previousValueOfEntityLoader);

        // remove xml version tag
        $domXml = dom_import_simplexml($svgElement);
        if (isset($arguments['class'])) {
            $domXml->setAttribute('class', $arguments['class']);
        }
        if (isset($arguments['width'])) {
            $domXml->setAttribute('width', $arguments['width']);
        }
        if (isset($arguments['height'])) {
            $domXml->setAttribute('height', $arguments['height']);
        }

        return $domXml->ownerDocument->saveXML($domXml->ownerDocument->documentElement);
    }
}
