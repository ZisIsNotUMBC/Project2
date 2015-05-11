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
	if($_POST['sview'])header('Location:main.php?ID=ZZZZZZZ');
	include('CommonMethods.php');
	$d=new Common(false);
	if($_POST['print']){
		if(strcmp(strtoupper($_POST['date']),"ALL")==0){
			$printb=date("Y-m-d H:i:s",strtotime("2000-01-01 00:00:00"));
			$printe=date("Y-m-d H:i:s",strtotime("2030-12-31 23:59:59"));
		}else{
			$printb=date("Y-m-d H:i:s",strtotime("$_POST[date] 00:00:00"));
			$printe=date("Y-m-d H:i:s",strtotime("$_POST[date] 00:00:00")+259200);
		}
	}else{
		$printb=date("Y-m-d")." 00:00:00";
		$printe=date("Y-m-d H:i:s",strtotime($printb)+259200);
	}
	$print="t>='$printb' AND t<='$printe'";
	$prints="start>='$printb' AND start<='$printe'";
	$rs=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'adv.E');
	if($ss=mysql_fetch_row($rs)){
		if($ss[6]==0)header("Location:main.php?ID=$_GET[ID]");
		print("<div align='center'>Hover over buttons/texts/textboxes to find out function explaination</div><br>");
		if(!$_POST['print'])print("<b>Individual Advising In Future 3 Days</b><br><table>");else print("<b>Individual Advising In 3/ALL Days</b><br><table>");
		$rs=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND isAdv='0' AND $print AND isGroup=0 ORDER BY t",'adv.F');
		$c=0;
		while($s=mysql_fetch_row($rs)){
			if($c%2==0)print("<tr><td><font color='blue'>$s[5]</font></td><td><b>$s[2]</b></td><td>$s[1]</td><td><b>$s[3]</b></td></tr>");
			else print("<tr><td><font color='red'>$s[5]</font></td><td><b>$s[2]</b></td><td>$s[1]</td><td><b>$s[3]</b></td></tr>");
			$c++;
		}
		if(!$_POST['print'])print("</table><br><b>Group Advising In Future 3 Days</b><br><table>");else print("</table><br><b>Group Advising In 3/ALL Days</b><br><table>");
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND $prints AND groupMax>0 ORDER BY start",'adv.G');
		while($s=mysql_fetch_row($rs)){
			print("<tr><td>$s[0]</td><td>$s[2]/$s[4]</td><td>$s[3]</td></tr>");
			$rsb=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND t='$s[0]'",'adv.H');
			$c=0;
			while($sp=mysql_fetch_row($rsb)){
				if($c%2==0)print("<tr><td><div align='right'><font color='blue'>$sp[1]</font></div></td><td><b>$sp[2]</b></td><td>$sp[3]</td></tr>");
				else print("<tr><td><div align='right'><font color='red'>$sp[1]</font></div></td><td><b>$sp[2]</b></td><td>$sp[3]</td></tr>");
				$c++;
			}
		}
		if($_POST['print'])die("</table><br><form action='adv.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='back' value='Back'></form>");
		print("</table><br><b>Free Time In Future 3 Days</b><br><font color='green'><table>");
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND $prints AND groupMax='0' ORDER BY start",'adv.I');
		while($s=mysql_fetch_row($rs))print("<tr><td>$s[0]</td><td>$s[1]</td><td>$s[3]</td><tr>");
		print("</table></font><br>");
		$flag=1;
		if(($_POST['add'])or($_POST['madd'])){
			if($_POST[mg][0]=='0'){
				$ub=strtotime("$_POST[date] $_POST[b]");
				$ue=strtotime("$_POST[date] $_POST[e]")-1800;
			}else{
				if(strcmp($_POST[b],"12:00:00")==0){
					$ub=strtotime("$_POST[date] 12:00:00");
					$ue=strtotime("$_POST[date] 12:30:00");
				}else if(strcmp($_POST[b],"12:30:00")==0){
					$ub=strtotime("$_POST[date] 12:30:00");
					$ue=strtotime("$_POST[date] 13:00:00");
				}else{
					$flag=0;
					print("<h3>ERROR: Group Advising Must be at 12:00:00 OR 12:30:00</h3><br>");
				}
			}
		}
		if(($_POST['add'])and($flag==1))F($ub,$ue,$d,$ss);
		if(($_POST['madd'])and($flag==1)){
			$l=intval($_POST['n']);
			for($i=0;$i<$l;$i++){
				F($ub,$ue,$d,$ss);
				$ub=$ub+604800;
				$ue=$ue+604800;
			}
		}
		if($_POST['cancel']){
			if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE start='$_POST[date] $_POST[b]' AND adv='$ss[4]'",'adv.J'))){
				$d->executeQuery("DELETE FROM i0 WHERE start='$_POST[date] $_POST[b]' AND adv='$ss[4]'",'adv.K');
				$da=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND t='$_POST[date] $_POST[b]'",'adv.L');
				while($db=mysql_fetch_row($da))print("$db[1]'s($db[2]) Advising Cancelled. You might want to inform him/her.");
				$d->executeQuery("DELETE FROM appts WHERE adv='$ss[4]' AND t='$_POST[date] $_POST[b]'",'adv.M');
				print("<h3>SUCCESS: Time '$_POST[date] $_POST[b]' Cancelled </h3><br>");
			}else print("<h3>ERROR: Time '$_POST[date] $_POST[b]' Don't Exist </h3><br>");
		}
	}else header("Location:index.php?MSG=0");
	print("<div align='center'>Welcome, <b>$ss[1]</b>(<b>$ss[2]</b>) from <b>$ss[3]</b><br>");
?>

<html>
<head>
	<title>Advisor UAP</title>
</head>
<body>
	<br>
	<form action=<?php print("'adv.php?ID=$_GET[ID]'"); ?> method='post' name='t'>
		Date:<input type='text' name='date' value='2015-5-10' size='10'> <input type='submit' name='print' value='Print Time Table' title='Enter date (2015-5-10) to print the time table for 3 days start at "Date" above.
		Enter ALL (case insensitive) to print all.'><br>
		Time From:<input type='text' name='b' value='9:00:00' size='8' title='Use 13:00:00 instead of 1:00:00 PM'> To:<input type='text' name='e' value='12:00:00' size='8' title='Use 13:00:00 instead of 1:00:00 PM'><br>
		Limited to Major:<input type='text' name='m' size='4' title='Change this only if you want to make this limited to a certain major'> Group Capacity:<select name='mg' title='Change this only if you want to make this a group advising time (12:00:00~12:30:00 or 12:30:00~13:00:00)'>
			<option value=0>Individual</option>
			<option value=5>5</option>
			<option value=6>6</option>
			<option value=7>7</option>
			<option value=8>8</option>
			<option value=9>9</option>
			<option value=10>10</option>
		</select><br>
		<input type='submit' name='add' value='Add Time'> <input type='submit' name='cancel' value='Cancel Time' title='Notice that the student taking the group advising will be cancelled. You will not be able to cancel "taken" individual advising position. Please input the correct "period starting time" in the table.'><br>
		Repeat <input type='text' name='n' value='3' size='1'> Weeks:<input type='submit' name='madd' value='Add Multiple Time' title='E.G. for Repeat 3 Weeks:
		2015-5-10 9:00:00~12:00:00
		2015-5-17 9:00:00~12:00:00
		2015-5-22 9:00:00~12:00:00'><br>
		<input type='submit' name='sview' value='Student View'>
		<input type='submit' name='return' value='Return to Login'>
		<input type='submit' name='refresh' value='Refresh This Page'><br>
	</form>
</div>

</body>
</html>
