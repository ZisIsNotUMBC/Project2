<?php
	function F($ub,$ue,$d,$ss){
		$now=date("Y-m-d H:i:s");
		$unow=strtotime($date);
		$b=date("Y-m-d H:i:s",$ub);
		$e=date("Y-m-d H:i:s",$ue);
		if($ub>$ue)print("<h3>ERROR: $b > $e</h3><br>");
		else if($ub<$unow)print("<h3>ERROR: $b ~ $e < NOW($now)</h3><br>");
		else if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND start<='$b' AND end>'$b'",'adv.A')))print("<h3>ERROR: Time conflict with $s[0] ~ $s[1]</h3><br>");
		else if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND start<'$e' AND end>='$e'",'adv.B')))print("<h3>ERROR: Time conflict with $s[0] ~ $s[1]</h3><br>");
		else if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND start>'$b' AND end<'$e'",'adv.C')))print("<h3>ERROR: Time conflict with $s[0] ~ $s[1]</h3><br>");
		else{
			$m=strtoupper($_POST['m']);
			$rt=$d->executeQuery("INSERT INTO i0(start,end,major,groupMax,adv) VALUES ('$b','$e','$M','$_POST[mg]','$ss[4]')",'adv.D');
			print("<h3>SUCCESS: Advising Time Set $b ~ $e</h3><br>");
		}
	}
	if($_POST['return'])header('Location:index.php');
	include('CommonMethods.php');
	$d=new Common(false);
	if($_POST['print']){
		if(strcmp(strtoupper($_POST['date']),"ALL")==0){
			$printb=date("Y-m-d H:i:s",strtotime("2000-01-01 00:00:00"));
			$printe=date("Y-m-d H:i:s",strtotime("2030-12-31 23:59:59"));
		}else{
			$printb=date("Y-m-d H:i:s",strtotime("$_POST[date] 00:00:00"));
			$printe=date("Y-m-d H:i:s",strtotime("$_POST[date] 23:99:99"));
		}
	}else{
		$printb=date("Y-m-d")." 00:00:00";
		$printe=date("Y-m-d")." 23:99:99";
	}
	$print="t>='$printb' AND t<='$printe'";
	$prints="start>='$printb' AND start<='$printe'";
	$rs=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'adv.E');
	if($ss=mysql_fetch_row($rs)){
		if($ss[6]==0)header("Location:main.php?ID=$_GET[ID]");
		print("<b>Individual Advising Today</b><br><table>");
		$rs=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND isAdv='0' AND $print AND isGroup=0 ORDER BY t",'adv.F');
		$c=0;
		while($s=mysql_fetch_row($rs)){
			if($c%2==0)print("<tr><td><b>$s[5]</b></td><td>$s[3]</td><td><b>$s[2]</b></td><td>$s[1]</td></tr>");
			else print("<tr><td><b>$s[5]</b></td><td>$s[3]</td><td><b>$s[2]</b></td><td>$s[1]</td></tr>");
			$c++;
		}
		print("</table><br><b>Group Advising Today</b><br><table>");
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND $prints AND groupMax>0 ORDER BY start",'adv.G');
		while($s=mysql_fetch_row($rs)){
			print("<tr><td>$s[0]</td><td>$s[2]/$s[4]</td><td>$s[3]</td></tr>");
			$rsb=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND t='$s[0]'",'adv.H');
			$c=0;
			while($sp=mysql_fetch_row($rsb)){
				if($c%2==0)print("<tr><td><div align='right'><b>$sp[1]</b></div></td><td>$sp[2]</td><td>$sp[3]</td></tr>");
				else print("<tr><td><div align='right'><b>$sp[1]</b></div></td><td>$sp[2]</td><td>$sp[3]</td></tr>");
				$c++;
			}
		}
		if($_POST['print'])die("</table><br><form action='adv.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='back' value='Go Back'></form>");
		print("</table><br><b>Free Time Today</b><br><font color='green'><table>");
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND $prints AND groupMax='0' ORDER BY start",'adv.I');
		while($s=mysql_fetch_row($rs))print("<tr><td>$s[0]</td><td>$s[1]</td><td>$s[3]</td><tr>");
		print("</table></font><br>");
		if($_POST['add']){
			$ub=strtotime("$_POST[date] $_POST[b]");
			$ue=strtotime("$_POST[date] $_POST[e]")-1800;
			F($ub,$ue,$d,$ss);
		}
		if($_POST['madd']){
			$ub=strtotime("$_POST[date] $_POST[b]");
			$ue=strtotime("$_POST[date] $_POST[e]")-1800;
			$l=intval($_POST['n']);
			for($i=0;$i<$l;$i++){
				F($ub,$ue,$d,$ss);
				$ub=$ub+604800;
				$ue=$ue+604800;
			}
		}
	}else header("Location:index.php?MSG=0");
	print("<div align='center'>Welcome, <b>$ss[1]</b>($ss[2]) from <b>$ss[3]</b>");
?>

<html>
<head>
	<title>Advisor UAP</title>
</head>
<body>
	<br>
	<form action=<?php print("'adv.php?ID=$_GET[ID]'"); ?> method='post' name='t'>
		Date:<input type='text' name='date' value='2015-5-10' size='10'><input type='submit' name='print' value='Print Time Table' title='Enter date (2015-5-10) to print the "Date" above.
		Enter ALL (case insensitive) to print all.'><br>
		Time From:<input type='text' name='b' value='9:00:00' size='8' title='Use 13:00:00 instead of 1:00:00 PM'> To:<input type='text' name='e' value='12:00:00' size='8' title='Use 13:00:00 instead of 1:00:00 PM'><br>
		Limited to Major:<input type='text' name='m' size='4' title='Change this only if you want to make this limited to a certain major'> Group Capacity:<select name='mg' title='Change this only if you want to make this a group advising time (12:00:00~12:30:00 or 12:30:00~13:00:00)'>
			<option value=0>0</option>
			<option value=5>5</option>
			<option value=6>6</option>
			<option value=7>7</option>
			<option value=8>8</option>
			<option value=9>9</option>
			<option value=10>10</option>
		</select><br>
		<input type='submit' name='add' value='Add Time'><input type='submit' name='return' value='Return to Login'><br>
		Repeat <input type='text' name='n' value='3' size='1'> Weeks:<input type='submit' name='madd' value='Add Multiple Time' title='E.G. for Repeat 3 Weeks:
		2015-5-10 9:00:00~12:00:00
		2015-5-17 9:00:00~12:00:00
		2015-5-22 9:00:00~12:00:00'><br><br>
	</form>
</div>

</body>
</html>
