<?php
	$REQ_STRING = $_GET['req'];
	
	if ( substr( $REQ_STRING, 0, 1 ) == ' ' ){
		$REQ_STRING = substr( $REQ_STRING, 1 );
	}else{ }
	
	class COMMAND_CENTRAL{
		var $command_name ;
		
		var $command_format ;
		
		var $command_response ;	
	}
	
	class REQUEST_BUILDER{
		var $base_string = "UBERSNIP.BASE";
		
		var $tokens = array();
		
		var $db;
		
		function update_base_string ( $new_base_string ){
			$this->base_string = $new_base_string;	
		}
		
		function parse_request ( $CMDS, $chain = 0 ){
			$words = implode ( $this->base_string ) ;
			
			$cmd_fnd = false;
			$optionals=false;
			$disqualifies = false;
			$wildcard = false;
			$indexed = false;
			$isDisqualified = false;
			
			$fCMD;
			foreach ( $CMDS as $CMD ){
				$cmd_req_words = explode ( " ", $CMD->command_format );	
				
				$req = "";
				$disqualifies = false;
				
				$cmdindex = 0;
				foreach ( $cmd_req_words as $wd ){
					if ( strcmp($wd, "%s") === 0 ){
						$indexed=false;
						break;	
					}else if (strcmp($wd, "%s^**") === 0){ //DISQUALIFIER
						$disqualifies = true;
						break;
					}else if (strcmp($wd, "%a") === 0){ //FILTER
						$optionals = true;
						break;
					}else if (strcmp($wd, "%s**") === 0){ //WILDCARD
						$wildcard = true;
						break;
					}else if (strcmp($wd, "%i**") === 0){ //INDEX
						$indexed = true;
						break;
					}
					
					$req .= $wd . " ";
					
					$cmdindex++;
				}
				$isOptional = false;
				//echo $this->base_string;
				if ( $optionals ){
					$options = $cmd_req_words[sizeof($cmd_req_words)-1];
					//echo $options;
					
					$options = str_replace("{", "", $options);
					$options = str_replace("}", "", $options);
					
					$toptions = explode("|", $options);
					
					//echo substr($CMD->command_format, strpos($CMD->command_format, "%a")+3);
					
					$isOptional = false;
					$isIndexed = false;	
					foreach ( $toptions as $topt ){	
						
						if ( $this->base_string == substr($CMD->command_format, 0, strpos($CMD->command_format, "%a")) . $topt ){
							$isOptional=true;
							
						}
					}
				}
				
				if ( $disqualifies ){
					$options = $cmd_req_words[sizeof($cmd_req_words)-1];
					//echo $options;
					
					$options = str_replace("{", "", $options);
					$options = str_replace("}", "", $options);
					
					$toptions = explode("|", $options);
					
					//echo substr($CMD->command_format, strpos($CMD->command_format, "%a")+3);
					
					$isOptional = false;
					$isIndexed = false;	
					
					foreach ( $toptions as $topt ){	
						
						if ( $this->base_string == substr($CMD->command_format, 0, strpos($CMD->command_format, "%s^**")) . $topt ){
							$isDisqualified=true;
							$cmd_fnd=false;
						}
					}
				}
				
				if ( $indexed ){
					$options = $cmd_req_words[sizeof($cmd_req_words)-1];
					//echo $options;
					
					$options = str_replace("{", "", $options);
					$options = str_replace("}", "", $options);
					
					//echo $this->base_string."<br />";
					//echo $options; 
					$toptions = explode(".", $options);
					//echo "SELECT * FROM ".$toptions[0]." WHERE `".$toptions[1]."` = '".$this->base_string."'";
					
					$q = mysqli_query ( $this->db, "SELECT * FROM ".$toptions[0]." WHERE `".$toptions[1]."` = '".$this->base_string."'" );
					
					if ( mysqli_num_rows ( $q ) > 0 ){
						$isIndexed = true;
					}
					
				}
				
				if ( $isIndexed == false ){
				for($i = $cmdindex+1; $i < sizeof( $cmd_req_words ); $i++ ){
					
					$req .= $cmd_req_words[$i] . " ";
				}
			
				
				$nebase = explode(" ", $this->base_string);
				$nestring = "";
				
				$nindex = 0;
				foreach ( $nebase as $nbase ){
					if ( $nindex != $cmdindex ){
						$nestring .= $nindex != sizeof($nebase)-1 ? $nbase . " " : $nbase;
					}
					$nindex++;
				}
				}
				
				//echo "Req: " . $req . "<br />";
				//echo "Nestring: '".$nestring."'"."<br />".strcmp( $req, $nestring)."<br />"."<br />";
				//echo $isIndexed;
				
				if( ( $isOptional == true || $wildcard || $isIndexed ) || (strcmp( $req, $nestring) === 0 && $isIndexed == false && $nestring != '')){
					
					if ( $isIndexed == false ){
						
					}
					//GET SUB_STRING LENGTH OF BASE_STRING UP UNTIL REQ_STRING FOUND
					$search_sub_len = strlen(substr($this->base_string, strpos($this->base_string,$req), strlen($req)));
					
					$CMD->command_response = str_replace("{uber:request_param}", '"'.substr($this->base_string, strpos($this->base_string,$req)+$search_sub_len, strlen($this->base_string)).'"', $CMD->command_response);
					
						$cmd_fnd=true;
						$fCMD = $CMD;
						
						//echo $this->base_string;
					break;
					//FINAL RESPONSE
				} else{
						
				}
			}
			if ( !$cmd_fnd || $isDisqualified ) { echo '{"correct":false}'; }else if ( !$isDisqualified && $cmd_fnd){ echo $CMD->command_response; }
		}
		
	}
	
	require_once("config.php");
	require_once("commands.php");
	$request->db = $db;
	
	$request->update_base_string($REQ_STRING);
	
	$request->parse_request($CMD);
?>