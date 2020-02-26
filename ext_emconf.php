<?php

/*
 * This file is part of the package buepro/timelog.
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
    'version' => '1.5.3-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'auxlibs' => '1.2.1-1.99.99',
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
