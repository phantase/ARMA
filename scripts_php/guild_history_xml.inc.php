<?php

	/** NOT TO BE CALLED ALONE **/
	/**
	 * INPUT: $apikey
	 * OUTPUT: XML file
	 **/

function WriteHistoryFromSQL($sql){
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$f = mysql_num_fields($req);
	for( $i = 0; $i < $f; $i++ ){
		$previous[$i] = "";
		$xmltab[$i] = "";
	}
	while( $data = mysql_fetch_array($req) ){
		for( $i = 0; $i < $f; $i++ ){
			if( mysql_field_name($req,$i) != 'gid' && mysql_field_name($req,$i) != 'date' && $data[$i] != $previous[$i] ){
				$previous[$i] = $data[$i];
				$xmltab[$i] .= "<".mysql_field_name($req,$i)." date=\"".$data['date']."\">".$data[$i]."</".mysql_field_name($req,$i).">";	
			}
		}
	}
	for( $i = 0; $i < $f; $i++ ){
		$tag_name = mysql_field_name($req,$i);
		if( $tag_name != 'gid' && $tag_name != 'date' ){
			if( $xmltab[$i] != "" )
				print "<".$tag_name."_history>".$xmltab[$i]."</".$tag_name."_history>";
		}
	}
}
	 
// Retrieve the part parameter of request
if( isset($_GET['part']) ){
	$part = $_GET['part'];
} else {
	$part = "infos";
}

// Retrieve gid (guild id)
$sql = "SELECT * FROM guild_history WHERE apikey = '$apikey' ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_assoc($req);
$gid = $data['gid'];

	printf("<gid>%s</gid>",$gid);
	printf("<date_history><first>%s</first><last>%s</last></date_history>",$data['date_first'],$data['date_last']);
	
// Retrieve basic info history
$sql = "SELECT * FROM guild_info_history WHERE gid = '$gid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);

// Retrieve the fames history
print "<fames>";
$sql = "SELECT * FROM guild_fames_history WHERE gid = '$gid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</fames>";

?>