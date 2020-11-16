<?php namespace App\Entities;

use App\Exceptions\InviteException;
use App\Libraries\Mailer;
use App\Models\InviteModel;
use App\Models\LedgerModel;
use CodeIgniter\I18n\Time;

class Job extends \Tatter\Workflows\Entities\Job
{
	use \Tatter\Relations\Traits\EntityTrait;

	protected $table      = 'jobs';
	protected $primaryKey = 'id';

	/**
	 * Stored invoice ledger
	 *
	 * @var Ledger|null
	 */
	protected $invoice;

	/**
	 * Stored estimate ledger
	 *
	 * @var Ledger|null
	 */
	protected $estimate;

	/**
	 * Gets the estimate.
	 *
	 * @return Ledger|null
	 */
	public function getEstimate(): ?Ledger
	{
		$this->ensureCreated();

		if (is_null($this->estimate))
		{
			$this->estimate = model(LedgerModel::class)
				->where('job_id', $this->attributes['id'])
				->where('estimate', 1)
				->first();
		}

		return $this->estimate;
	}

	/**
	 * Gets the invoice.
	 *
	 * @return Ledger|null
	 */
	public function getInvoice(): ?Ledger
	{
		$this->ensureCreated();

		if (is_null($this->invoice))
		{
			$this->invoice = model(LedgerModel::class)
				->where('job_id', $this->attributes['id'])
				->where('estimate', 0)
				->first();
		}

		return $this->invoice;
	}

	/**
	 * Creates an invitation to this job and sends it to
	 * the specified email address.
	 *
	 * @param string $recipient Email address to invite
	 *
	 * @return void
	 *
	 * @throws InviteException
	 */
	public function invite(string $recipient): void
	{
		$this->ensureCreated();

		// Check if invitations are allowed
		if (! config('Auth')->allowInvitations)
		{
			throw new InviteException(lang('Invite.disabled'));
		}

		// Make sure we have an issuer
		/** @var User|null $issuer */
		$issuer = user();
		if (! $issuer)
		{
			throw new InviteException(lang('Invite.noLogin'));
		}

		// Build the row		
		$row = [
			'job_id'     => $this->attributes['id'],
			'email'      => $recipient,
			'token'      => bin2hex(random_bytes(16)),
			'created_at' => date('Y-m-d H:i:s'),
		];

		// Check for expirations
		if ($seconds = config('Auth')->invitationLength)
		{
			$row['expired_at'] = (new Time("+{$seconds} seconds"))->toDateTimeString();
		}

		// Use the model to make the insert
		if (! model(InviteModel::class)->insert($row))
		{
			$error = implode(' ', model(InviteModel::class)->errors());
			throw new InviteException($error);
		}

		// Send the email
		Mailer::forJobInvite($issuer, $recipient, $this, $row['token']);
	}
}
