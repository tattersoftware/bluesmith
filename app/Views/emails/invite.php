<?php
$data = [
	'title'   => 'Job invitation',
	'preview' => 'Collaborate with ' . $issuer->firstname,
	'contact' => 'Company Inc, 3 Abbey Road, San Francisco CA 94102',
];

$this->setData($data)->extend('layouts/email', $data);
?>
<?= $this->section('main') ?>

  <td>
	<p>Hi there,</p>
	<p><?= $issuer->name ?> has invited you to collaborate on this job!</p>
	<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
	  <tbody>
		<tr>
		  <td align="left">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0">
			  <tbody>
				<tr>
				  <td> <a href="<?= site_url('emails/invite/' . $token) ?>" target="_blank">Accept Invitation</a> </td>
				</tr>
			  </tbody>
			</table>
		  </td>
		</tr>
	  </tbody>
	</table>
	<p>You will need to create an account to proceed, but it is free and easy.</p>
	<p>Some basic instructions go here.</p>
  </td>

<?= $this->endSection() ?>
