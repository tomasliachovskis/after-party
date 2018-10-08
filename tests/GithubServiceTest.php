<?php

namespace App\Tests\Service;

use App\Service\GithubService;
use Github\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GithubServiceTest extends WebTestCase
{
    const GITHUB_USER = 'tomasliachovskis';
    const GITHUB_REPOSITORY = 'stuff';

    /**
     * @var GithubService
     */
    private $githubService;

    public function setUp()
    {
        self::bootKernel();

        $this->githubService = self::$kernel->getContainer()->get(GithubService::class);
    }

    public function testGetIssues()
    {
        $issues = $this->githubService->getIssue(self::GITHUB_USER, self::GITHUB_REPOSITORY, 1);

        $this->assertInternalType('array', $issues);
        $this->assertEquals('test', $issues['title']);
        $this->assertArrayHasKey('user', $issues);
    }

    public function testGetIssueCount()
    {
        $issueCount = $this->githubService->getIssueCount(GithubService::STATUS_OPEN, self::GITHUB_USER);

        $this->assertEquals(2, $issueCount);
    }

    public function testGetIssue()
    {
        $existingIsue = $this->githubService->getIssue(
            self::GITHUB_USER,
            self::GITHUB_REPOSITORY,
            4
        );

        $this->assertEquals('2issue', $existingIsue['title']);

        $this->expectException(RuntimeException::class);

        $this->githubService->getIssue(
            self::GITHUB_USER,
            self::GITHUB_REPOSITORY,
            100
        );
    }

    public function testGetComments()
    {
        $comments = $this->githubService->getComments(self::GITHUB_USER, self::GITHUB_REPOSITORY, 3);
        $noComments = $this->githubService->getComments(self::GITHUB_USER, self::GITHUB_REPOSITORY, 4);

        $this->assertCount(4, $comments);
        $this->assertCount(0, $noComments);
    }
}
