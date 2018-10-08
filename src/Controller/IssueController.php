<?php

namespace App\Controller;

use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IssueController.
 */
class IssueController extends Controller
{
    /**
     * @var GithubService
     */
    private $githubService;

    /**
     * IssueController constructor.
     *
     * @param GithubService $githubService
     */
    public function __construct(GithubService $githubService)
    {
        $this->githubService = $githubService;
    }

    /**
     * @Route("/{state}/{page}",
     *     methods={"GET"},
     *     requirements={"state"="open|closed"},
     *     defaults={"state"="open", "page"=1},
     *     name="issue_list"
     * )
     *
     * @param string                $state
     * @param int                   $page
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function issuesListAction(string $state, int $page, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUsername();
        $paginator = $this->get('knp_paginator');
        $issues = $this->githubService->getIssues($state, $page, $user);
        $openCount = $this->githubService->getIssueCount(GithubService::STATUS_OPEN, $user);
        $closedCount = $this->githubService->getIssueCount(GithubService::STATUS_CLOSED, $user);

        $count = GithubService::STATUS_CLOSED == $state ? $closedCount : $openCount;

        $pagination = $paginator->paginate(
            $issues['items'],
            $page,
            GithubService::ITEM_PER_PAGE
        );

        $pagination->setItems($issues['items']);
        $pagination->setTotalItemCount($count);

        return $this->render(
            '@app/issue/issue-list.html.twig',
            [
                'issues' => $pagination,
                'pagination' => $pagination,
                'open_issues' => $openCount,
                'closed_issues' => $closedCount,
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/{user}/{repository}/{issueId}",
     *     methods={"GET"},
     *     name="issue"
     * )
     *
     * @param string $user
     * @param string $repository
     * @param string $issueId
     *
     * @return Response
     */
    public function issueAction(string $user, string $repository, string $issueId)
    {
        $issue = $this->githubService->getIssue($user, $repository, $issueId);
        $comments = $this->githubService->getComments($user, $repository, $issueId);

        return $this->render(
            '@app/issue/issue.twig',
            [
                'issue' => $issue,
                'comments' => $comments,
            ]
        );
    }
}
