<?php
	$DB_HOST = "snipleecast.com";
	$DB_USER =  "public_iov";
	$DB_PASS = "publiciov1992";
	$DB_TABLE = "io_voice";
	$db = mysqli_connect ( $DB_HOST, $DB_USER, $DB_PASS, $DB_TABLE);
	mysqli_set_charset($db,"UTF8");
?>