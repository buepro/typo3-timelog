<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Utility;

use Buepro\Timelog\Domain\Model\Task;

class TaskUtility
{
    /** @param Task[] $tasks */
    public static function getActiveTimeForTasks(array $tasks): float
    {
        $result = 0.0;
        foreach ($tasks as $task) {
            $result += $task->getActiveTime();
        }
        return $result;
    }

    /** @param Task[] $tasks */
    public static function getHeapTimeForTasks(array $tasks): float
    {
        $result = 0.0;
        foreach ($tasks as $task) {
            if ($task->getBatchDate() === null) {
                $result += $task->getActiveTime();
            }
        }
        return $result;
    }

    /** @param Task[] $tasks */
    public static function getBatchTimeForTasks(array $tasks): float
    {
        $result = 0.0;
        foreach ($tasks as $task) {
            if ($task->getBatchDate() !== null) {
                $result += $task->getActiveTime();
            }
        }
        return $result;
    }
}
