<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Timelog',
    'description' => 'Increases the efficiency and transparency of work by continuously collecting work information and communicate it to customers.',
    'category' => 'plugin',
    'author' => 'Roman Büchler',
    'author_email' => 'rb@buechler.pro',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.7.2',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.20-11.5.99',
            'auxlibs' => '1.4.0-1.99.99',
            'pvh' => '1.1.0-1.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Buepro\\Timelog\\' => 'Classes'
        ],
    ],
];
