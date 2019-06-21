<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<!--Mobile meta-data -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<meta name="description" content="Bluesmith 3D Print Job Management" />
	<meta name="keywords" content="Bluesmith,3D print,job,manage" />
	<meta name="author" content="Duke University OIT" />
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

	<title>Bluesmith<?= empty($title) ? '' : " | {$title}" ?></title>

	<!-- Favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url() ?>assets/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url() ?>assets/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url() ?>assets/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?= base_url() ?>assets/favicon/site.webmanifest">
	<link rel="mask-icon" href="<?= base_url() ?>assets/favicon/safari-pinned-tab.svg" color="#012169">
	<link rel="shortcut icon" href="<?= base_url() ?>assets/favicon/favicon.ico">
	<meta name="msapplication-TileColor" content="#2b5797">
	<meta name="msapplication-config" content="<?= base_url() ?>assets/favicon/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

	<?= service('assets')->display('css') ?>
	<?= service('alerts')->css() ?>
	<?= view('Tatter\Themes\Views\css') ?>

	<!-- CMS stylesheet -->
	<link href="<?=site_url('sections/stylesheet') ?>" rel="stylesheet" type="text/css" media="all" />

	<!-- Matomo -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
		var u="//pulse.oit.duke.edu/analytics/";
		_paq.push(['setTrackerUrl', u+'piwik.php']);
		_paq.push(['setSiteId', '1']);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<!-- End Matomo Code -->
</head>
<body>
	<nav class="navbar navbar-expand-lg <?= (theme()->name == 'Dark') ? 'navbar-dark' : 'navbar-light' ?>">
		<a class="navbar-brand" href="<?= site_url() ?>">
			<img src="assets/images/logo.png" width="30" height="30" class="d-inline-block align-top" alt="logo">
			Bluesmith
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href="<?= site_url() ?>">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= site_url('jobs') ?>">Jobs</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= site_url('users') ?>">Users</a>
				</li>
			</ul>
<?php
if (service('authentication')->user()):
?>
			<a class="navbar-text" href="<?=route_to('logout') ?>" id="login"><i class="fas fa-sign-out-alt"></i> Logout</a>
<?php
else:
?>
			<a class="navbar-text" href="<?=route_to('login') ?>" id="login"><i class="fas fa-unlock-alt"></i> Login</a>
    	</div>
<?php
endif;
?>
    	</div>
	</nav>
	
	<?= service('alerts')->display() ?>
	
	<main role="main" class="my-5">