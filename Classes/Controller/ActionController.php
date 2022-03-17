<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Controller;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    protected function initializeAction(): void
    {
        parent::initializeAction();
        $this->settings['controller']['name'] = $this->request->getControllerName();
    }
}
