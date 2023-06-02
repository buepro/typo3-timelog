<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\UserFunc;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaUserFunc
{
    public function getProjectLabel(array &$parameters): void
    {
        $textLength = 30;
        $parts = [
            GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength),
            sprintf('%.1f, %.1f', ($parameters['row']['active_time'] ?? 0.0), ($parameters['row']['heap_time'] ?? 0.0)),
        ];
        if (
            isset($parameters['row']['client']) &&
            ($clientUid = $this->getChildUid($parameters['row']['client'])) > 0 &&
            ($client = BackendUtility::getRecord('fe_users', $clientUid)) !== null
        ) {
            $parts[] = $client['company'] ?? $client['name'] ?? $client['last_name'] ?? '';
        }
        if (($parameters['row']['handle'] ?? '') !== '') {
            $parts[] = $parameters['row']['handle'];
        }
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getTaskLabel(array &$parameters): void
    {
        $textLength = 20;
        $parts = [];
        if (($parameters['row']['title'] ?? '') !== '') {
            $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength * 2);
        } elseif (($parameters['row']['description'] ?? '') !== '') {
            $parts[] = GeneralUtility::fixed_lgd_cs($parameters['row']['description'], $textLength * 2);
        }
        if (($parameters['row']['active_time'] ?? '') !== '') {
            $parts[] = sprintf('%.1f', $parameters['row']['active_time']);
        }
        if (
            isset($parameters['row']['project']) &&
            ($projectUid = $this->getChildUid($parameters['row']['project'])) > 0 &&
            ($project = BackendUtility::getRecord('tx_timelog_domain_model_project', $projectUid)) !== null
        ) {
            if (($project['title'] ?? '') !== '') {
                $parts[] = GeneralUtility::fixed_lgd_cs($project['title'], $textLength);
            }
            if (
                ($clientUid = (int)($project['client'] ?? 0)) !== 0 &&
                ($client = BackendUtility::getRecord('fe_users', $clientUid)) !== null
            ) {
                $parts[] = $client['company'] ?? $client['name'] ?? $client['last_name'] ?? '';
            }
        }
        if (($parameters['row']['handle'] ?? '') !== '') {
            $parts[] = $parameters['row']['handle'];
        }
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getTaskGroupLabel(array &$parameters): void
    {
        $textLength = 30;
        $parts = [
            GeneralUtility::fixed_lgd_cs($parameters['row']['title'], $textLength),
            sprintf('%.1f, %.1f', ($parameters['row']['active_time'] ?? 0.0), ($parameters['row']['time_deviation'] ?? 0.0)),
        ];
        if (
            isset($parameters['row']['project']) &&
            ($projectUid = $this->getChildUid($parameters['row']['project'])) > 0 &&
            ($project = BackendUtility::getRecord('tx_timelog_domain_model_project', $projectUid)) !== null
        ) {
            $parts[] = GeneralUtility::fixed_lgd_cs($project['title'], $textLength);
        }
        if (($parameters['row']['handle'] ?? '') !== '') {
            $parts[] = $parameters['row']['handle'];
        }
        $parameters['title'] = implode(' - ', array_filter($parts));
    }

    public function getIntervalLabel(array &$parameters): void
    {
        $interval = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        if ($interval === null || !isset($interval['task']) || !(bool)$interval['task']) {
            return;
        }
        if (
            ($task = BackendUtility::getRecord('tx_timelog_domain_model_task', $interval['task'])) !== null &&
            isset($task['title'], $interval['start_time'], $interval['duration'])
        ) {
            $parameters['title'] = sprintf(
                '%s (%s - %.2f)',
                $task['title'],
                BackendUtility::datetime($interval['start_time']),
                $interval['duration']
            );
        }
    }

    public function getFeUsersLabel(array &$parameters): void
    {
        $items = [];

        // Add username
        $items[] = $parameters['row']['username'];

        // Add name
        if (
            ($name = trim($parameters['row']['name'] ?? '')) !== '' ||
            ($name = trim(($parameters['row']['first_name'] ?? '') . ' ' . ($parameters['row']['last_name'] ?? ''))) !== ''
        ) {
            $items[] = $name;
        }

        // Add company
        if (($parameters['row']['company'] ?? '') !== '') {
            $items[] = $parameters['row']['company'];
        }

        // Add path
        if (($parameters['entry']['path'] ?? '') !== '') {
            $items[] =  $parameters['entry']['path'];
        }

        // Compile text
        $parameters['entry']['label'] = implode(' | ', $items);
    }

    /**
     * @param array|int $field
     * @return int
     */
    protected function getChildUid($field): int
    {
        return (int)(is_array($field) && isset($field[0]) ? $field[0] : $field);
    }
}
