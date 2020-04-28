<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Utility;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\Container\Container as ExtbaseContainer;

/**
 * Provides utilities related to dependency injection.
 *
 */
class DiUtility implements SingletonInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ExtbaseContainer
     */
    protected $objectContainer;

    /**
     * DiUtility constructor.
     * @param ContainerInterface $container
     * @param ExtbaseContainer $objectContainer
     */
    public function __construct(ContainerInterface $container, ExtbaseContainer $objectContainer)
    {
        $this->container = $container;
        $this->objectContainer = $objectContainer;
    }

    /**
     * Substitutes \TYPO3\CMS\Extbase\Object\ObjectManager->get()
     *
     * @param string $className The name of the class to return an instance of
     * @param array<int,mixed> $constructorArguments
     * @return object The object instance
     */
    public static function getObject(string $className, ...$constructorArguments): object
    {
        $diUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(__CLASS__);
        if ($diUtility->container->has($className)) {
            $instance = $diUtility->container->get($className);
            if (is_object($instance)) {
                return $instance;
            }
        }
        return $diUtility->objectContainer->getInstance($className, $constructorArguments);
    }
}
