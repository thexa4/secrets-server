<?php
function formatHtml($behavior) {
	?>
	<!DOCTYPE html>
	<html lang="en">
	  <head>
	    <title>Secrets Server</title>
	  </head>
	  <body>
	    <?php $behavior(); ?>
	  </body>
	</html>
	<?php
}

if (!file_exists(dirname(__FILE__) . "/config.php")) {
	http_response_code(503);

	formatHtml(function() {
		global $clientcn;
		?>
			<h1>Config file could not be found</h1>
			<p>Please copy config.php.sample to config.php and check the values.</p>
		<?php
	});

	die();
	
}

include(dirname(__FILE__) . "/config.php");

$clientcn = getenv('SSL_CLIENT_S_DN_CN');
if (preg_match("/[^.0-9a-zA-Z-]/", $clientcn) === true) {
	http_response_code(400);

	formatHtml(function() {
		global $clientcn;
		?>
			<h1>Unexpected characters in client certificate CN field</h1>
			<p>Please use a different certificate to authenticate with.</p>
			<pre><?= $clientcn ?></pre>
		<?php
	});	

	die();
}

function getIndex() {
	formatHtml(function() {
		global $clientcn, $config;
		?>
			<h1>Secrets Server</h1>
			<p>Welcome, <?= $clientcn ?></p>
			<h2>Available modules</h2>
			<ul>
			<?php
				foreach($config["modules_available"] as $module) {
					?>
						<li><a href="/<?= $module ?>" ><?= $module ?></a></li>
					<?php
				}
			?>
			</ul>
		<?php
	});
}

function createData($module, $clientcn, $destination) {
	global $config;
	$modulepath = $config["module_dir"] . "/$module";
	if (!file_exists($modulepath)) {
		http_response_code(501);

		formatHtml(function() {
			global $clientcn;
			?>
				<h1>Unable to create data</h1>
				<p>The module to generate this data could not be found. Please check the modules_dir parameter in config.php.</p>
			<?php
		});	
		die();
	}

	$argument = escapeshellarg($clientcn);
	$data = [];

	$return = -1;
	exec($modulepath . ' ' . $argument, $data, $return);
	if ($return != 0) {
		http_response_code(500);
		die("Unable to generate secret");
	}

	file_put_contents($destination, $data) || (http_response_code(500) && die("Unable to save secret"));
}

function getModule($module) {
	global $clientcn, $config;

	$datapath = $config["data_dir"] . "/$module/$clientcn";
	$folder = dirname($datapath);
	if (!file_exists($folder)) {
		mkdir($folder, 0750) || (http_response_code(500) && die("Unable to create data directory"));
	}

	if (!file_exists($datapath)) {
		createData($module, $clientcn, $datapath);
	}

	$type = mime_content_type($datapath);
	header("Content-Type: $type");
	readfile($datapath) || (http_response_code(500) && die("Unable to read secret"));
}

$request = substr(getenv('REQUEST_URI'), 1);

if ($request == "") {
	getIndex();
	die();
}

$module = $request;
if (!in_array($module, $config["modules_available"])) {
	http_response_code(404);

	formatHtml(function() {
		global $clientcn;
		?>
			<h1>Module not found</h1>
			<p>Please add this module to config.php to allow clients to fetch this data.</p>
		<?php
	});	
	die();
}

getModule($module);
die();
