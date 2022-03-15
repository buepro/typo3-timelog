<?php
declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Middleware;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
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
     * StandaloneSubjectView
     * @var StandaloneView
     */
    protected $standaloneSubjectView;

    /**
     * StandaloneBodyPlainView
     * @var StandaloneView
     */
    protected $standaloneBodyPlainView;

    /**
     * StandaloneBodyHtmlView
     * @var StandaloneView
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
        $response = new Response();
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
            /** @var ProjectRepository $projectRepository */
            $projectRepository = GeneralUtility::makeInstance(ProjectRepository::class);
            /** @var Project $project */
            $project = $projectRepository->findByUid($projectUid);
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
            $taskRepository = GeneralUtility::makeInstance(TaskRepository::class);
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
            /** @var MailMessage $mail */
            $mail = GeneralUtility::makeInstance(MailMessage::class);
            $subject = trim($this->standaloneSubjectView->render());
            $htmlText = $this->standaloneBodyHtmlView->render();
            $plainText = $this->standaloneBodyPlainView->render();
            $mail
                ->setSubject($subject)
                ->setTo($client->getEmailAddress())
                ->html($htmlText)
                ->text($plainText);
            // Set email sender ($client->ownerEamil overrides $owner->email)
            $from = '';
            if ($project->getOwner() && GeneralUtility::validEmail($project->getOwner()->getEmail())) {
                $from = $project->getOwner()->getEmailAddress();
            }
            if (GeneralUtility::validEmail($client->getOwnerEmail())) {
                $from = [$client->getOwnerEmail()];
                if ($project->getOwner()) {
                    $project->getOwner()->setEmail($client->getOwnerEmail());
                    $from = $project->getOwner()->getEmailAddress();
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
