<?php
	if($_POST['return'])header('Location:index.php');
	else if($_POST['helperI'])header("Location:helperI.php?ID=$_GET[ID]&ins=$_POST[i]");
	else if($_POST['helperG'])header("Location:helperG.php?ID=$_GET[ID]&ins=$_POST[i]");
	include('CommonMethods.php');
	$d=new Common(false);
	$ra=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'main.A');
	if($ss=mysql_fetch_row($ra)){
		if($ss[6]==1)header("Location:adv.php?ID=$_GET[ID]");
		$i=intval($_POST['i']);
		$ut=strtotime($_POST['t']);
		$t=date('Y-m-d H:i:s',$ut);
		if($_POST['cancel']){
			if($ss[7]==1){
				$m=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE start='$ss[5]' AND adv='$ss[4]'",'main.B'));
				$m=$m[2]-1;
				$rt=$d->executeQuery("UPDATE i0 SET groupNow='$m' WHERE start='$ss[5]' AND adv='$ss[4]'",'main.C');
			}else{
				$te=date('Y-m-d H:i:s',strtotime($ss[5])+1800);
				$rt=$d->executeQuery("INSERT INTO i0(start,end,major,adv) VALUES ('$ss[5]','$te','$ss[3]','$ss[4]')",'main.D');
			}
			$rt=$d->executeQuery("UPDATE appts SET t='0000-00-00 00:00:00',isGroup=0 WHERE i='$ss[0]'",'main.E');
			header("Location:main.php?ID=$_GET[ID]");
		}
		$name=array();
		$namec=0;
		print("Notice that you can actually hang your mouse on '?' to check info, click 'HIDE' to hide some advisor and clikc the time below.<br>START &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;END  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; CAPACITY<br>");
		$rb=$d->executeQuery('SELECT * FROM i','main.F');
		while($s=mysql_fetch_row($rb)){
			$name[$namec]=$s[1];
			$namec=$namec+1;
			print("<div id='e$namec'><b>$s[1]</b> <img src='p.png' height=15 width=15 title='$s[2]'></img> <i onclick='document.getElementById(\"e$namec\").remove();'>HIDE</i><br>");
			$rc=$d->executeQuery("SELECT * FROM i0 WHERE adv='$s[0]' AND ( major='$ss[3]' OR major='' ) AND ( groupNow='0' OR groupNow!=groupMax ) ORDER BY start",'main.G');
			while($s=mysql_fetch_row($rc)){
				$named=$namec-1;
				print("<font onclick='document.getElementById(\"t\").value=\"$s[0]\";document.getElementById(\"i\").value=$named;window.scrollTo(0,document.body.scrollHeight);'>$s[0]</font> ~ <font onclick='document.getElementById(\"t\").value=\"$s[1]\";document.getElementById(\"i\").value=$named;window.scrollTo(0,document.body.scrollHeight);'>$s[1]</font> : ");
				if($s[4]==0){
					print("<font color='blue'>Individual<br></font>");
				}else{
					print("<font color='red'>$s[2]/$s[4]<br></font>");
				}
			}
			print("</div>");
		}
		print("<div align='center'>Welcome, <b>$ss[1]</b>(<b>$ss[2]</b>) in <b>$ss[3]</b>");
		if(strtotime($ss[5])==943938000){
			if($_POST['indiv']){
				if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE adv='$i' AND start<='$t' AND end>='$t' AND groupMax=0 AND ( major='' OR major='$ss[3]' )",'main.H'))){
					$ute=$ut+1800;
					$te=date('Y-m-d H:i:s',$ute);
					$rb=$d->executeQuery("SELECT * FROM i0 WHERE adv='$i' AND start<='$t' AND end>'$t' AND ( major='' OR major='$ss[3]' )",'main.I');
					while($s=mysql_fetch_row($rb)){
						$ub=strtotime($s[0]);
						$ue=strtotime($s[1]);
						if(($ub>=$ut)&&($ue<=$ute)){
							$rt=$d->executeQuery("DELETE FROM i0 WHERE adv='$i' AND start='$s[0]'",'main.J');
						}else if(($ub<=$ut)&&($ue>=$ute)){
							$rt=$d->executeQuery("UPDATE i0 SET end='$t' WHERE adv='$i' AND start='$s[0]'",'main.K');
							$rt=$d->executeQuery("INSERT INTO i0(start,end,major,adv) VALUES ('$te','$s[1]','$ss[3]','$i')",'main.L');
						}else{
							$rt=$d->executeQuery("UPDATE i0 SET end='$t' WHERE adv='$i' AND start='$s[0]'",'main.M');
						}
					}
					$rb=$d->executeQuery("SELECT * FROM i0 WHERE adv='$i' AND start<'$te' AND end>='$te' AND ( major='' OR major='$ss[3]' )",'main.N');
					while($s=mysql_fetch_row($rb)){
						$ub=strtotime($s[0]);
						$ue=strtotime($s[1]);
						if(($ub<=$ut)&&($ue>=$ute)){
							$rt=$d->executeQuery("UPDATE i0 SET end='$t' WHERE adv='$i' AND start='$s[0]'",'main.O');
							$rt=$d->executeQuery("INSERT INTO i(b,e,m,adv) VALUES ('$te','$s[1]','$ss[3]','$i')",'main.P');
						}else{
							$rt=$d->executeQuery("UPDATE i0 SET start='$te' WHERE adv='$i' AND start='$s[0]'",'main.Q');
						}
					}
					$rt=$d->executeQuery("UPDATE appts SET adv='$i',t='$t',isGroup='0' WHERE id='$_GET[ID]'",'main.R');
					$rt=$name[$i];
					die("<h3>Advising: $t with $rt</h3><br><form action='main.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='cancel' value='Cancel Advising'><input type='submit' name='return' value='Return To Login'></form>");
				}else{
					print("<h3>Time Do Not Exist</h3><br>");
				}
			}else{
				if($_POST['jgroup'])if($s=mysql_fetch_row($d->executeQuery("SELECT * FROM i0 WHERE adv='$i' AND start='$t' AND groupMax>'0' AND groupNow<groupMax AND ( major='' OR major='$ss[3]' )",'main.S'))){
					$n=$s[2]+1;
					$rt=$d->executeQuery("UPDATE i0 SET groupNow=$n WHERE adv='$i' AND start='$s[0]'",'main.T');
					$rt=$d->executeQuery("UPDATE appts SET adv=$i,t='$s[0]',isGroup='1' WHERE id='$_GET[ID]'",'main.U');
					$rt=$name[$i];
					die("<h3>Advising: $t with $rt</h3><br><form action='main.php?ID=$_GET[ID]' method='post' name='t'><input type='submit' name='cancel' value='Cancel Advising'><input type='submit' name='return' value='Return To Login'></form>");
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

<html><head><title>Student UAP</title></head><body>

<form action=<?php print("'main.php?ID=$_GET[ID]'"); ?> method='post' name='t'>
	<br>Advisor:<select name='i' id='i'><?php for($i=0;$i<$namec;$i++)print("<option value=$i>$name[$i]</option>") ?></select>
	Time:<input type='text' id=t name='t' value=<?php print("'$_GET[t]'"); ?>><br>
	<input type='submit' name='indiv' value='Make Individual Advising' title='Just enter the START time. Use 13:00:00 instead of 1:00:00 PM. The advising always lasts for 30min.'>
	<input type='submit' name='jgroup' value='Join Group Advising' title='Just enter the START time. Use 13:00:00 instead of 1:00:00 PM. The advising always lasts for 30min.'>
	<input type='submit' name='return' value='Return to Login'><br>
	<input type='submit' name='helperI' value='All Time Selector'>
	<input type='submit' name='helperG' value='Group Time Selector'><br><br>
</form>
</div>

</body></html>
