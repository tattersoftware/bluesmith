<?= $this->setVar('menu', $menu ?? '')->extend('layouts/manage') ?>
<?= $this->section('main') ?>

<div class="container">

	<p>Delivery prompt here.</p>

	<?= form_open('jobs/deliver/' . $job->id) ?>

		<input class="btn btn-primary float-md-right" type="submit" name="save" value="<?= lang('Pub.saveContinue') ?>">	

	<?= form_close() ?>

</div>

<?= $this->endSection() ?>
