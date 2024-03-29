<?php

namespace App\Actions;

use App\Models\ChargeModel;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\ActionTrait;
use Tests\Support\AuthenticationTrait;
use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class EstimateTest extends ProjectTestCase
{
    use ActionTrait;
    use AuthenticationTrait;
    use DatabaseTestTrait;

    protected $namespace = [
        'Tatter\Files',
        'Tatter\Outbox',
        'Tatter\Settings',
        'Tatter\Themes',
        'Tatter\Workflows',
        'Myth\Auth',
        'App',
    ];

    /**
     * UID of the Action to test
     *
     * @var string
     */
    protected $actionId = 'estimate';

    /**
     * Creates an estimate Ledger and adds a Charge
     */
    protected function setUp(): void
    {
        parent::setUp();

        $estimate = $this->job->getEstimate(true);

        model(ChargeModel::class)->insert([
            'ledger_id' => $estimate->id,
            'name'      => 'Test Charge',
            'amount'    => 1000,
            'quantity'  => 2,
        ]);
    }

    public function testGetReturnsForm()
    {
        $response = $this->expectResponse('get');

        $response->assertSee('Charges', 'h3');
    }

    public function testPost()
    {
        $_POST = [
            'users'       => [$this->user->id],
            'description' => 'foobar',
        ];

        $this->expectNull('post');

        // Verify the description was updated
        $this->seeInDatabase('ledgers', ['description' => 'foobar']);

        // Verify an email was sent
        $this->seeInDatabase('emails_jobs', ['job_id' => $this->job->id]);
    }

    public function testPostInvalidUsers()
    {
        $_POST = [
            'users'       => [42, 'jimmy'],
            'description' => 'foobar',
        ];

        $this->expectNull('post');

        // Verify the description was still updated
        $this->seeInDatabase('ledgers', ['description' => 'foobar']);

        // Make sure no emails were sent
        $this->dontSeeInDatabase('emails_jobs', ['job_id' => $this->job->id]);

        // Check for the alert
        $this->assertSame([
            'warning' => ['Unable to locate user #42'],
        ], session()->getFlashData());
    }
}
