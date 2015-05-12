<?php
include('CommonMethods.php');
$d=new Common(false);
$ID=strtoupper($_POST['ID']);
$Major=strtoupper($_POST['Major']);
if($_GET['MSG'][0]=='0')print("<h3>Error: invalid login</h3><br>");
else if($_POST['signup']){
	$flag=0;
	if(strlen($_POST['Name'])==0){
		$flag=1;
		print("<h3>Error: name is required</h3><br>");
	}
	if(strlen($_POST['ID'])!=7){
		$flag=1;
		print("<h3>Error: IDs must be seven characters.</h3><br>");
	}
	if((strlen($_POST['Major'])<3)||(strlen($_POST['Major'])>4)){
		$flag=1;
		print("<h3>Error: major must be an official major abbreviation of three or four characters.</h3><br>");
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
			print("<h3>Error: this ID is already registered</h3><br>");
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
	if(strlen($ID)!=7)print("<h3>Error: IDs must be seven characters.</h3><br>");
	else{
		if(mysql_fetch_row($d->executeQuery("SELECT * FROM appts where id='$ID'",'index.E')))header("Location: main.php?ID=$ID");
		print("<h3>Error: this ID is not registered</h3><br>");
	}
}
?>

<html><head><title>Login UAP</title></head><body>

<script>
function f(){if(ss.selectedIndex==1)info.disabled=true;else info.disabled=false;}
</script>

<div align='center'>
	<b><font size='5'>Undergraduate Advising Login</font></b><br>
	<table id='ta'>
		<form action='index.php' method='post' name='t'>
			<tr bgcolor="#22F53E"><td colspan='2'>ID:<input type='text' name='ID' size='7' title='E.G. AB12345, case insensitive'></td></tr>
			<tr><td bgcolor="#22F53E"><input type='submit' name='login' value='Log In' title='Just enter ID to Log In, You might directly hit enter to login.'></td>
			</form>
			<td bgcolor="#43FAE2"><button onclick="document.getElementById('ta').remove();document.getElementById('tb').style.visibility='visible';">Sign Up</button></td></tr>
		</table>
		<form action='index.php' method='post' name='t'>
			<p id='tb'><table bgcolor="#43FAE2"><tr><td>
				ID:<input type='text' name='ID' size='7' title='E.G. AB12345, case insensitive'>
				<select id='ss' name='i' onchange='f();'>
					<option value=1>Advisor</option>
					<option value=0>Student</option>
				</select>
				Name:<input type='text' name='Name' size='9'>
				Major:<input type='text' name='Major' size='4' title='E.G. CMSC (4 char at most, case insensitive)'> <input type='submit' name='signup' value='Sign Up' title='Enter EVERYTHING to Sign Up'><br>
				<textarea id="info" name='info' cols=66 rows=4>If you are an advisor, input the info you would like your students to see to register</textarea>
			</table></p>
		</form>
	</div>

	<script>
	document.getElementById('tb').style.visibility='collapse';
	var ss=document.getElementById('ss');;
	var info=document.getElementById('info');
	</script>

	<marquee behavior='scroll' direction='left'>
		<img src='a.png'><img src='b.png'><img src='c.png'><img src='d.png'><img src='e.png'><img src='f.png'><img src='g.png'>
	</marquee>

</body>
</html>
