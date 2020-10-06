<?php
declare(strict_types=1);

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Middleware;

use Buepro\Timelog\Domain\Model\Client;
use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use Buepro\Timelog\Utility\DiUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class SendMailMiddleware
 * @link https://kronova.net/tutorials/typo3/extbase-fluid/use-middlewares-with-multilanguage.html
 */
class SendMailMiddleware implements MiddlewareInterface
{
    /**
     * @var StandaloneSubjectView
     */
    protected $standaloneSubjectView;

    /**
     * @var StandaloneBodyPlainView
     */
    protected $standaloneBodyPlainView;

    /**
     * @var StandaloneBodyHtmlView
     */
    protected $standaloneBodyHtmlView;

    public function __construct()
    {
        $this->standaloneSubjectView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneBodyPlainView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneBodyHtmlView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->initializeStandaloneView();
    }

    private function initializeStandaloneView(): void
    {
        $this->standaloneSubjectView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/Subject.html'
        );
        $this->standaloneBodyPlainView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/BodyPlain.html'
        );
        $this->standaloneBodyHtmlView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/BodyHtml.html'
        );
    }

    private function getErrorResponse(string $reasonPhrase)
    {
        $response = new \TYPO3\CMS\Core\Http\Response();
        return $response->withStatus(503, $reasonPhrase);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get url parameters
        $sendMailTo = $request->getParsedBody()['smt'] ?? $request->getQueryParams()['smt'] ?? null;
        $projectHandle = $request->getParsedBody()['tx_timelog_taskpanel']['projectHandle'] ??
            $request->getQueryParams()['tx_timelog_taskpanel']['projectHandle'] ?? null;

        if ($sendMailTo === 'client' && $projectHandle) {
            // Initialize project
            $projectUid = \Buepro\Timelog\Utility\GeneralUtility::decodeHashid(
                (string)$projectHandle,
                Project::class
            );
            /** @var Project $project */
            $project = DiUtility::getObject(ProjectRepository::class)->findByUid($projectUid);
            if (!$project) {
                return $this->getErrorResponse('Project isn\'t available.');
            }
            // Initializes client
            $client = $project->getClient();
            if (!$client || !$client->getEmail()) {
                return $this->getErrorResponse('Client email isn\'t available');
            }
            // Initialize tasks
            /** @var TaskRepository $taskRepository */
            $taskRepository = DiUtility::getObject(TaskRepository::class);
            $tasks = $taskRepository->findRecentForProject($project);
            // Initializes views
            $this->standaloneSubjectView->assignMultiple([
                'project' => $project,
                'tasks' => $tasks
            ]);
            $this->standaloneBodyPlainView->assignMultiple([
                'client' => $client,
                'project' => $project,
                'tasks' => $tasks
            ]);
            $this->standaloneBodyHtmlView->assignMultiple([
                'client' => $client,
                'project' => $project,
                'tasks' => $tasks
            ]);
            // Set email text and recipient
            $mail = GeneralUtility::makeInstance(MailMessage::class);
            $subject = trim($this->standaloneSubjectView->render());
            $htmlText = $this->standaloneBodyHtmlView->render();
            $plainText = $this->standaloneBodyPlainView->render();
            $mail
                ->setSubject($subject)
                ->setTo(\Buepro\Timelog\Utility\GeneralUtility::getEmailAddress($client))
                ->html($htmlText)
                ->text($plainText);
            // Set email sender ($client->ownerEamil overrides $owner->email)
            $from = '';
            if ($project->getOwner() && GeneralUtility::validEmail($project->getOwner()->getEmail())) {
                $from = \Buepro\Timelog\Utility\GeneralUtility::getEmailAddress($project->getOwner());
            }
            if (GeneralUtility::validEmail($client->getOwnerEmail())) {
                $from = [$client->getOwnerEmail()];
                if ($project->getOwner()) {
                    $project->getOwner()->setEmail($client->getOwnerEmail());
                    $from = \Buepro\Timelog\Utility\GeneralUtility::getEmailAddress($project->getOwner());
                }
            }
            if ($from) {
                $mail->setFrom($from);
                $mail->setCc($from);
            }
            // Set additional cc recipients
            if ($project->getCcEmail()) {
                $ccEmails = GeneralUtility::trimExplode(',', $project->getCcEmail());
                foreach ($ccEmails as $ccEmail) {
                    if (GeneralUtility::validEmail($ccEmail)) {
                        $mail->addCc($ccEmail);
                    }
                }
            }
            // Send email
            $mail->send();
            //return new HtmlResponse($htmlText);
            //return new HtmlResponse($plainText);
            return new NullResponse();
        }
        return $handler->handle($request);
    }
}
