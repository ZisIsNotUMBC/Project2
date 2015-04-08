<?php
	if($_POST['return'])header('Location:index.php');
	else if($_POST['helperI'])header("Location:helperI.php?ID=$_GET[ID]&ins=$_POST[i]");
	else if($_POST['helperG'])header("Location:helperG.php?ID=$_GET[ID]&ins=$_POST[i]");
	include('CommonMethods.php');
	$d=new Common(false);
	$ra=$d->executeQuery("SELECT * FROM appts WHERE sid='$_GET[ID]'",'main.A');
	if($ss=mysql_fetch_row($ra)){
		if($ss[6]==1)header("Location:adv.php?ID=$_GET[ID]");
		$i=intval($_POST['i']);
		$ut=strtotime($_POST['t']);
		$t=date('Y-m-d H:i:s',$ut);
		if($_POST['cancel']){
			if($ss[7]==1){
				$m=mysql_fetch_row($d->executeQuery("SELECT * FROM i$i WHERE b='$ss[5]'",'main.C'));
				$m=$m[2]-1;
				$rt=$d->executeQuery("UPDATE i$i SET m='$m' WHERE b='$ss[5]'",'main.I');
			}else{
				$te=date('Y-m-d H:i:s',strtotime($ss[5])+1800);
				$rt=$d->executeQuery("INSERT INTO i$i(b,e,m) VALUES ('$ss[5]','$te','$ss[3]')",'main.M');
			}
			$rt=$d->executeQuery("UPDATE appts SET t='0000-00-00 00:00:00',g=0 WHERE id='$ss[0]'",'main.I');
			header("Location:main.php?ID=$_GET[ID]");
		}
		$name=array();
		$namec=0;
		print("START &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;END  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; CAPACITY<br>");
		$rb=$d->executeQuery('SELECT * FROM i','main.B');
		while($s=mysql_fetch_row($rb)){
			$name[$namec]=$s[1];
			$namec=$namec+1;
			print("<b>$s[1]</b> <img src='p.png' height=15 width=15 title='$s[2]'></img><br>");
			$rc=$d->executeQuery("SELECT * FROM i$s[0] WHERE ( m='$ss[3]' OR m='' ) AND ( g='0' OR g!=mg ) ORDER BY b",'main.C');
			while($s=mysql_fetch_row($rc)){
				print("$s[0] ~ $s[1] : ");
				if($s[4]==0){
					print("<font color='blue'>Individual<br></font>");
				}else{
					print("<font color='red'>$s[2]/$s[3]<br></font>");
				}
			}
		}
		print("<div align='center'>Welcome, <b>$ss[1]</b>(<b>$ss[2]</b>) in <b>$ss[3]</b>");
		if(strtotime($ss[5])==943938000){
			if($_POST['indiv']){
				if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i$i WHERE b<='$t' AND e>='$t' AND mg=0 AND ( m='' OR m='$ss[3]' )",'main.G'))){
					$ute=$ut+1800;
					$te=date('Y-m-d H:i:s',$ute);
					$rb=$d->executeQuery("SELECT * FROM i$i WHERE b<='$t' AND e>'$t' AND ( m='' OR m='$ss[3]' )",'main.H');
					while($s=mysql_fetch_row($rb)){
						$ub=strtotime($s[0]);
						$ue=strtotime($s[1]);
						if(($ub>=$ut)&&($ue<=$ute)){
							$rt=$d->executeQuery("DELETE FROM i$i WHERE b='$s[0]'",'main.K');
						}else if(($ub<=$ut)&&($ue>=$ute)){
							$rt=$d->executeQuery("UPDATE i$i SET e='$t' WHERE b='$s[0]'",'main.I');
							$rt=$d->executeQuery("INSERT INTO i$i(b,e,m) VALUES ('$te','$s[1]','$ss[3]'')",'main.J');
						}else{
							$rt=$d->executeQuery("INSERT i$i SET e='$t' WHERE b='$s[0]'",'main.L');
						}
					}
					$rb=$d->executeQuery("SELECT * FROM i$i WHERE b<'$te' AND e>='$te' AND ( m='' OR m='$ss[3]' )",'main.M');
					while($s=mysql_fetch_row($rb)){
						$ub=strtotime($s[0]);
						$ue=strtotime($s[1]);
						if(($ub<=$ut)&&($ue>=$ute)){
							$rt=$d->executeQuery("UPDATE i$i SET e='$t' WHERE b='$s[0]'",'main.I');
							$rt=$d->executeQuery("INSERT INTO i$i(b,e,m) VALUES ('$te','$s[1]','$ss[3]'')",'main.J');
						}else{
							$rt=$d->executeQuery("INSERT i$i SET b='$te' WHERE b='$s[0]'",'main.L');
						}
					}
					$rt=$d->executeQuery("UPDATE appts SET inst='$i',t='$t',g='0' WHERE sid='$_GET[ID]'",'main.G');
					$rt=$name[$ss[4]];
					die("<h3>Advising: $t with $rt</h3><br><form action='main.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='cancel' value='Cancel Advising'><input type='submit' name='return' value='Return To Login'></form>");
				}else{
					print("<h3>Time Do Not Exist</h3><br>");
				}
			}else{
				if($_POST['jgroup'])if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i$i WHERE b='$t' AND mg>'0' AND g<mg AND ( m='' OR m='$ss[3]' )",'main.D'))){
					$n=$s[2]+1;
					$rt=$d->executeQuery("UPDATE i$i SET g=$n WHERE b='$s[0]'",'main.E');
					$rt=$d->executeQuery("UPDATE appts SET inst=$i,t='$s[0]',g='1' WHERE sid='$_GET[ID]'",'main.F');
					$rt=$name[$ss[4]];
					die("<h3>Advising: $ss[5] with $rt</h3><br><form action='main.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='cancel' value='Cancel Advising'><input type='submit' name='return' value='Return To Login'></form>");
				}else{
					print("<h3>Group Do Not Exist or Full</h3><br>");
				}
			}
		}else{
			$rt=$name[$ss[4]];
			die("<h3>Advising: $ss[5] with $rt</h3><br><form action='main.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='cancel' value='Cancel Advising'><input type='submit' name='return' value='Return To Login'></form>");
		}
	}else header('Location:index.php?MSG=0');
?>

<html><head><title>Undergraduate Advising Project</title></head><body>

<form action=<?php print("'main.php?ID=$_GET[ID]'"); ?> method='post' name='t'>
	<br>Advisor:<select name='i'><?php for($i=0;$i<$namec;$i++)print("<option value=$i>$name[$i]</option>") ?></select>
	Time:<input type='text' name='t' value=<?php print("'$_GET[t]'"); ?>><br>
	<input type='submit' name='indiv' value='New Individual Advising'>
	<input type='submit' name='jgroup' value='Join Group Advising'>
	<input type='submit' name='return' value='Return To Login'><br>
	<input type='submit' name='helperI' value='Individual Time Clicker'>
	<input type='submit' name='helperG' value='Group Time Clicker'><br><br>
</form>
</div>

Select 'Advisor', enter 'Time' above as 'xxxx-x-x x:x:x', click 'New Individual Advising' or 'Join Group Advising' to setup Advising.<br>
All time given above is the <b>starting time</b>, which lasts 30min.<br>

For CAPACITY=0/0 time periods, they are for 'New Individual Advising' .<br>
For CAPACITY=?/? time periods, they are for 'Join Group Advising'.<br><br>

<font color='blue'>Try 'Individual Time Clicker' AND 'Group Time Clicker' to "click" the time<br>
Remember to choose 'Advisor' before that, and click the correct button accoridng to your choice<br><br></font>

</body></html>
