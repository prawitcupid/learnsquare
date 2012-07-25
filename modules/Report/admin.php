<?

include_once 'includes/lnSession.php';
include_once 'includes/lnAPI.php';

//global $config;
//$config = array();
//include "../../config.php";

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}
/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,Report);
$links=array('index.php?mod=Admin','index.php?mod=Report&amp;file=admin');
lnBlockNav($menus,$links);


function studentname(){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	$query1 = "SELECT  *
	FROM $group_membershiptable 
		WHERE  $group_membershipcolumn[gid] = 4 ";

	$result1 = mysql_query($query1);

	$i =0;
	while($row = mysql_fetch_array($result1))
	{
		$a = mysql_num_rows($result1);

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
		$name[$b] = namelearn($uid[$b]);
		$now = $b % 4;
		if($now == 0){
			if (lnSecAuthAction(0, 'Report::', "show::", ACCESS_READ)) {
				echo '</tr>';
				echo '<tr><td width=250 align=center ><A  HREF="index.php?mod=Report&file=show&amp;uid='.$uid[$b].'" >'.$name[$b].'</A></td>';
			}else{} //?uid=$uid[$b]
			//HREF="index.php?mod=Courses&amp;op=course_detail&amp;cid='.$cid.'">'.$code.' : '.$cname.'</A>';
		}else{
			if (lnSecAuthAction(0, 'Report::', "show::", ACCESS_READ)) {
				echo '<td width=250 align=center ><A  HREF="index.php?mod=Report&file=show&amp;uid='.$uid[$b].'" >'.$name[$b].'</A></td>';
			}else{}
		}
		$b++;
	}


}
$bgcolor1 = "#ffffff";
$bgcolor2 = "#000000";

echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor1\" height=\"350\"><tr><td valign=\"top\">\n";

echo '<br><IMG SRC="images/global/bl_red.gif"><B>เลือกบุคคลที่ต้องการแสดงรายงาน</B><br><br>';


/*	echo '<center><FORM METHOD=POST ACTION="index.php">'
 .'<INPUT TYPE="hidden" NAME="mod" VALUE="Report">'
 .'<INPUT TYPE="hidden" NAME="file" VALUE="usershow">'
 .'<INPUT TYPE="hidden" NAME="op" VALUE="finduser"><center>'

 ._SEARCH.': <INPUT  class="input" TYPE="text" NAME="word" SIZE="20">&nbsp;'
 .'<SELECT class="select" NAME="field">'
 .'<OPTION VALUE="uname">'._NICKNAME.'</OPTION>'
 .'<OPTION VALUE="name">'._NAME.'</OPTION>'
 .'<OPTION VALUE="uno">'._UNO.'</OPTION>'
 .'<OPTION VALUE="email">'._EMAIL.'</OPTION>'
 .'</SELECT>'
 .' <INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'">'
 .'</FORM></center><br><br>';
 */

// Creates the list of letters and makes them a link.
$alphabet = array (_ALL, "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$num = count($alphabet) - 1;
$counter = 0;
while(list(, $ltr) = each($alphabet)) {
	$class = (@$letter == $ltr) ? "class=line": "";
	$menu[] = "<a $class  href=\"index.php?mod=Report&amp;file=useredit&amp;letter=".$ltr."\">".$ltr."</a>";
	//	"index.php?mod=StudentReport&amp;file=show&amp;op=show&amp;uid=$uid\"
	$counter++;
}
//$menus = "<center>[ ".join('&nbsp;&nbsp;|&nbsp;&nbsp;',$menu)." ]</center><BR>";
$menus = "<center>[ ".join('&nbsp;|&nbsp;',$menu)." ]</center><BR>";
echo $menus;

$pagesize = lnConfigGetVar('pagesize');
if(empty($sorting)){
	$sorting="uname";
}

if (!isset($letter)) {
	$letter = "A";
}

if (!isset($page)) {
	$page = 1;
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userstable = $lntable['users'];
$userscolumn = &$lntable['users_column'];
$datatable = $lntable['user_data'];
$datacolumn = &$lntable['user_data_column'];
$propertiestable = $lntable['user_property'];
$propcolumn = &$lntable['user_property_column'];
$groupstable = $lntable['groups'];
$groupscolumn = &$lntable['groups_column'];
$group_membershiptable = $lntable['group_membership'];
$group_membershipcolumn = &$lntable['group_membership_column'];
$min = $pagesize * ($page - 1); // This is where we start our record set from
$max = $pagesize; // This is how many rows to select
$count = "SELECT COUNT($userscolumn[uid]) FROM $userstable ";

$resultcount = $dbconn->Execute($count);
list ($totalusers) = $resultcount->fields;
$resultcount->Close();

//Security Fix - Cleaning the search input
$sorting   = lnVarCleanFromInput('sorting');
if (!empty($sorting)){
	$sort = "$sorting ASC";
}

if (($letter != _ALL)) {
	$where .= " UPPER($userscolumn[uname]) LIKE UPPER('".lnVarPrepForStore($letter)."%')";
}


//$result = $dbconn->Execute($query);
$myquery = buildSimpleQuery('users', array('uid', 'name', 'uname', 'email', 'regdate', 'phone','uno','news','active'), $where, lnVarPrepForStore($sort), $max, $min);

$result = $dbconn->Execute($myquery);
if ($result === false) {
	error_log("Error: " . $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg());
}

echo '<table width=100% cellpadding=3 cellspacing=1 bgcolor="#999999">'
.'<tr bgcolor="#D0D0D0" align=center><td><B>No.</B></td><td>'
.'<A class="white"><B>'._NICKNAME.'</B></A></td><td>'
.'<A class="white"><B>'._UNO.'</B></A></td><td>'
.'<A class="white"><B>'._NAME.'</B></A></td><td>'
.'<A class="white"><B>'._STATE.'</B></A></td><td>'
.'<A class="white"><B>'._REGDATE.'</B></A></td></tr>';
for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
	$result->MoveNext();
	if ($active)
	$activecolor="#000000";
	else
	$activecolor="#999999";
	$n=$min + $i;
	$link = "<A HREF=\"index.php?mod=Report&amp;file=show&amp;op=show&amp;uid=$uid\">";
	$group = check($uid);
	echo '<TR  bgcolor=#FFFFFF align=center>'
	.'<TD width="5%"><FONT COLOR="'.$activecolor.'">'.$link . $n .'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $uname .'</FONT></A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $uno .'</A></TD>'
	.'<TD width="35%"><FONT COLOR="'.$activecolor.'">'.$link . $name.'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $group.'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . date('d-M-Y',$regdate).'</A></TD>';
	echo '</TR>';
}
echo '</table>';


echo "</table></td></tr></table>\n";

include 'footer.php';
function check($uid){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	$query2 = "SELECT *
	FROM $group_membershiptable 
		WHERE  $group_membershipcolumn[uid] = $uid ";

	$result2 = mysql_query($query2);

	while($rets = mysql_fetch_row($result2)) {
		$gid= $rets[0];
	}
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];

	$query3 = "SELECT *
	FROM $groupstable 
		WHERE  $groupscolumn[gid] = $gid ";

	$result3 = mysql_query($query3);

	while($rets = mysql_fetch_row($result3)) {
		$group= $rets[1];
	}

	return $group;

}
function namelearn($uid){
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

	$query2 = "SELECT *
	FROM $userstable 
		WHERE  $userscolumn[uid] = $uid ORDER BY $userscolumn[name]";

	$result2 = mysql_query($query2);

	while($rets = mysql_fetch_row($result2)) {
		$uname= $rets[1];
	}
	return $uname;
}


?>
<head>
<style>
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
</script>
</head>
<!-- <form onMouseover="change('yellow')" onMouseout="change('lime')">
<center><input type="button" value="BACK" class="initial2" onClick="goHist(-1)"><center>
</form> -->
<FORM
	METHOD="post">
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
