<html>
	<head>
		<title>ARMA - Arma Reveals Memory of Atys</title>
		<style type="text/css">
			body {background-color: #ffffff; color: #000000;}
			body, td, th, h1, h2 {font-family: sans-serif;}
			pre {margin: 0px; font-family: monospace;}
			a.mini_link {cursor:pointer; font-size:8px; font-variant:italic;}
			a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
			a:hover {text-decoration: underline;}
			table {border-collapse: collapse;}
			.center {text-align: center;}
			.center table { margin-left: auto; margin-right: auto; text-align: left;}
			.center th { text-align: center !important; }
			td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
			h1 {font-size: 150%; margin:0px;}
			h2 {font-size: 125%; margin:0px;}
			h3 {font-size: 110%; margin:0px;}
			.p {text-align: left;}
			.p1 {text-align: left; color: #FFFFFF; position:absolute; }
			.p2 {text-align: left; color: #000000; position:absolute; top:1px; left:1px;}
			.h {background-color: #007F0E; font-weight: bold; color: #FFFFFF;}
			.hh {background-color: #3F7F5F; font-weight: bold; color: #FFFFFF;}
			.e {background-color: #8BBAA3; font-weight: bold; color: #000000;}
			.v {background-color: #cccccc; color: #000000;}
			.vr {background-color: #cccccc; text-align: right; color: #000000;}
			img {float: right; border: 0px;}
			hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;} 
			.h td div {position:relative; margin-left:25px; margin-top:20px;}
			input {border:1px solid black; }
			.popup_chart {border:1px solid black; position:absolute; right:0px; top:0px; }
			.popup_charttransparent {border:1px solid black; position:absolute; right:0px; top:0px; filter : alpha(opacity=80); -moz-opacity : 0.8; opacity : 0.8; }
		</style>
		<script>
			function $(id){
				return document.getElementById(id);
			}
		</script>
	</head>
	<body>
		
		<div class="center">
		<table width="600" cellpadding="3" border="0">
			<tr class="h">
				<td>
					<a href="http://wiki.github.com/phantase/ARMA">
						<img border="0" alt="ARMA Logo" src="arma.png"/>
					</a>
					<div>
						<h1 class="p2">ARMA - Arma Reveals Memory of Atys</h1>
						<h1 class="p1">ARMA - Arma Reveals Memory of Atys</h1>
					</div>
				</td>
			</tr>
		</table>
		
		<p/>
		
<?php

// The time part of the Ryzom php API
require_once('ryzom_api/functions_time.php');

// A crazy function to use number with google charting
function ExtendedGoogleEncoding($number){
	if($number > 4095 )
		return "AA";	// Error: the Google fucking system is limited to 4095
	
	$q = $number / 64;
	$r = $number % 64;
	
	return EGECharacter($q).EGECharacter($r);
	
}
// Another crazy function to use number with google charting
function EGECharacter($number){
	if( $number >= 0 && $number <= 25 )
		return chr($number+65);
	if( $number >= 26 && $number <= 51 )
		return chr($number+71);
	if( $number >= 52 && $number <= 61 )
		return chr($number-4);
	if( $number == 62 )
		return chr(45);
	if( $number == 63 )
		return chr(46);
}

// Parameters for the database connection
include('dbb_params.inc.php');

// Connect to the database
$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

?>
		<table width="600" cellpadding="3" border="0">
			<tr class="h"><td colspan="2"><h2>Characters API</h2></td></tr>
<?php
// STATISTICS: count of APIKEY registered
$sql = "SELECT count(*) AS c FROM character_history ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Registered Character keys: </td> <td class=\"v\" >%s</td></tr>" , $data['c'] );

// STATISTICS: count of APIKEY from each race
$sql = "SELECT count(*) AS c, race FROM character_history GROUP BY race ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$cfyros = 0; $cmatis = 0; $ctryker = 0; $czorai = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['race'] ){
		case "Fyros":  $cfyros  = $data['c']; break;
		case "Matis":  $cmatis  = $data['c']; break;
		case "Tryker": $ctryker = $data['c']; break;
		case "Zorai":  $czorai  = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By race: <a class=\"mini_link\" onMouseOver=\"$('character_race_chart').style.display='block';\" onMouseOut=\"$('character_race_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"character_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",ExtendedGoogleEncoding($cfyros),ExtendedGoogleEncoding($cmatis),ExtendedGoogleEncoding($ctryker),ExtendedGoogleEncoding($czorai));
//printf("<div class=\"popup_chart\" id=\"character_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s,%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",$cfyros,$cmatis,$ctryker,$czorai);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Fyros:  </td> <td class=\"v\" >%s</td></tr>" , $cfyros  );
printf("<tr><td class=\"e\">Matis:  </td> <td class=\"v\" >%s</td></tr>" , $cmatis  );
printf("<tr><td class=\"e\">Tryker: </td> <td class=\"v\" >%s</td></tr>" , $ctryker );
printf("<tr><td class=\"e\">Zorai:  </td> <td class=\"v\" >%s</td></tr>" , $czorai  );
// STATISTICS: count of APIKEY from each gender
$sql = "SELECT count(*) AS c, gender FROM character_history GROUP BY gender ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$cfemale = 0; $cmale = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['gender'] ){
		case "f": $cfemale = $data['c']; break;
		case "m": $cmale   = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By gender: <a class=\"mini_link\" onMouseOver=\"$('character_gender_chart').style.display='block';\" onMouseOut=\"$('character_gender_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"character_gender_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s&chs=250x100&chl=Female|Male&chco=FF7FB6,7FC9FF\" /></div>",ExtendedGoogleEncoding($cfemale),ExtendedGoogleEncoding($cmale));
//printf("<div class=\"popup_chart\" id=\"character_gender_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s&chs=250x100&chl=Female|Male&chco=FF7FB6,7FC9FF\" /></div>",$cfemale,$cmale);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Female: </td> <td class=\"v\">%s</td>" , $cfemale );
printf("</tr>");
printf("<tr><td class=\"e\">Male:   </td> <td class=\"v\">%s</td>" , $cmale   );
printf("</tr>");

// STATISTICS: count of APIKEY from each shard
$sql = "SELECT count(*) AS c, shard FROM character_history GROUP BY shard ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$caniro = 0; $caristople = 0; $cleanon = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['shard'] ){
		case "aniro":     $caniro     = $data['c']; break;
		case "aristople": $caristople = $data['c']; break;
		case "leanon":    $cleanon    = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By shard: <a class=\"mini_link\" onMouseOver=\"$('character_shard_chart').style.display='block';\" onMouseOut=\"$('character_shard_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"character_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",ExtendedGoogleEncoding($caniro),ExtendedGoogleEncoding($caristople),ExtendedGoogleEncoding($cleanon));
//printf("<div class=\"popup_chart\" id=\"character_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",$caniro,$caristople,$cleanon);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Aniro:     </td> <td class=\"v\">%s</td></tr>" , $caniro );
printf("<tr><td class=\"e\">Aristople: </td> <td class=\"v\">%s</td></tr>" , $caristople );
printf("<tr><td class=\"e\">Leanon:    </td> <td class=\"v\">%s</td></tr>" , $cleanon );

printf("<tr><td class=\"hh\" colspan=\"2\">Important dates: </td>" );
// STATISTICS: date of the first APIKEY registered
$sql = "SELECT date_first AS date FROM character_history ORDER BY date_first ASC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the first registered Key: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );
// STATISTICS: date of the last APIKEY registered
$sql = "SELECT date_first AS date FROM character_history ORDER BY date_first DESC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the last registered Key: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );
// STATISTICS: date of the last  modification in APIKEY
$sql = "SELECT date_last AS date FROM character_history ORDER BY date_last DESC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the last change: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );

?>
			<tr class="hh"><td colspan="2"><h3>Tools</h3></td></tr>
			<tr><td class="e">Register a new Character key</td>
				<td class="v" >
					<form action="character_history.php" method="GET">
						<input type="text" name="key" size="40" />
						<input type="submit" />
					</form>
				</td>
			</tr>
		</table>
		
		<p/>

		<table width="600" cellpadding="3" border="0">
			<tr class="h"><td colspan="2"><h2>Guilds API</h2></td></tr>
			
<?php
// STASTITICS: count of APIKEY registered
$sql = "SELECT count(*) AS c FROM guild_history ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Registered Guild keys: </td> <td class=\"v\" >%s</td></tr>" , $data['c'] );

// STATISTICS: count of APIKEY from each race
$sql = "SELECT count(*) AS c, race FROM guild_history GROUP BY race ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$cfyros = 0; $cmatis = 0; $ctryker = 0; $czorai = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['race'] ){
		case "Fyros":  $cfyros  = $data['c']; break;
		case "Matis":  $cmatis  = $data['c']; break;
		case "Tryker": $ctryker = $data['c']; break;
		case "Zorai":  $czorai  = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By race: <a class=\"mini_link\" onMouseOver=\"$('guild_race_chart').style.display='block';\" onMouseOut=\"$('guild_race_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guild_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",ExtendedGoogleEncoding($cfyros),ExtendedGoogleEncoding($cmatis),ExtendedGoogleEncoding($ctryker),ExtendedGoogleEncoding($czorai));
//printf("<div class=\"popup_chart\" id=\"guild_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s,%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",$cfyros,$cmatis,$ctryker,$czorai);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Fyros:  </td> <td class=\"v\" >%s</td></tr>" , $cfyros  );
printf("<tr><td class=\"e\">Matis:  </td> <td class=\"v\" >%s</td></tr>" , $cmatis  );
printf("<tr><td class=\"e\">Tryker: </td> <td class=\"v\" >%s</td></tr>" , $ctryker );
printf("<tr><td class=\"e\">Zorai:  </td> <td class=\"v\" >%s</td></tr>" , $czorai  );

// STATISTICS: count of APIKEY from each shard
$sql = "SELECT count(*) AS c, shard FROM guild_history GROUP BY shard ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$caniro = 0; $caristople = 0; $cleanon = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['shard'] ){
		case "aniro":     $caniro     = $data['c']; break;
		case "aristople": $caristople = $data['c']; break;
		case "leanon":    $cleanon    = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By shard: <a class=\"mini_link\" onMouseOver=\"$('guild_shard_chart').style.display='block';\" onMouseOut=\"$('guild_shard_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guild_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s,%s,%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",ExtendedGoogleEncoding($caniro),ExtendedGoogleEncoding($caristople),ExtendedGoogleEncoding($cleanon));
//printf("<div class=\"popup_chart\" id=\"guild_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",$caniro,$caristople,$cleanon);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Aniro:     </td> <td class=\"v\">%s</td></tr>" , $caniro );
printf("<tr><td class=\"e\">Aristople: </td> <td class=\"v\">%s</td></tr>" , $caristople );
printf("<tr><td class=\"e\">Leanon:    </td> <td class=\"v\">%s</td></tr>" , $cleanon );

printf("<tr><td class=\"hh\" colspan=\"2\">Important dates: </td>" );
// STATISTICS: date of the first APIKEY registered
$sql = "SELECT date_first AS date FROM guild_history ORDER BY date_first ASC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the first registered Key: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );
// STATISTICS: date of the last APIKEY registered
$sql = "SELECT date_first AS date FROM guild_history ORDER BY date_first DESC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the last registered Key: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );
// STATISTICS: date of the last  modification in APIKEY
$sql = "SELECT date_last AS date FROM guild_history ORDER BY date_last DESC LIMIT 1";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
printf("<tr><td class=\"e\">Date of the last change: </td> <td class=\"v\" >%s</td></tr>" , ryzom_time_txt(ryzom_time_array($data['date'],'anyshard')) );

?>
			<tr class="hh"><td colspan="2"><h3>Tools</h3></td></tr>
			<tr><td class="e">Register a new Guild key</td>
				<td class="v">
					<form action="guild_history.php" method="GET">
						<input type="text" name="key" size="40" />
						<input type="submit" />
					</form>
				</td>
			</tr>
		</table>

		<p/>
		
		<table width="600" cellpadding="3" border="0">
			<tr class="h"><td colspan="2"><h2>Guilds list API</h2></td></tr>
<?php
// STATISTICS: count of guilds from each shard
$sql = "SELECT count(*) AS c, shardid FROM guilds_list GROUP BY shardid ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$caniro = 0; $caristople = 0; $cleanon = 0;
while( $data = mysql_fetch_array($req) ){
	switch( $data['shardid'] ){
		case "ani": $caniro     = $data['c']; break;
		case "ari": $caristople = $data['c']; break;
		case "lea": $cleanon    = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">By shard: <a class=\"mini_link\" onMouseOver=\"$('guilds_list_shard_chart').style.display='block';\" onMouseOut=\"$('guilds_list_shard_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guilds_list_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",ExtendedGoogleEncoding($caniro),ExtendedGoogleEncoding($caristople),ExtendedGoogleEncoding($cleanon));
//printf("<div class=\"popup_chart\" id=\"guilds_list_shard_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s&chs=250x100&chl=Aniro|Aristople|Leanon\" /></div>",$caniro,$caristople,$cleanon);
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Aniro:     </td> <td class=\"v\">%s</td></tr>" , $caniro );
printf("<tr><td class=\"e\">Aristople: </td> <td class=\"v\">%s</td></tr>" , $caristople );
printf("<tr><td class=\"e\">Leanon:    </td> <td class=\"v\">%s</td></tr>" , $cleanon );
// STATISTICS: count of guilds from each shard
$sql = "SELECT count(*) AS c, shardid,race FROM guilds_list GROUP BY shardid,race ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$caniroR = array('Fyros'=>0,'Matis'=>0,'Tryker'=>0,'Zorai'=>0); $caristople = array('Fyros'=>0,'Matis'=>0,'Tryker'=>0,'Zorai'=>0); $cleanon = array('Fyros'=>0,'Matis'=>0,'Tryker'=>0,'Zorai'=>0);
while( $data = mysql_fetch_array($req) ){
	switch( $data['shardid'] ){
		case "ani": $caniroR[$data['race']]     = $data['c']; break;
		case "ari": $caristopleR[$data['race']] = $data['c']; break;
		case "lea": $cleanonR[$data['race']]    = $data['c']; break;
	}
}
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">On Aniro by race: <a class=\"mini_link\" onMouseOver=\"$('guilds_list_aniro_race_chart').style.display='block';\" onMouseOut=\"$('guilds_list_aniro_race_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guilds_list_aniro_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",ExtendedGoogleEncoding($caniroR['Fyros']),ExtendedGoogleEncoding($caniroR['Matis']),ExtendedGoogleEncoding($caniroR['Tryker']),ExtendedGoogleEncoding($caniroR['Zorai']));
//printf("<div class=\"popup_chart\" id=\"guilds_list_aniro_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s,%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",($caniroR['Fyros']),($caniroR['Matis']),($caniroR['Tryker']),($caniroR['Zorai']));
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Fyros:  </td> <td class=\"v\">%s</td></tr>" , $caniroR['Fyros'] );
printf("<tr><td class=\"e\">Matis:  </td> <td class=\"v\">%s</td></tr>" , $caniroR['Matis'] );
printf("<tr><td class=\"e\">Tryker: </td> <td class=\"v\">%s</td></tr>" , $caniroR['Tryker'] );
printf("<tr><td class=\"e\">Zorai:  </td> <td class=\"v\">%s</td></tr>" , $caniroR['Zorai'] );
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">On Aristople by race: <a class=\"mini_link\" onMouseOver=\"$('guilds_list_aristople_race_chart').style.display='block';\" onMouseOut=\"$('guilds_list_aristople_race_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guilds_list_aristople_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",ExtendedGoogleEncoding($caristopleR['Fyros']),ExtendedGoogleEncoding($caristopleR['Matis']),ExtendedGoogleEncoding($caristopleR['Tryker']),ExtendedGoogleEncoding($caristopleR['Zorai']));
//printf("<div class=\"popup_chart\" id=\"guilds_list_aristople_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s,%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",($caristopleR['Fyros']),($caristopleR['Matis']),($caristopleR['Tryker']),($caristopleR['Zorai']));
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Fyros:  </td> <td class=\"v\">%s</td></tr>" , $caristopleR['Fyros'] );
printf("<tr><td class=\"e\">Matis:  </td> <td class=\"v\">%s</td></tr>" , $caristopleR['Matis'] );
printf("<tr><td class=\"e\">Tryker: </td> <td class=\"v\">%s</td></tr>" , $caristopleR['Tryker'] );
printf("<tr><td class=\"e\">Zorai:  </td> <td class=\"v\">%s</td></tr>" , $caristopleR['Zorai'] );
printf("<tr><td class=\"hh\" colspan=\"2\"><div style=\"position:relative;\">On Leanon by race: <a class=\"mini_link\" onMouseOver=\"$('guilds_list_leanon_race_chart').style.display='block';\" onMouseOut=\"$('guilds_list_leanon_race_chart').style.display='none';\">(view chart)</a>");
printf("<div class=\"popup_chart\" id=\"guilds_list_leanon_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=e:%s%s%s%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",ExtendedGoogleEncoding($cleanonR['Fyros']),ExtendedGoogleEncoding($cleanonR['Matis']),ExtendedGoogleEncoding($cleanonR['Tryker']),ExtendedGoogleEncoding($cleanonR['Zorai']));
//printf("<div class=\"popup_chart\" id=\"guilds_list_leanon_race_chart\" style=\"display:none;\"><img src=\"http://chart.apis.google.com/chart?cht=p3&chd=t:%s,%s,%s,%s&chs=250x100&chl=Fyros|Matis|Tryker|Zorai&chco=FF6A00,267F00,0094FF,57007F\" /></div>",($cleanonR['Fyros']),($cleanonR['Matis']),($cleanonR['Tryker']),($cleanonR['Zorai']));
printf("</div></td></tr>" );
printf("<tr><td class=\"e\">Fyros:  </td> <td class=\"v\">%s</td></tr>" , $cleanonR['Fyros'] );
printf("<tr><td class=\"e\">Matis:  </td> <td class=\"v\">%s</td></tr>" , $cleanonR['Matis'] );
printf("<tr><td class=\"e\">Tryker: </td> <td class=\"v\">%s</td></tr>" , $cleanonR['Tryker'] );
printf("<tr><td class=\"e\">Zorai:  </td> <td class=\"v\">%s</td></tr>" , $cleanonR['Zorai'] );
?>			
		</table>
		
<?php
mysql_close();
?>
		<p/>
		
		<table width="600" cellpadding="3" border="0">
			<tr class="h"><td colspan="2"><h2>WARNING</h2></td></tr>
			<tr class="v"><td colspan="2">
				<p>Don't register your key here if you don't trust this application.</p>
				<p>Your personnal data won't be revealed to another third party, just owners of the key are able to see the information.</p>
				<p>Don't share your key if you don't want to divulgate your character/guild information.</p>
				<p>The statistics presented here are only statistics for the current population of the Memory API, not the population of gaming shards.</p>
			</td></tr>
		</table>

		</div>
	</body>
</html>