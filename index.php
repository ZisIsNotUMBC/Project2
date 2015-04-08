<?php
	include('CommonMethods.php');
	$d=new Common(false);
	$ID=strtoupper($_POST['ID']);
	$Major=strtoupper($_POST['Major']);
	if($_GET['MSG'][0]=='0'){
		print("<h3>ERROR: Invalid Direct Login</h3><br>");
	}else if($_POST['signup']){
		$flag=0;
		if(strlen($_POST['Name'])==0){
			$flag=1;
			print("<h3>ERROR: Name Not Given</h3><br>");
		}
		if(strlen($_POST['ID'])==0){
			$flag=1;
			print("<h3>ERROR: ID Not Given</h3><br>");
		}
		if(strlen($_POST['Major'])==0){
			$flag=1;
			print("<h3>ERROR: Major Not Given</h3><br>");
		}
		if($flag==0){
			if($_POST['i']==1){
				$i="TRUE";
				$inst=mysql_fetch_row($d->executeQuery("SELECT COUNT(i) FROM i",'index.A'));
				$inst=$inst[0];
			}else{
				$i="FALSE";
				$inst=0;
			}
			$s=$d->executeQuery("select * from appts where sid='$ID'",'index.B');
			if(mysql_fetch_row($s)){
				print("<h3>ERROR: ID Already Exist</h3><br>");
			}else{
				$s=$d->executeQuery("INSERT INTO appts(`std`,`sid`,`smajor`,`inst`,`t`,`i`) values ('$_POST[Name]','$ID','$Major','$inst',0,$i)",'index.D');
				if($_POST['i']==1){
					$rs=$d->executeQuery("INSERT INTO i(n,s,i) VALUES ('$_POST[Name]','$_POST[info]',$inst)",'index.C');
					$rs=$d->executeQuery("CREATE TABLE i$inst(b timestamp DEFAULT '0000-00-00 00:00:00',e timestamp DEFAULT '0000-00-00 00:00:00',g tinyint UNSIGNED DEFAULT 0,m char(4),mg tinyint DEFAULT 0,i int AUTO_INCREMENT,PRIMARY KEY(i) )",'index.D');
					header("Location: adv.php?ID=$ID");
				}
				header("Location: main.php?ID=$ID");
			}
		}
	}else if($_POST['login']){
		if(strlen($ID)==0){
			print("<h3>ERROR: ID Not Given</h3><br>");
		}else{
			$s="select * from appts where sid='$ID'";
			$s=$d->executeQuery($s,$_SERVER["SCRIPT_NAME"]);
			if(mysql_fetch_row($s))header("Location: main.php?ID=$ID");
			print("<h3>ERROR: ID Do Not Exist</h3><br>");
		}
	}
?>

<html>
<head>
	<title>Undergraduate Advising Project Login</title>
</head>
<body>

<script type="text/javascript">
	function f(){if(ss.selectedIndex==1)info.disabled=true;else info.disabled=false;}
</script>

<div align='center'>
	<h2>Undergraduate Advising Project Login</h2><br>
	<form action='index.php' method='post' name='t'>
		ID:<input type='text' name='ID'><br>
		Name:<input type='text' name='Name'><br>
		Major:<input type='text' name='Major'><br>
		<select id="ss" name="i" onchange="f();">
			<option value=1>Advisor</option>
			<option value=0>Student</option>
		</select>
		<input type='submit' name='signup' value='Signup'>
		<input type='submit' name='login' value='Login'><br>
		<textarea id="info" name='info' cols=30 rows=4>
If you are an advisor, input the info you would like your students to see to register
		</textarea><br>	
	</form>	
	<br>	<!--how to use-->
	You need first four for Signup<br>
	You need all five for Advisor Signup
	You only need ID for Login<br>
</div>

<script type="text/javascript">
	var ss=document.getElementById("ss");
	var info=document.getElementById("info");
</script>

</body>
</html>
