<?php
	header("Content-type: application/json");
	
	require_once("config.php");
	
	if ( mysqli_error ( $db ) ){
		die(json_decode("[error: 'Database Error']", true));	
	}
	
	$lesson = mysqli_query ( $db, "SELECT * FROM lesson_sentences WHERE lesson_id = '".$_GET['lesson_id']."'" );
	
	$sentences = array ( );
	while ( $row = mysqli_fetch_array ( $lesson, MYSQL_ASSOC ) ){
		//$row['lesson_speech'] = mb_convert_encoding($row['lesson_speech'], "UTF-8", "auto");
		array_push ( $sentences, $row ) ;
	}
	
	echo json_encode ( $sentences ) ;
	
?>