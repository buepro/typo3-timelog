<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\UserFunc;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaUserFunc
{
    public function getProjectLabel(&$parameters)
    {
        $textLength = 30;
        $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength);
        $parts[] = sprintf('%.1f, %.1f', $parameters['row']['active_time'], $parameters['row']['heap_time']);
        if ($parameters['row']['client']) {
            $client = BackendUtility::getRecord('fe_users', $parameters['row']['client']);
            $parts[] = $client['company'] ?? $client['name'] ?? $client['last_name'] ?? '';
        }
        $parts[] = $parameters['row']['handle'];
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getTaskLabel(&$parameters)
    {
        $textLength = 20;
        if ($parameters['row']['title']) {
            $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength * 2);
        } else {
            $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['description'], $textLength * 2);
        }
        $parts[] = sprintf('%.1f', $parameters['row']['active_time']);
        if ($parameters['row']['project']) {
            $project = BackendUtility::getRecord('tx_timelog_domain_model_project', $parameters['row']['project']);
            if ($project && $project['title']) {
                $parts[] = GeneralUtility::fixed_lgd_cs($project['title'], $textLength);
            }
            if ($project && $project['client']) {
                $client = BackendUtility::getRecord('fe_users', $project['client']);
                $parts[] = $client['company'] ?? $client['name'] ?? $client['last_name'] ?? '';
            }
        }
        $parts[] = $parameters['row']['handle'];
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getTaskGroupLabel(&$parameters)
    {
        $textLength = 30;
        $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength);
        $parts[] = sprintf('%.1f, %.1f', $parameters['row']['active_time'], $parameters['row']['time_deviation']);
        if ($parameters['row']['project']) {
            $project = BackendUtility::getRecord('tx_timelog_domain_model_project', $parameters['row']['project']);
            $parts[] = GeneralUtility::fixed_lgd_cs($project['title'], $textLength);
        }
        if ($parameters['row']['handle']) {
            $parts[] = $parameters['row']['handle'];
        }
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getIntervalLabel(&$parameters)
    {
        $interval = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        if (!$interval['task']) {
            return;
        }
        $task = BackendUtility::getRecord('tx_timelog_domain_model_task', $interval['task']);
        $parameters['title'] = sprintf(
            '%s (%s - %.2f)',
            $task['title'],
            BackendUtility::datetime($interval['start_time']),
            $interval['duration']
        );
    }

    public function getFeUsersLabel(&$parameters)
    {
        // Default text (contains username and path)
        $text = explode('<br />', $parameters['entry']['text']);

        // Adds username
        $items[] = $text[0];

        // Adds name
        if ($parameters['row']['name']) {
            $items[] = $parameters['row']['name'];
        } elseif ($parameters['row']['first_name'] || $parameters['row']['last_name']) {
            if ($parameters['row']['first_name']) {
                $name[] = $parameters['row']['first_name'];
            }
            if ($parameters['row']['last_name']) {
                $name[] = $parameters['row']['last_name'];
            }
            $items[] = implode(' ', $name);
        }
        // Adds company
        if ($parameters['row']['company']) {
            $items[] = $parameters['row']['company'];
        }

        // Compiles text and adds path
        $parameters['entry']['text'] = implode(' | ', $items) . '<br />' . $text[1];
    }
}
