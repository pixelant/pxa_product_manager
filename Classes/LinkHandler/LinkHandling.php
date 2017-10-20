<?php

namespace Pixelant\PxaProductManager\LinkHandler;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
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

use TYPO3\CMS\Core\LinkHandling\LinkHandlingInterface;

class LinkHandling implements LinkHandlingInterface
{
    /**
     *
     *
     * @param array $parameters
     * @return string
     */
    public function asString(array $parameters): string
    {
        $urn = 't3://pxappm';

        if (!empty($parameters['product'])) {
            $urn .= '?product=' . $parameters['product'];
        } elseif (!empty($parameters['category'])) {
            $urn .= '?category=' . $parameters['category'];
        }

        return $urn;
    }

    /**
     * resolveHandlerData
     *
     * @param array $data
     * @return array
     */
    public function resolveHandlerData(array $data): array
    {
        $result = [];

        if (isset($data['product'])) {
            $result['product'] = $data['product'];

            unset($data['product']);
        } elseif (isset($data['category'])) {
            $result['category'] = $data['category'];

            unset($data['category']);
        }

        return $result;
    }
}
