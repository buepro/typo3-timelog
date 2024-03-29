<?php
declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Middleware\Frontend;

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
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class SendMailMiddleware
 * @link https://kronova.net/tutorials/typo3/extbase-fluid/use-middlewares-with-multilanguage.html
 */
class SendMailMiddleware implements MiddlewareInterface
{
    protected StandaloneView $standaloneSubjectView;
    protected StandaloneView $standaloneBodyPlainView;
    protected StandaloneView $standaloneBodyHtmlView;

    private function initializeStandaloneViews(ServerRequestInterface $request): void
    {
        $this->standaloneSubjectView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneBodyPlainView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneBodyHtmlView = GeneralUtility::makeInstance(StandaloneView::class);

        $this->standaloneSubjectView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/Subject.html'
        );
        $this->standaloneBodyPlainView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/BodyPlain.html'
        );
        $this->standaloneBodyHtmlView->setTemplatePathAndFilename(
            'EXT:timelog/Resources/Private/Templates/Mail/BodyHtml.html'
        );

        $extbaseRequest = new Request($request->withAttribute('extbase', new ExtbaseRequestParameters()));
        $this->standaloneSubjectView->setRequest($extbaseRequest);
        $this->standaloneBodyHtmlView->setRequest($extbaseRequest);
        $this->standaloneBodyPlainView->setRequest($extbaseRequest);
    }

    private function getErrorResponse(string $reasonPhrase): Response
    {
        return (new Response())->withStatus(503, $reasonPhrase);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->initializeStandaloneViews($request);

        // Get url parameters
        $parsedBody = $request->getParsedBody();
        $parsedBody = is_array($parsedBody) ? $parsedBody : [];
        $sendMailTo = $parsedBody['smt'] ?? $request->getQueryParams()['smt'] ?? null;
        $projectHandle = $parsedBody['tx_timelog_taskpanel']['projectHandle'] ??
            $request->getQueryParams()['tx_timelog_taskpanel']['projectHandle'] ?? null;

        if ($sendMailTo === 'client' && $projectHandle) {
            // Initialize project
            $projectUid = \Buepro\Timelog\Utility\GeneralUtility::decodeHashid(
                (string)$projectHandle,
                Project::class
            );
            /** @var ProjectRepository $projectRepository */
            $projectRepository = GeneralUtility::makeInstance(ProjectRepository::class);
            /** @var Project|null $project */
            $project = $projectRepository->findByUid($projectUid);
            if ($project === null) {
                return $this->getErrorResponse('Project isn\'t available.');
            }
            // Initializes client
            if (
                ($client = $project->getClient()) === null ||
                $client->getEmailAddress() === null
            ) {
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
            if (
                $project->getOwner() !== null &&
                GeneralUtility::validEmail($project->getOwner()->getEmail())
            ) {
                $from = $project->getOwner()->getEmailAddress();
            }
            if (GeneralUtility::validEmail($client->getOwnerEmail())) {
                $from = [$client->getOwnerEmail()];
                if ($project->getOwner() !== null) {
                    $project->getOwner()->setEmail($client->getOwnerEmail());
                    $from = $project->getOwner()->getEmailAddress();
                }
            }
            if (is_array($from)) {
                $mail->setFrom($from);
                $mail->setCc($from);
            }
            // Set additional cc recipients
            if (GeneralUtility::validEmail($project->getCcEmail())) {
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
