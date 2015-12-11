<?php
	
	if ( mysqli_error ( $db ) ){
		die(json_decode("{\"error\": 'Database Error'}", true));	
	}
	
	$q = mysqli_query ( $db, "SELECT * FROM possible_answers WHERE sentence_id = '".$_GET['sentence_id']."'");
	
	$index = 0;
	
	$possibles = array ();
	$CMD;
	while ( $row = mysqli_fetch_array ( $q, MYSQL_ASSOC ) ) {
		array_push ( $possibles, $row );
		$TCMD = new COMMAND_CENTRAL();
		$TCMD->command_format = $row['response_text'];
		$TCMD->command_response = "{\"correct\":true}";
		$CMD[$index] = $TCMD;
		$index++;
	}
	
	//var_dump ( $CMD );
	//echo json_encode ( $possibles );
	
	
	
	$request = new REQUEST_BUILDER();
?>