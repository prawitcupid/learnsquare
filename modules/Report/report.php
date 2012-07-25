<?
  include 'includes/lnSession.php';
  include 'includes/lnAPI.php';

 if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

	global $config;
    $config = array();
    include "config.php";
	
function namelearn($uid){
 global $config;

    // Connect to database
   if ($config['encoded']) {

        $config['dbuname'] = base64_decode($config['dbuname']);
        $config['dbpass'] = base64_decode($config['dbpass']);
        $config['encoded'] = 0;

    }

	$dbtype = $config['dbtype'];
    $dbhost = $config['dbhost'];
    $dbname = $config['dbname'];
    $dbuname = $config['dbuname'];
    $dbpass = $config['dbpass'];
	$prefix = $config['prefix'];
    global $dbconn;

  @$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
			      mysql_select_db($dbname);
					if(!$db){
										echo "เกิดข้อผิดพลาด";
										exit;
									}
	$userscolumnname= $prefix.'_'."name";
	$userscolumnuid= $prefix.'_'."uid";
	$userstable = $prefix.'_'."users";
    $query2 = "SELECT * 
	FROM $userstable 
		WHERE  $userscolumnuid = $uid ORDER BY $userscolumnname";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $uname= $rets[1];
	}	
				 return $uname;
		  }

function studentname(){
	 global $config;

    // Connect to database
   if ($config['encoded']) {

        $config['dbuname'] = base64_decode($config['dbuname']);
        $config['dbpass'] = base64_decode($config['dbpass']);
        $config['encoded'] = 0;

    }

	$dbtype = $config['dbtype'];
    $dbhost = $config['dbhost'];
    $dbname = $config['dbname'];
    $dbuname = $config['dbuname'];
    $dbpass = $config['dbpass'];
	$prefix = $config['prefix'];
    global $dbconn;

  @$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
			      mysql_select_db($dbname);
					if(!$db){
										echo "เกิดข้อผิดพลาด";
										exit;
									}

	$group_membershipgid= $prefix.'_'."gid";
	$group_membershipuid= $prefix.'_'."uid";
///	$group_membershipname= $prefix.'_'."name";
	$group_membershiptable = $prefix.'_'."group_membership";

    $query1 = "SELECT  * FROM $group_membershiptable WHERE  $group_membershipgid = 4 ";

    $result1 = mysql_query($query1);

	 $i =0;
	 while($row = mysql_fetch_array($result1)) 
				{ 
												$a = mysql_num_rows($result1); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				           $uid[$i]= $row[1];
												}                   
								$i++;
					} 
 $b = 0;
  $d = 0;
 $a = mysql_num_rows($result1); 
			while($a > $b){
											//echo $uid[$b].'<br>';
											 $name[$b] = namelearn($uid[$b]);
											// echo $name[$b].'<br>';
									$now = $b % 4;
			         if($now == 0){
						 echo '</tr>';
					 	    echo "<tr><td width=250 align=center ><A  HREF='show.php?uid=$uid[$b]' onMouseOver='startsnow()' onMouseOut='stopsnow()'>$name[$b]</A></td>";
					 }else{
					 	   echo " <td width=250 align=center ><A  HREF='show.php?uid=$uid[$b]' onMouseOver='startsnow()' onMouseOut='stopsnow()'>$name[$b]</A></td>";
					 }


						$b++;
						}

        
}
echo ' <body background="photo.gif" text = "#1C1D62" link = "#E80202" vlink = "#87221D" alink = "#FD8627" bgcolor = "#FFFFFF"> ';
	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
echo '</TD></TR><TR><TD>';
	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';
echo '<center><B>&nbsp;&nbsp;&nbsp;&nbsp;<U>ตารางรายชื่อนักเรียน</U></B><U><BR><br>';
echo '<center><table cellpadding="1" cellspacing="1" border="1"  align=center name = Learn>';
echo "<tr bgcolor=#CCFFFF align=center  width=100  >";
   
		echo "<tr bgcolor=#FFCC99 align=center  width=100 >";
		echo "	<td width=250 align=center >Name:</td>";
		echo "	<td width=250 align=center>Name:</td>";
		echo "	<td  width=250  align = center >Name:</td>";
		echo "	<td width=250  align = center >Name:</td></tr>";

          studentname();
echo '</table>';
	   
	echo '</table>';
	
	echo '</TD></TR>';

echo '</TABLE>';

	
	?>
	<head><style>

</style>

<script>
<!--

function change(color){
var el=event.srcElement
if (el.tagName=="INPUT"&&el.type=="button")
event.srcElement.style.backgroundColor=color
}

function jumpto2(url){
window.location=url
}

//-->

<!-- <INPUT TYPE="button" VALUE="  BACK " onClick="goHist(-1)"> -->
</form>
<SCRIPT LANGUAGE="JavaScript">
<!-- hide this script tag s contents from old browsers-->
function goHist(a) 
{
   history.go(a);      // Go back one.
}
//<!-- done hiding from old browsers -->
</script>
<script language="javascript">
if (window != top) top.location.href = location.href;
self.moveTo(0,0);self.moveTo(-1,-1);self.resizeTo(screen.availWidth+1,screen.availHeight+1);
</script>
<script>

// จำนวนหิมะที่ต้องการมี (ประมาณ 20 - 40 กำลังดี)
var snowmax=20

// สีของหิมะที่ต้องการ
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD")

// ใส่รูปแบบของหิมะ (โดยการใส่ชื่อ Font ต่างๆลงไป)
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS")

// หน้าตาของหิมะ (ไม่ต้องแก้)
var snowletter="*"

// ความเร็วในการตกของหิมะ (ควรจะอยู่ระหว่าง 0.3 ถึง 2)
var sinkspeed=1.5

// ขนาดของเม็ดหิมะที่ใหญ่ที่สุด
var snowmaxsize=23

// ขนาดของเม็ดหิมะที่เล็กที่สุด
var snowminsize=8

// ขอบเขตในการตกของหิมะ (ยิ่งใส่ค่ามาก ยิ่งตกทั่วหน้าจอ แต่ไม่ควรใส่เกิน 600)
var snowboxwidth=100

///////////////////////////////////////////////////////////////////////////
// Do not edit below this line
///////////////////////////////////////////////////////////////////////////

var snow=new Array()
var marginbottom
var marginright
var marginleft
var margintop
var snowingzone=1
var posleft
var postop
var is_snowing=false
var timer
var i_snow=0
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent 
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/)
var ns6=document.getElementById&&!document.all
var opera=browserinfos.match(/Opera/)  
var browserok=ie5||ns6||opera

function randommaker(range) {		
	rand=Math.floor(range*Math.random())
    return rand
}

function startsnow() {
	is_snowing=true
	if (ie5 || opera) {
		margintop = postop+15
		marginbottom = document.body.clientHeight
		marginleft = posleft
		marginright = posleft+snowboxwidth
	}
	else if (ns6) {
		margintop = postop+15
		marginbottom = window.innerHeight
		marginleft = posleft
		marginright = posleft+snowboxwidth
	}
	var snowsizerange=snowmaxsize-snowminsize
	for (i=0;i<=snowmax;i++) {
		crds[i] = 0;                      
    	lftrght[i] = Math.random()*15;         
    	x_mv[i] = 0.03 + Math.random()/10;
		snow[i]=document.getElementById("s"+i)
		snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)]
		snow[i].size=randommaker(snowsizerange)+snowminsize
		snow[i].style.fontSize=snow[i].size
		snow[i].style.color=snowcolor[randommaker(snowcolor.length)]
		snow[i].sink=sinkspeed*snow[i].size/5
		snow[i].posx=randommaker(snowboxwidth)+marginleft-2*snow[i].size
		if (ie5 || opera) {
			if (snow[i].posx>=document.body.clientWidth-2*snow[i].size) { 
				snow[i].posx=snow[i].posx-snowboxwidth
			}
		}
		if (ns6) {
			if (snow[i].posx>=window.innerWidth-2*snow[i].size) { 
				snow[i].posx=snow[i].posx-snowboxwidth
			}
		}
		snow[i].posy=randommaker(marginbottom-margintop)+margintop-2*snow[i].size
		snow[i].style.left=snow[i].posx
		snow[i].style.top=snow[i].posy
		snow[i].style.visibility="visible";
		
	}
	movesnow()
}

function stopsnow() {
	is_snowing=false
}

function movesnow() {
	if (is_snowing) {
		for (i=0;i<=snowmax;i++) {
			crds[i] += x_mv[i];
			snow[i].posy+=snow[i].sink
			snow[i].style.left=snow[i].posx+lftrght[i]*Math.sin(crds[i]);
			snow[i].style.top=snow[i].posy
		
			if (snow[i].posy>=marginbottom-2*snow[i].size || parseInt(snow[i].style.left)>(marginright-3*lftrght[i])){
				snow[i].posx=randommaker(snowboxwidth)+marginleft-2*snow[i].size
				if (ie5 || opera) {
					if (snow[i].posx>=document.body.clientWidth-2*snow[i].size) { 
						snow[i].posx=snow[i].posx-snowboxwidth
					}
				}
				if (ns6) {
					if (snow[i].posx>=window.innerWidth-2*snow[i].size) { 
						snow[i].posx=snow[i].posx-snowboxwidth
					}		
				}
				snow[i].posy=randommaker(marginbottom-margintop)+margintop-2*snow[i].size
			}
		}
		var timer=setTimeout("movesnow()",50)
	}
	else {
		for (i=0;i<=snowmax;i++) {
			snow[i].style.visibility="hidden";
		}
	}
}

function getcoordinates(e) {
	if (ie5 || opera) {
		posleft=document.body.scrollLeft+window.event.x;
		postop=document.body.scrollTop+window.event.y;
	}
	if (ns6) {
		posleft=e.pageX
		postop=e.pageY
	}
}

for (i=0;i<=snowmax;i++) {
	document.write("<span id='s"+i+"' style='position:absolute;top:-"+snowmaxsize+"'>"+snowletter+"</span>")
}
if (browserok) {
	document.onmousemove=getcoordinates
}
</script>