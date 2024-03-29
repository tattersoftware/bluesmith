<?php

namespace Tests\Support;

use App\Entities\Job;
use App\Models\JobModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\TestResponse;
use RuntimeException;
use Tatter\Assets\Test\AssetsTestTrait;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Action Test Trait
 *
 * Support components for testing Actions.
 *
 * @property string $actionId The Action to test. Must be set by child class.
 */
trait ActionTrait
{
    use AssetsTestTrait;

    /**
     * @var BaseAction
     */
    protected $action;

    /**
     * @var Job
     */
    protected $job;

    /**
     * Fakes a Job and initializes the Action identified by $actionId
     */
    protected function setUpActionTrait(): void
    {
        $this->setUpAssetsTestTrait();
        $this->publishAll();

        // Create a random Job for the Action
        fake(WorkflowModel::class);
        fake(StageModel::class);
        fake(StageModel::class);
        $this->job = fake(JobModel::class);

        // Locate the Action based on its UID
        if (! $class = ActionFactory::find($this->actionId)) {
            throw new RuntimeException('Unable to locate an Action for ' . $this->actionId);
        }

        // Create the Action
        $this->action = new $class($this->job);
    }

    /**
     * Calls an Action method expected to return `null`.
     */
    protected function expectNull(string $method): void
    {
        $result = $this->action->{$method}();

        $this->assertNull($result);
    }

    /**
     * Calls an Action method expected to return
     * a display response (i.e. not a redirect).
     */
    protected function expectResponse(string $method): TestResponse
    {
        return $this->getResponse($method, false);
    }

    /**
     * Calls an Action method expected to return
     * a display response (i.e. not a redirect).
     */
    protected function expectRedirect(string $method): TestResponse
    {
        return $this->getResponse($method, true);
    }

    /**
     * Calls an Action method expected to return
     * a `ResponseInterface`, verifies it, and returns
     * the response wrapped in a TestResponse.
     *
     * @param string $method   The Action method to call
     * @param bool   $redirect Whether to expect a redirect
     */
    private function getResponse(string $method, bool $redirect): TestResponse
    {
        $result = $this->action->{$method}();
        $this->assertInstanceOf(ResponseInterface::class, $result);

        $response = new TestResponse($result);
        $response->assertOK();

        $this->assertEquals($redirect, $response->isRedirect());

        return $response;
    }
}
