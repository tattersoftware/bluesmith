<?php $this->extend(config('Layouts')->manage); ?>
<?php $this->section('main'); ?>

<!-- Page Heading -->
<h1 class="h3 mb-0 text-gray-800">Edit Option</h1>

<div class="row">
	<div class="col">
		<?= view('options/form', ['option' => $option]) ?>
	</div>
	<div class="col-md"></div>
	<div class="col-xl"></div>
</div>

<?= $this->endSection() ?>
