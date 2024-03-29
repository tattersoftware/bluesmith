<?php

namespace App\Actions;

use App\BaseAction;
use App\Entities\Invoice;
use App\Entities\Ledger;
use App\Libraries\Mailer;
use App\Models\ChargeModel;
use App\Models\LedgerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Entities\Job;

class InvoiceAction extends BaseAction
{
    public const HANDLER_ID = 'invoice';
    public const ATTRIBUTES = [
        'name'     => 'Invoice',
        'role'     => 'manageJobs',
        'icon'     => 'fas fa-receipt',
        'category' => 'Complete',
        'summary'  => 'Staff issues an invoice for actual charges',
        'header'   => 'Create Invoice',
        'button'   => 'Send Invoice',
    ];

    /**
     * Creates the initial invoice Ledger from the estimate Ledger.
     *
     * @param \App\Entities\Job $job
     */
    public static function up(Job $job): Job
    {
        // If there is no estimate then create a new one
        if (! $job->getInvoice()) {
            $ledgerId = model(LedgerModel::class)->insert([
                'job_id'   => $job->id,
                'estimate' => 0,
            ]);

            // Add Charges from the estimate
            foreach ($job->getEstimate(true)->charges ?? [] as $charge) {
                model(ChargeModel::class)->insert([
                    'ledger_id' => $ledgerId,
                    'name'      => $charge->name,
                    'amount'    => $charge->amount,
                    'quantity'  => $charge->quantity,
                ]);
            }
        }

        return $job;
    }

    /**
     * Displays the invoice form.
     */
    public function get(): ResponseInterface
    {
        return $this->render('actions/invoice', [
            'invoice' => $this->job->getInvoice(true),
        ]);
    }

    /**
     * Ends the Action
     *
     * @return null
     */
    public function post(): ?ResponseInterface
    {
        // Update the description and reload the Invoice
        model(LedgerModel::class)->update($this->job->invoice->id, [
            'description' => service('request')->getPost('description'),
        ]);

        /** @var Ledger $ledger */
        $ledger  = model(LedgerModel::class)->find($this->job->invoice->id);
        $invoice = new Invoice($ledger->toRawArray());

        // Verify each user and grab their email address
        $recipients = [];

        foreach (service('request')->getPost('users') ?? [] as $userId) {
            if (! is_numeric($userId)) {
                continue;
            }

            if ($user = model(UserModel::class)->find($userId)) {
                $recipients[] = $user->email;
            } else {
                alert('warning', 'Unable to locate user #' . $userId);
            }
        }

        if ($recipients) {
            // Send the email
            Mailer::forInvoice($recipients, $this->job, $invoice);
        }

        return null;
    }

    /**
     * Removes a single Charge.
     *
     * @return RedirectResponse
     */
    public function delete(): ResponseInterface
    {
        if ($chargeId = service('request')->getPost('charge_id')) {
            model(ChargeModel::class)->delete($chargeId);
            alert('warning', 'Charge removed.');
        }

        return redirect()->back();
    }

    /**
     * Adds a Charge to the invoice Ledger.
     *
     * @return RedirectResponse
     */
    public function put(): ResponseInterface
    {
        $data = service('request')->getPost();

        if (! isset($data['amount'])) {
            return redirect()->back()->withInput()->with('error', 'You must enter a price!');
        }

        // Convert the input into fractional money units
        $data['amount']    = scaled_to_price($data['amount']);
        $data['ledger_id'] = $this->job->invoice->id;

        if (empty($data['quantity'])) {
            unset($data['quantity']);
        }

        // Add the Charge
        if (! model(ChargeModel::class)->insert($data)) {
            return redirect()->back()->withInput()->with('error', implode(' ', model(ChargeModel::class)->errors()));
        }

        return redirect()->back();
    }
}
