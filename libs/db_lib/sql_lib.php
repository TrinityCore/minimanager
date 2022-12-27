<?php

 
//##########################################################################################
//saves data from selected DB.table - Thanks to Assure for partial structurbackup code
function sql_table_dump ($dbhost, $dbuser, $dbpass, $database, $table, $construct, $file) {
 global $lang_global;

 $sql_0 = new SQL;
 $sql_0->connect($dbhost, $dbuser, $dbpass, $database, true);

 $fp = fopen($file, 'r+') or die (error($lang_backup['file_write_err']));
 fseek($fp,0,SEEK_END);
 
 fwrite($fp, "--\n")or die (error($lang_backup['file_write_err']));
 fwrite($fp, "-- Dump of $database.$table\n")or die (error($lang_backup['file_write_err']));
 fwrite($fp, "-- Dump DATE : " . date("m.d.y H:i:s") ."\n--\n\n")or die (error($lang_backup['file_write_err']));

 if($construct){
	fwrite($fp, "-- Table structure for table $database.$table\n")or die (error($lang_backup['file_write_err']));			
	if(!($fi = $sql_0->query("DESC ".$table))) error($sql_0->error());

	fwrite($fp, "DROP TABLE IF EXISTS $table;\n")or die (error($lang_backup['file_write_err']));
	$pri = "";
	$creatinfo = array();
	while($tmp = $sql_0->fetch_row($fi)){
		$con = "`".$tmp[0]."` ";
		$con .= trim($tmp[1]." ");
		if($tmp[2] != "YES") { $con .= " NOT NULL"; }
		if($tmp[4]) {
			if ($tmp[4] == 'CURRENT_TIMESTAMP' || $tmp[4] == 'timestamp') $con .= " default ".$tmp[4]; 
				else $con .= " default '".$tmp[4]."'"; 
			} else if($tmp[4] === '' && $tmp[3] != "PRI") { 
					$con .= " default ''"; 
					} else if(strlen($tmp[4])!=0) { 
							$con .= " default '0'"; 
							}
		if(strtolower($tmp[5]) == "auto_increment") { $con .= " auto_increment"; }
				
		$creatinfo[] = $con;
	}

	$fieldscon = implode(",\n\t", $creatinfo);
	
	fwrite($fp, "CREATE TABLE ".$table." (")or die (error($lang_backup['file_write_err']));
	fwrite($fp, "\n\t$fieldscon")or die (error($lang_backup['file_write_err']));

	$qkey = $sql_0->query("SHOW INDEX FROM ".$table);

	if($rkey = $sql_0->fetch_array($qkey)){
		$knames = array();
		$keys = array();
		do {
			$keys[$rkey["Key_name"]]["nonunique"] = $rkey["Non_unique"];
			if(!$rkey["Sub_part"]){
				$keys[$rkey["Key_name"]]["order"][$rkey["Seq_in_index"]-1] = $rkey["Column_name"];
			} else {
				$keys[$rkey["Key_name"]]["order"][$rkey["Seq_in_index"]-1] = $rkey["Column_name"]."(".$rkey["Sub_part"].")";
			}

			$flag = false;
			for($l=0; $l<sizeof($knames); $l++){
				if($knames[$l] == $rkey["Key_name"]) $flag = true;
				}

			if(!$flag) { $knames[] = $rkey["Key_name"]; }
		} while($rkey = $sql_0->fetch_array($qkey));

		for($kl=0; $kl<sizeof($knames); $kl++){
			if($knames[$kl] == "PRIMARY") {
				fwrite($fp, ",\n\tPRIMARY KEY")or die (error($lang_backup['file_write_err']));
			} else {
				if($keys[$knames[$kl]]["nonunique"] == "0") {
					fwrite($fp, ",\n\tUNIQUE `$knames[$kl]`")or die (error($lang_backup['file_write_err']));
				} else {
					fwrite($fp, ",\n\tKEY `$knames[$kl]`")or die (error($lang_backup['file_write_err']));
				}
			}
         $a=@implode("`,`", $keys[$knames[$kl]]["order"]);
		 fwrite($fp, " (`$a`)")or die (error($lang_backup['file_write_err']));
		}
	}

	$query_res = $sql_0->query("SHOW TABLE STATUS FROM $database WHERE Name = '$table'");
	$tmp = $sql_0->fetch_row($query_res);

	$query_charset = $sql_0->query("SHOW VARIABLES WHERE Variable_name = 'character_set_database'");

	$info = " ";
	if($tmp[1]) $info .= "ENGINE=$tmp[1] ";
	$info .= "DEFAULT CHARSET=".$sql_0->result($query_charset, 0, 'Value')." ";
	if($tmp[16]) $info .= strtoupper($tmp[16])." ";
	if($tmp[10]) $info .= "AUTO_INCREMENT=$tmp[10] ";
	if($tmp[17]) $info .= "COMMENT='$tmp[17]'";

	fwrite($fp, "\n)$info;\n\n")or die (error($lang_backup['file_write_err']));
 }

 $query = $sql_0->query("SELECT * FROM $table");
 $num_fields = $sql_0->num_fields($query);
 $numrow = $sql_0->num_rows($query);
 $row_counter = 0;
 
 if ($numrow){
	fwrite($fp, "-- Dumping data for table $database.$table\n")or die (error($lang_backup['file_write_err']));
	fwrite($fp, "LOCK TABLES $table WRITE;\n")or die (error($lang_backup['file_write_err']));
	fwrite($fp, "DELETE FROM $table;\n")or die (error($lang_backup['file_write_err']));

	fwrite($fp, "INSERT INTO $table (")or die (error($lang_backup['file_write_err']));
	for($count = 0; $count < $num_fields; $count++) {
		fwrite($fp, "`".$sql_0->field_name($query,$count)."`")or die (error($lang_backup['file_write_err']));
		if ($count < ($num_fields-1)) fwrite($fp, ",")or die (error($lang_backup['file_write_err']));
		}
	fwrite($fp, ") VALUES \n")or die (error($lang_backup['file_write_err']));
	for ($i =0; $i<$numrow; $i++) {
		$row_counter++;
		fwrite($fp, "\t(")or die (error($lang_backup['file_write_err']));
		$row = $sql_0->fetch_row($query);
		for($j=0; $j<$num_fields; $j++) {
			$row[$j] = addslashes($row[$j]);
			$row[$j] = preg_replace("\n","\\n",$row[$j]);
			if (isset($row[$j])) {
				if ($sql_0->field_type($j,$query) == "int") fwrite($fp, "$row[$j]")or die (error($lang_backup['file_write_err']));
					else fwrite($fp, "'$row[$j]'")or die (error($lang_backup['file_write_err']));
				}else fwrite($fp, "''")or die (error($lang_backup['file_write_err']));
			if ($j<($num_fields-1)) fwrite($fp, ",")or die (error($lang_backup['file_write_err']));
			}

		if ($row_counter >= 10) {
			fwrite($fp, ");\n")or die (error($lang_backup['file_write_err']));
			fwrite($fp, "INSERT INTO $table (")or die (error($lang_backup['file_write_err']));
			for($count = 0; $count < $num_fields; $count++) {
				fwrite($fp, "`".$sql_0->field_name($query,$count)."`")or die (error($lang_backup['file_write_err']));
			if ($count < ($num_fields-1)) fwrite($fp, ",")or die (error($lang_backup['file_write_err']));
			}
			fwrite($fp, ") VALUES \n")or die (error($lang_backup['file_write_err']));
	
			$row_counter = 0;
			} elseif ($i < ($numrow-1)) fwrite($fp, "),\n")or die (error($lang_backup['file_write_err']));
		}
	fwrite($fp, ");\n")or die (error($lang_backup['file_write_err']));
	fwrite($fp, "UNLOCK TABLES;\n")or die (error($lang_backup['file_write_err']));

 } else fwrite($fp, "-- EMPTY\n")or die (error($lang_backup['file_write_err']));
 
 $sql_0->close();
 fwrite($fp, "\n")or die (error($lang_backup['file_write_err']));
 fclose($fp);
}


//##########################################################################################
//executes given file into sql
function run_sql_script($dbhost, $dbuser, $dbpass, $dbname, $path, $unlink) {
	global $lang_global;

	$fp = fopen($path, 'r') or die (error("Couldn't Open File!"));
	$sql_1 = new SQL;
	$sql_1->connect($dbhost, $dbuser, $dbpass, $dbname);

	$query="";
	$queries=0;
	$linenumber=0;
	$inparents=false;

	while (!feof($fp)){
		$dumpline = "";
		while (!feof($fp) && substr ($dumpline, -1) != "\n"){
			$dumpline .= fgets($fp, 16384);
			}

		$dumpline=preg_replace("\r\n$", "\n", $dumpline);
		$dumpline=preg_replace("\r$", "\n", $dumpline);

		if (!$inparents){ 
			$skipline=false;
			if (!$inparents && (trim($dumpline)=="" || strpos ($dumpline, '#') === 0 || strpos ($dumpline, '-- ') === 0)){ 
				$skipline=true;
				}

			if ($skipline){
				$linenumber++;
				continue;
				}
		}

		$dumpline_deslashed = str_replace ("\\\\","",$dumpline);

		$parents=substr_count ($dumpline_deslashed, "'")-substr_count ($dumpline_deslashed, "\\'");
		if ($parents % 2 != 0)
			$inparents=!$inparents;

		$query .= $dumpline;

		if (preg_match(";$",trim($dumpline)) && !$inparents){ 
			if (!$sql_1->query(trim($query))){
				fclose($fp);
				if($unlink) unlink($path);
				$err = preg_replace ("\n","",$sql_1->error());
				$err = preg_replace ("\r\n$","",$err);
				$err = preg_replace ("\r$","",$err);
				error("SQL Error at the line: $linenumber in $path <br /> $err");
				break;
			}
			$queries++;
			$query="";
		}
		$linenumber++;
	}
	$sql_1->close();
	fclose($fp);
	return $queries;
}

?>