<?php
	include('CommonMethods.php');
	$d=new Common(false);
	$ID=strtoupper($_POST['ID']);
	$Major=strtoupper($_POST['Major']);
	if($_GET['MSG'][0]=='0')print("<h3>ERROR: Invalid Direct Login</h3><br>");
	else if($_POST['signup']){
		$flag=0;
		if(strlen($_POST['Name'])==0){
			$flag=1;
			print("<h3>ERROR: Name Not Given</h3><br>");
		}
		if(strlen($_POST['ID'])!=7){
			$flag=1;
			print("<h3>ERROR: ID Not 7 Char</h3><br>");
		}
		if((strlen($_POST['Major'])<3)||(strlen($_POST['Major'])>4)){
			$flag=1;
			print("<h3>ERROR: Major Not 3 or 4 Char</h3><br>");
		}
		if($flag==0){
			if($_POST['i']==1){
				$i=1;
				$inst=mysql_fetch_row($d->executeQuery("SELECT COUNT(i) FROM i",'index.A'));
				$inst=$inst[0];
			}else{
				$i=0;
				$inst=0;
			}
			$s=$d->executeQuery("SELECT * FROM appts WHERE id='$ID'",'index.B');
			if(mysql_fetch_row($s)){
				print("<h3>ERROR: ID Already Exist</h3><br>");
			}else{
				$s=$d->executeQuery("INSERT INTO appts(name,id,major,adv,t,isAdv) values ('$_POST[Name]','$ID','$Major','$inst','0','$i')",'index.C');
				if($_POST['i']==1){
					$rs=$d->executeQuery("INSERT INTO i(name,info,i) VALUES ('$_POST[Name]','$_POST[info]',$inst)",'index.D');
					header("Location:adv.php?ID=$ID");
				}
				header("Location:main.php?ID=$ID");
			}
		}
	}else if($_POST['login']){
		if(strlen($ID)!=7)print("<h3>ERROR: ID Not 7 Char</h3><br>");
		else{
			if(mysql_fetch_row($d->executeQuery("SELECT * FROM appts where id='$ID'",'index.E')))header("Location: main.php?ID=$ID");
			print("<h3>ERROR: ID Do Not Exist</h3><br>");
		}
	}
?>

<html><head><title>Login UAP</title></head><body>

<script>
	function f(){if(ss.selectedIndex==1)info.disabled=true;else info.disabled=false;}
</script>

<div align='center'>
	<h2>Undergraduate Advising Project Login</h2><br>
	<form action='index.php' method='post' name='t'>
		<table>
			<tr bgcolor="#22F53E"><td colspan='2'>ID:<input type='text' name='ID' size='7' title='E.G. AB12345, case insensitive'></td></tr>
			<tr><td bgcolor="#22F53E"><input type='submit' name='login' value='Log In' title='Just enter ID to Log In, You might directly hit enter to login.'></td>
			<td bgcolor="#43FAE2"><input type='submit' name='signup' value='Sign Up' title='Enter EVERYTHING to Sign Up'></td></tr>
		</table>
		<table bgcolor="#43FAE2"><tr><td>
				<select id='ss' name='i' onchange='f();'>
					<option value=1>Advisor</option>
					<option value=0>Student</option>
				</select>
				Name:<input type='text' name='Name' size='9'>
				Major:<input type='text' name='Major' size='4' title='E.G. CMSC (4 char at most, case insensitive)'><br>
				<textarea id="info" name='info' cols=44 rows=4>If you are an advisor, input the info you would like your students to see to register</textarea>
		</td></tr></table>
	</form>
</div>

<script>
	var ss=document.getElementById('ss');;
	var info=document.getElementById('info');
</script>

</body>
</html>
