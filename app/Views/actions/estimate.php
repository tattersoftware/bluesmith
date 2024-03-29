<?= $this->extend(config('Layouts')->manage) ?>
<?= $this->section('main') ?>

	<?= form_open('jobs/estimate/' . $job->id) ?>

		<?php if (empty($job->users)): ?>

		<p><em><?= lang('Actions.noClients') ?></em></p>
		<p class="text-danger"><?= lang('Actions.noClients') ?></p>

		<?php else: ?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">Client</th>
					<th scope="col">Email</th>
					<th scope="col" class="text-center">Include?</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach ($job->users as $user): ?>

				<tr>
					<td><?= $user->firstname ?> <?= $user->lastname ?></td>
					<td><?= $user->email ?></td>
					<td class="text-center"><input type="checkbox" name="users[]" value="<?= $user->id ?>" checked></td>
				</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

		<?php endif; ?>

		<h3>Charges</h3>

		<?php if (empty($estimate->charges)): ?>
		<p class="text-danger">No charges have been set.</p>
		<?php else: ?>
		<?= view('charges/table', ['mayDelete' => false, 'charges' => $estimate->charges]) ?>
		<?php endif; ?>

		<span class="float-right h3">Total: <?= $estimate->getTotal(true) ?></span>

		<h3>Additional Notes</h3>
		<textarea class="form-control mb-3" name="description" rows="8" placeholder="Additional notes..."><?= old('description') ?></textarea>

		<input class="btn btn-success" type="submit" name="complete" value="<?= $buttonText ?>">

	<?= form_close() ?>

<?= $this->endSection() ?>
