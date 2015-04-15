<html><head><title>Individual Time Clicker</title></head><body>

<canvas id='myCanvas' width='4000' height='601'></canvas>
<script>
	function xyconv(x,y){
		var nx=Math.trunc(x/100)-2;
		if((nx>xc)||(nx<0))return '';
		var i;
		for(i=0;i<yc[nx];i++)if((b[nx][i]<=y)&&(e[nx][i]>=y)){
			if(g[nx][i]==true){
				var ta=Math.trunc(b[nx][i]/100+8.5);
				var tb=Math.trunc((((b[nx][i]-50)*36)%3600)/60);
				return xd[nx]+' '+ta+':'+tb;
			}else{
				var ta=Math.trunc(y/100+8.5);
				var tb=Math.trunc((((y-50)*36)%3600)/60);
				return xd[nx]+' '+ta+':'+tb;
			}
		}
		return '';
	}
	var c=document.getElementById('myCanvas');
	var ct=c.getContext('2d');
	c.addEventListener('mousemove',function(e){
		var r=c.getBoundingClientRect();
		ct.clearRect(0,0,199,49);
		ct.font='18pt Calibri';
		ct.fillStyle='black';
		ct.fillText(xyconv(e.clientX-r.left,e.clientY-r.top),0,25);
	},false);
	c.addEventListener('click',function(e){
		var r=c.getBoundingClientRect();
		var s=xyconv(e.clientX-r.left,e.clientY-r.top);
		if((s.length!=0)||(confirm('Invalid Time Clicked, Are You Going Back?')))window.location.href="main.php?ID="+ID+"&t="+year+"-"+s+":00";
	},false);
	<?php
		include('CommonMethods.php');
		$d=new Common(false);
		$ra=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'helperI.A');
		if($ss=mysql_fetch_row($ra)){
			if($ss[6]==1)header("Location:adv.php?ID=$_GET[ID]");
			if($_GET['ins'])$ins=intval($_GET['ins']);else $ins=0;
			for($i=0;$i<7;$i++){
				$c=$i+9;
				$y=$i*100+75;
				print("ct.font='20px Arial';ct.fillStyle='black';ct.fillText('$c:00',0,$y);");
				$y=$y-25;
				print("ct.moveTo(0,$y);ct.lineTo(4000,$y);ct.stroke();");
			}
			$ld='';
			$i=1;
			$j=0;
			$jyc=array();
			$jg=array();
			$jb=array();
			$je=array();
			$jxd=array();
			$ra=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ins' AND major='$ss[3]' ORDER BY start",'helperG.A');
			while($s=mysql_fetch_row($ra)){
				$ub=strtotime($s[0]);
				$ue=strtotime($s[1]);
				$d=date('m-d',$ub);
				if(strcmp($d,$ld)!=0){
					if($i>1)$jyc[$i-2]=$j;
					$i++;
					$j=0;
					$jxd[$i-2]=$d;
					$jb[$i-2]=array();
					$je[$i-2]=array();
					$jg[$i-2]=array();
					$x=100*$i;
					print("ct.moveTo($x,0);ct.lineTo($x,600);ct.stroke();ct.font='30px Arial';ct.fillStyle='black';ct.fillText('$d',$x,25);");
				}
				$y=intval(date('H',$ub))*100+intval(date('i',$ub))*1.67;
				$l=intval(date('H',$ue))*100+intval(date('i',$ue))*1.67-$y;
				$y=$y-850;
				$jb[$i-2][$j]=$y;
				$je[$i-2][$j]=$l+$y;
				$jg[$i-2][$j]=false;
				print("ct.beginPath();ct.rect($x,$y,100,$l);");
				if($s[4]==0)print("ct.fillStyle='blue';ct.fill();ct.stroke();");else{
					$jg[$i-2][$j]=true;
					print("ct.fillStyle='red';ct.fill();ct.stroke();");
					$y=$y+37;
					print("ct.font='30px Arial';ct.fillStyle='white';ct.fillText('$s[2]/$s[4]',$x,$y);");
				}
				$ld=$d;
				$j++;
			}
			if($i>1)$jyc[$i-2]=$j;
			$jxc=$i-1;
			$jyear=date('Y',$ub);
			print("var ID='$_GET[ID]';var xc=$jxc;var year=$jyear;var yc=[");
			for($i=0;$i<$jxc;$i++)print("$jyc[$i],");
			print("0];var xd=[");
			for($i=0;$i<$jxc;$i++)print("'$jxd[$i]',");
			print("0];var b=[");
			for($i=0;$i<$jxc;$i++){
				print("[");
				for($j=0;$j<$jyc[$i];$j++){
					$t=$jb[$i][$j];
					print("$t,");
				}
				print("0],");
			}
			print("0];var e=[");
			for($i=0;$i<$jxc;$i++){
				print("[");
				for($j=0;$j<$jyc[$i];$j++){
					$t=$je[$i][$j];
					print("$t,");
				}
				print("0],");
			}
			print("0];var g=[");
			for($i=0;$i<$jxc;$i++){
				print("[");
				for($j=0;$j<$jyc[$i];$j++)if($jg[$i][$j]==true)print("true,");else print("false,");
				print("0],");
			}
			print("0];");
		}else header('Location:index.php?MSG=0');
	?>
</script>

<br><font color='blue'>Blue</font> are for <b>New Individual Advising</b><br>
<font color='red'>Red</font> are for <b>'Join Group Advising'</b>. CAPACITY is written above.<br><br>

Just click the time you want the advising to <b>START</b>, and it will auto-input the "Time Box" in the privious page for you.

</body></html>