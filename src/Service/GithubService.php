<?php

namespace App\Service;

use Github\Api\Issue;
use Github\Api\Search as Search;

class GithubService
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    const ITEM_PER_PAGE = 5;

    /**
     * @var Search
     */
    private $githubSearchApi;

    /**
     * @var Issue
     */
    private $githubIssueApi;

    /**
     * GithubService constructor.
     * @param Search $githubSearchApi
     * @param Issue  $githubIssueApi
     */
    public function __construct(
        Search $githubSearchApi,
        Issue $githubIssueApi
    ) {
        $this->githubSearchApi = $githubSearchApi;
        $this->githubIssueApi = $githubIssueApi;
    }

    /**
     * @param string $state
     * @param int    $page
     * @param string $user
     * @return array
     */
    public function getIssues(string $state, int $page, string $user): array
    {
        $this->githubSearchApi->setPerPage(GithubService::ITEM_PER_PAGE);
        $this->githubSearchApi->setPage($page);

        return $this->githubSearchApi->issues(
            sprintf('assignee:%s state:%s', $user, $state)
        );
    }

    /**
     * @param string $status
     * @param string $user
     * @return int
     */
    public function getIssueCount(string $status, string $user): int
    {
        $this->githubSearchApi->setPerPage(1);
        $this->githubSearchApi->setPage(1);

        $result = $this->githubSearchApi->issues(
            sprintf('assignee:%s state:%s', $user, $status)
        );

        return $result['total_count'];
    }

    /**
     * @param string $user
     * @param string $repository
     * @param string $number
     * @return array
     */
    public function getIssue(string $user, string $repository, string $number): array
    {
        return $this->githubIssueApi->show($user, $repository, $number);
    }

    /**
     * @param string $user
     * @param string $repository
     * @param string $number
     * @return array
     */
    public function getComments(string $user, string $repository, string $number): array
    {
        return $this->githubIssueApi->comments()->all($user, $repository, $number);
    }
}
