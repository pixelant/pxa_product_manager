<?php
namespace Pixelant\PxaProductManager\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
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
 ***************************************************************/

use Pixelant\PxaProductManager\Utility\TemplateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderAdditionalButtonsViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
class RenderAdditionalButtonsViewHelper extends AbstractViewHelper
{
    /**
     * @var Dispatcher $signalSlotDispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @param Dispatcher $signalSlotDispatcher
     */
    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Render additional buttons on a single (on a single view page)
     *
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function render()
    {
        /**
         * Generate additional buttons
         * Should an array that follows this structure
         * [
         *     [
         *         'name' => 'Do something',
         *         'link' => 'https://www.example.com',
         *         'classes' => [],
         *         'order' => '100'
         *     ],
         *     [
         *         'name' => 'Buy',
         *         'link' => 'https://www.test.net',
         *         'classes' => ['beauty', 'clarence'],
         *         'order' => '20'
         *     ],
         * ]
         *
         * name - button text
         * link - button link
         * classes - array of additional button classes
         * order - used to specify buttons order
         */
        $buttons = [
        ];

        // Add a signal slot so other extension could add additional buttons
        $this->signalSlotDispatcher->dispatch(__CLASS__, 'BeforeProcessingAdditionalButtons', [&$buttons]);

        // Process
        foreach ($buttons as &$button) {
            $button['classes'] = empty($button['classes']) ? '' : implode(' ', $button['classes']);
        }
        unset($button);

        // Sort
        usort($buttons, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        // Render
        return TemplateUtility::generateStandaloneTemplate(
            'singleViewAdditionalButtons',
            [
                'buttons' => $buttons
            ]
        );
    }
}
