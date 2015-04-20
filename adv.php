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
		$printb=date("Y-m-d H:i:s",strtotime("$_POST[date] 9:00:00"));
		$printe=date("Y-m-d H:i:s",strtotime("$_POST[date] 16:00:00"));
		$print=" AND t>='$printb' AND t<='$printe' ";
	}else $print="";
	$rs=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'adv.E');	//check login validity (main.php?ID=xxx)
	if($ss=mysql_fetch_row($rs)){
		if($ss[6]==0)header("Location:main.php?ID=$_GET[ID]");
		$rs=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND isAdv=0 $print AND t>'0000-00-00 00:00:00' ORDER BY t",'adv.F');
		$c=0;
		print("<b>Time Table</b><br>");
		while($s=mysql_fetch_row($rs)){
			if($c%2==0)print("<font color='blue'>$s[3] <b>$s[5]</b> $s[2] <b>$s[1]</b><br></font>");
			else print("<font color='black'>$s[3] <b>$s[5]</b> $s[2] <b>$s[1]</b><br></font>");
			$c=$c+1;
		}
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND groupMax>0 ORDER BY start",'adv.G');
		while($s=mysql_fetch_row($rs)){
			print("<font color='red'>$s[0] $s[3] Group:$s[2]/$s[4]</font><br>");
			$rsb=$d->executeQuery("SELECT * FROM appts WHERE adv='$ss[4]' AND t='$s[0]'",'adv.H');
			$c=0;
			while($sp=mysql_fetch_row($rsb)){
				if($c%2==0)print("<font color='blue'> - - $sp[3] <b>$sp[2]</b> $sp[1]<br></font>");
				else print("<font color='black'> - - $sp[3] <b>$sp[2]</b> $sp[1]<br></font>");
				$c=$c+1;
			}
		}
		print("<br><b>Remaining Time</b><br><font color='green'>");
		$rs=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ss[4]' AND groupMax=0 ORDER BY start",'adv.I');
		while($s=mysql_fetch_row($rs))print("$s[0] ~ $s[1] : $s[3]<br>");
		print("</font><br>");
		if($_POST['add']){
			$ub=strtotime("$_POST[date] $_POST[b]");
			$ue=strtotime("$_POST[date] $_POST[e]");
			F($ub,$ue,$d,$ss);
		}
		if($_POST['madd']){
			$ub=strtotime("$_POST[date] $_POST[b]");
			$ue=strtotime("$_POST[date] $_POST[e]");
			$l=intval($_POST['n']);
			for($i=0;$i<$l;$i++){
				F($ub,$ue,$d,$ss);
				$ub=$ub+604800;
				$ue=$ue+604800;
			}
		}
	}else header("Locationindex.php?MSG=0");
	print("<div align='center'>Welcome, Advisor <b>$ss[1]</b>(<b>$ss[2]</b>) in <b>$ss[3]</b>");
?>

<html>
<head>
	<title>Undergraduate Advising Project Advisor Page</title>
</head>
<body>
	<form action=<?php print("'adv.php?ID=$_GET[ID]'"); ?> method='post' name='t'>
		Date:<input type='text' name='date' value="2015-4-10"><br>
		Time Start:<input type='text' name='b' value="9:00:00"><br>
		Time End:<input type='text' name='e' value="12:00:00"><br>
		Major:<input type='text' name='m' value=""><br>
		Group Capacity:<select name='mg'>
			<option value=0>0</option>
			<option value=5>5</option>
			<option value=6>6</option>
			<option value=7>7</option>
			<option value=8>8</option>
			<option value=9>9</option>
			<option value=10>10</option>
		</select><br>
		<input type='submit' name='print' value='Print Time Table for Specific Date'>
		<input type='submit' name='add' value='Add Time'>
		<input type='submit' name='return' value='Return to Login'><br>
		Repeat <textarea name="n" cols=1 rows=1>3</textarea> Weeks: <input type='submit' name='madd' value='Add Multiple Time'><br>
	</form>
</div>

<br>The "Time Table" is showing the Registed Individual Advisings and all Group Advisings.<br>
The "Remaining Time" table is showing the "free" time you still have<br>
<br>
<b>Print Time Table for Specific Date: </b>According to the "Date" box above, print the time table for that date.<br>
<br>
<b>Add Time: </b>To add time to "Avaliable/Remaining Time", Input Date(xxxx-xx-xx),Time Start(xx:xx:xx),Time End(xx:xx:xx). Enter Major to make the time only available to specific major, or enter nothing to make it general. Select Group Capacity 5~10 if you are creating an advising group, or just leave it 0 for individual time. 
<br>
<b>Add Multiple Time: </b>Enter time just like "Add Time", then input a number N for times which it would be repeated. The time would repeat for the following N-1 weaks.

</body>
</html>