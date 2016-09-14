<?php
if (getenv('SSL_CLIENT_VERIFY') != 'SUCCESS') {
	header("Content-Type: text/markdown; charset=UTF-8");
	readfile("../README.md");
	exit(0);
}
include("../base.php");
?>
