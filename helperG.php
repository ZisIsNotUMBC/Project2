<html><head><title>Individual Time Clicker</title></head><body>

<canvas id='myCanvas' width='4000' height='251'></canvas>
<script>
	function xyconv(x,y){
		var nx=Math.trunc(x/100)-2;
		if((nx>xc)||(nx<0))return '';
		var i;
		for(i=0;i<yc[nx];i++)if((b[nx][i]<=y)&&(e[nx][i]>=y)){
			if(b[nx][i]==50)var tb='00';else var tb='30';
			return xd[nx]+' '+'12:'+tb;
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
		if((s.length!=0)||(confirm('Return to list view?')))window.location.href="main.php?ID="+ID+"&t="+year+"-"+s+":00&adv="+adv;
	},false);
	<?php
		include('CommonMethods.php');
		$d=new Common(false);
		$ra=$d->executeQuery("SELECT * FROM appts WHERE id='$_GET[ID]'",'helperG.A');
		if($ss=mysql_fetch_row($ra)){
			if($ss[6]==1)header("Location:adv.php?ID=$_GET[ID]");
			if($_GET['ins'])$ins=intval($_GET['ins']);else $ins=0;
			for($i=0;$i<2;$i++){
				$c=$i*30;
				$y=$i*100+75;
				print("ct.font='20px Arial';ct.fillStyle='black';ct.fillText('12:$c',0,$y);");
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
			$namee=mysql_fetch_row($d->executeQuery("SELECT * FROM i WHERE i='$ins'",'T'));
			$namee=$namee[1];
			$ra=$d->executeQuery("SELECT * FROM i0 WHERE adv='$ins' AND ( major='$ss[3]' OR major='' ) AND groupMax>0 ORDER BY start",'helperG.B');
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
					print("ct.moveTo($x,0);ct.lineTo($x,250);ct.stroke();ct.font='30px Arial';ct.fillStyle='black';ct.fillText('$d',$x,25);");
				}
				if(intval(date('i',$ub))==0)$y=50;else $y=150;
				$l=100;
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
			print("var ID='$_GET[ID]';var adv=$ins;var xc=$jxc;var year=$jyear;var yc=[");
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

<?php print("<div align='center'>Welcome, <b>$ss[1]</b>(<b>$ss[2]</b>) in <b>$ss[3]</b>. Advisor:<b>$namee</b></div>"); ?>
<br><font color='red'>Red</font> denotes a <b>group</b> appointment.<br>
You can save this time table as an image using your browser's right-click menu.<br>


</body></html>
