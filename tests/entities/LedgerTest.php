<?php

namespace App\Entities;

use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class LedgerTest extends ProjectTestCase
{
    private Ledger $ledger;

    /**
     * @var Charge[]
     */
    private array $charges = [];

    /**
     * Mocks the Settings service and creates a
     * test Ledger with some Charges.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test Ledger with some Charges
        $this->ledger = new Ledger([
            'id'          => 42,
            'job_id'      => 1,
            'description' => 'Test Ledger',
        ]);

        // Create some test Charges
        for ($i = 1; $i < 5; $i++) {
            $this->charges[] = new Charge([
                'name'      => $i,
                'ledger_id' => $this->ledger->id,
                'amount'    => $i * 1000,
            ]);
        }

        // Inject the Charges into the test Ledger
        $this->ledger->charges = $this->charges;
    }

    public function testGetTotalReturnsSum()
    {
        $result = $this->ledger->getTotal();

        $this->assertSame(10000, $result);
    }

    public function testGetTotalFormatted()
    {
        $result = $this->ledger->getTotal(true);

        $this->assertSame('$100.00', $result);
    }
}
