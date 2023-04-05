<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<head>
	<style {csp-style-nonce}>
		body {
		  padding-top: 4.5rem;
		}

		.alert-pre {
		  word-wrap: break-word;
		  word-break: break-all;
		  white-space: pre-wrap;
		}
		</style>
</head>

<div class="jumbotron">

  <h1>Mystery App</h1>
  <p class="lead">This demo shows off my mystery app!</p>
  <?php if(! empty($userName)) : ?>
    <h4>Welcome <?= esc($userName)?>!</h4>
    <p>Use the navigation bar at the top of the page to get started.</p>
  

  
  <?php endif ?>
</div>
<?= $this->endSection() ?>
