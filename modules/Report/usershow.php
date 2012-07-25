<?php
/**
* Edit/Find/Delete User

create by : pook

last edit :-----
programmer : orrawin
date : 23-06-49


*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
//_ADMINMENU,Student_Report
if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}

include 'header.php';

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


/* options */
// Find User
if ($op=="finduser") {
		/** Navigator **/
		$menus= array(_ADMINMENU,Report);
		$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=usershow','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
	
	 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'._USERSEARCH.'</B><BR>&nbsp;';
	 

	 list($word,$field) = lnVarCleanFromInput('word','field');
	
	// Search
	$eval_cmd = "\$usersfindcolumn=\$userscolumn[$field];";
	@eval($eval_cmd); 
	$result = $dbconn->Execute("SELECT $userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
							$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active] 
							 FROM $userstable 
 							 WHERE $usersfindcolumn LIKE '".lnVarPrepForStore($word)."%' ");
		echo "<BR>"._SEARCH." &nbsp;'<B>$word</B>'<BR>";
		if ( $result->PO_RecordCount() == 0) {
			 echo '<BR><BR>'._SEARCH. '&nbsp; <B>'. $word. '</B>&nbsp;'._NOTFOUND;
		}
		else {
							echo '<BR><table width="100%" cellpadding=3 cellspacing=1 bgcolor=#d3d3d3>'
			.'<tr align=center bgcolor=#808080><td class="head">No.</td><td class="head"  align=center>'._NICKNAME.'</td><td class="head"  align=center>'._UNO.'</td><td class="head" >'._NAME.'</td><td class="head"  align=center>'.State.'</td>';
			 for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
					 $result->MoveNext();
			$group = check($uid);  
		//			 echo '<tr bgcolor=#FFFFFF><td width=25>'.$i.'</td><td width=100><A  HREF="index.php?mod=StudentReport&file=useredit&op=show&amp;uid='.$uid[$b].'>'.$uname.'</td><td>'.$name.'</td><td width=80>'.$uno.'</td>';
//	index.php?mod=User&amp;file=useredit&amp;op=edituser&amp;uid=$uid
                   echo "<tr bgcolor=#FFFFFF><td width=25>$i</td> <td width=100 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$uname</a></td><td width=80 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$uno</a></td><td><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$name</a></td ><td width=80 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$group</a></td>";
				 echo "</tr>";
			}
			echo "</table>";
		}
	
	CloseTable();

	include 'footer.php';

	return;
}

if ($op == "showopen") {
$uid =$_GET['uid'];
$cid=$_GET['cid'];

$menus= array(_ADMINMENU,Report,Courses,Courses_enroll);
		$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=show&op=show&uid='.$uid.'','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
showopencourse($uid,$cid);
//echo 'pook';
}
if ($op == "showlearn") {
$uid =$_GET['uid'];
$menus= array(_ADMINMENU,Report,Courses,Learning);
		$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=show&op=show&uid='.$uid.'','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
showlearn();
//echo 'pook';
}
if ($op == "showstudent") {
$uid =$_GET['uid'];
$cid =$_GET['cid'];

$menus= array(_ADMINMENU,Report,Courses,Courses_enroll,List_student);
		$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=show&op=show&uid='.$uid.'', 'index.php?mod=Report&file=usershow&op=showopen&uid='.$uid.'&cid='.$cid.'','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
student();
//echo 'pook';
}
/* - - - - - - - - - - - */
/// show edit user form
if ($op == "show") {

$uid =$_GET['uid'];
$menus= array(_ADMINMENU,ReportCourses);
	$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
learn();



//	echo 'pook';
/** Navigator **/

}
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
function datainstulearn($cid,$title,$uname,$uid){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

    $query2 = "SELECT * 
	FROM $course_submissionstable  
		WHERE  $course_submissionscolumn[cid] = $cid ";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
	 $i =0;
while($row = mysql_fetch_array($result2)) 
				{ 
												$active = mysql_num_rows($result2); 
												if($i == $active){
																			exit;
												}else{
																						   $sid[$i] = $row[0];
																						    $start[$i]= $row[2];
																					   $numstudent[$i] = numeid($sid[$i] );
																					   $no = $i+1;
							 	                          if($active ==1){
																					   $numstudent = numeid($sid[$i] );
																					   openta($sid[$i],$start[$i],$cid,$code,$no,$title,$i,$active,$numstudent[$i],$uid);

																				}if($active >1 ){
					                                                               openta($sid[$i],$start[$i],$cid,$code,$no,$title,$i,$active,$numstudent[$i],$uid);
							 
																				}
								
												}                   
								$i++;
						
				}
return $active;
      }

	
	}
function datainstu($uname){
	$uid =$_GET['uid'];


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
    $query2 = "SELECT * 
	FROM $coursestable  
		WHERE  $coursescolumn[author] = $uid ";

    $result2 = mysql_query($query2);

	 $i =0;
	 while($row = mysql_fetch_array($result2)) 
				{ 
												$a = mysql_num_rows($result2); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				           $cid[$i]= $row[0];
																						   $code[$i] = $row[1];
																						   $sid[$i] = $row[2];
																						    $title[$i]= $row[3];
																				//			$active[$i] = $row[10];
																							
												}                   
								$i++;
					} 
			
 $b = 0;
  $d = 0;
 $a = mysql_num_rows($result2); 
			while($a > $b){
							 $no = $b+1;
									      $active[$b] = openlist($cid[$b],$code[$b],$no,$title[$b]);

	if($active[$b] ==""){
		echo "<tr><td width=50 align=center>$no</td>";
 echo "<td width=150 align=center > $code[$b]</td>";
echo "<td width=400> $title[$b]</td>";
	echo "<td width=250 align=center >0</td>";
	}else{	
		echo "<tr><td width=50 align=center>$no</td>";
 echo "<td width=150 align=center ><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showopen&amp;uid=$uid&amp;cid=$cid[$b]&amp;title=$title[$b]&amp;uname=$uname\"> $code[$b]</td>";
echo "<td width=400><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showopen&amp;uid=$uid&amp;cid=$cid[$b]&amp;title=$title[$b]&amp;uname=$uname\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$title[$b]</td>";
	echo "<td width=250 align=center > <A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showopen&amp;uid=$uid&amp;cid=$cid[$b]&amp;title=$title[$b]&amp;uname=$uname\"> $active[$b]</td>";
	}
						$b++;
						}
	
	}
	function pass($sid){
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$sta =2;
    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = '$sid' AND $course_enrollscolumn[status] = '$sta'";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
			$pass = mysql_num_rows($result2); 
			return $pass;
				}
	
	}


		function Learning($sid){
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
$sta =1;
    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = '$sid' AND $course_enrollscolumn[status] = '$sta'";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
				$learning = mysql_num_rows($result2); 
				return $learning;
				}
	
	}
		function drop($sid){
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
$sta =3;
    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = '$sid' AND $course_enrollscolumn[status] = '$sta'";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
	
												$drop = mysql_num_rows($result2); 
													return $drop;
				}
	
	}
	function color($cid){
		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$ccoursescolumn = &$lntable['courses_column'];

    $query2 = "SELECT *
	FROM $coursestable  
		WHERE  $ccoursescolumn[cid] = $cid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $coursecode = $rets[10];
	}	
return $coursecode;
}
function openta($sid,$start,$cid,$code,$no,$title,$i,$active,$numstudent,$uid){
                                 $numstudent = numeid($sid);
								 $color = color($cid);
								 if($color  == 1){
  		                      echo "<tr align=center  bgcolor = '#00FF33'><td>$no</td>";
								 }else{
								 echo "<tr align=center bgcolor = '#FF3300'><td>$no</td>";
								 }
								 if($numstudent == 0){
											echo "<td width=250 align=center >$start</td>";
											echo "<td width=250 align=center >$numstudent</td>";
											echo "<td width=250 align=center >0</td>";
											echo "<td width=250 align=center >0</td>";
											echo "<td width=250 align=center >0</td>";
								 }else{
										$pass = pass($sid);
								 		$drop = drop($sid);
							 			$Learning = Learning($sid);
									    echo "<td><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showstudent&amp;uid=$uid&amp;cid=$cid&amp;sid=$sid&amp;title=$title\">$start</a></td>";
										echo "<td width=250 align=center ><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showstudent&amp;uid=$uid&amp;cid=$cid&amp;sid=$sid&amp;title=$title\"> $numstudent</a></td>";
							  		    echo "<td width=250 align=center >$Learning</td>";
										echo "<td width=250 align=center >$pass</td>";
									    echo "<td width=250 align=center >$drop</td>";
								 }
}

	function openlist($cid,$code,$no,$title){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

    $query2 = "SELECT * 
	FROM $course_submissionstable  
		WHERE  $course_submissionscolumn[cid] = $cid ";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
	 $i =0;
while($row = mysql_fetch_array($result2)) 
				{ 
												$active = mysql_num_rows($result2); 
												if($i == $active){
																			exit;
												}else{
																						   $sid[$i] = $row[0];
																						    $start[$i]= $row[2];
																				if($active ==1){
																					   $numstudent = numeid($sid[$i] );
			//		 openta($sid[$i],$start[$i],$cid,$code,$no,$title,$i,$active);
																				}if($active >1 ){
			//		         openta1($sid[$i],$start[$i],$cid,$code,$no,$title,$i,$active);
							 
																				}
												}                   
								$i++;
						
				}
return $active;
      }

}


	function numeid($sid){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = $sid ";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
	 $i =0;
while($rowdistance = mysql_fetch_array($result2)) 
				{ 
												$numstudent = mysql_num_rows($result2); 
				}
if($numstudent  == ""){
$numstudent = 0;
}
		return $numstudent;
}

	}
function	showopencourse($uid,$cid){

	$cid =$_GET['cid'];
	$title =$_GET['title'];
	$uname =$_GET['uname'];	
echo '<center><B><U>ตารางเรียนของ '.$title.'</U></B><U><BR><br>';
echo '<center><table width=100% cellpadding=1 cellspacing=1 bgcolor="#999999" name=Learn>';
   
		echo "<tr bgcolor=\"#D0D0D0\" align=center>";
		echo "	<td   rowspan='2' width=10% >NO.</td>";
		echo "	<td  rowspan='2'  width=30% >วันที่เปิดสอน</td>";
		echo "	<td   rowspan='2' width=20% >จำนวนนักเรียน</td>";
		echo "	<td  colspan=3 >สถานะ</td></tr>";
		echo "	<td  bgcolor=#D0D0D0 width=10% align=center>Learning</td>";
		echo "	<td  bgcolor=#D0D0D0 width=10%  align = center >Pass</td>";
		echo "	<td  bgcolor=#D0D0D0  width=10%  align = center >Drop</td></tr>";

         datainstulearn($cid,$title,$uname,$uid);

	  echo '</TABLE>';
		      	  	echo '<center>';
//echo '<br><br><br><input type="button" value="BACK" onClick="goHist(-1)">';
	echo '</table>';

	echo '</TD></TR>';

echo '</TABLE>';
include 'footer.php';
}
function showlearn(){
	$sid =$_GET['sid'];
	$eid =$_GET['eid'];
	$title =$_GET['title'];
	$uid =$_GET['uid'];
	$uname =$_GET['uname'];
echo '<center><B><U>ตารางประวัติการเรียนในหลักสูตร '.$title.'  ของ '.$uname.'</U></B><U><BR><br>';
echo '<center><table cellpadding="3" cellspacing="1" border="0" bgcolor="#999999" name = Learn width=80%>';
echo "<tr bgcolor=#D0D0D0 align=center>";
echo "	<td width=20%>บทเรียน</td>";
		echo "	<td width=20%>เวลาที่เข้าเรียน</td>";
		echo "	<td  width=20%>เวลาที่ออกเรียน</td>";;
		echo "	<td width=40%>ระยะเวลาในการเรียน(h:m:s)</td>";

         dataLe($eid,$uid,$sid);
		 
			echo '</TABLE>';
		    echo '<center>';
			echo '</table>';
			echo '</TD></TR>';

echo '</TABLE>';
include 'footer.php';
}

function dataLe($eid,$uid,$sid){

		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

$query1 = "SELECT distinct($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = $eid  ORDER BY $course_trackingcolumn[weight]";

    $result1 = mysql_query($query1);

if (!$result1) 
	die ("result error");

$i =0;
		while($rowdistance = mysql_fetch_array($result1)) 
				{ 
												$a = mysql_num_rows($result1); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				  $set[$i]=$rowdistance[0];
												}                   
								$i++;
					} 
// echo $set[1];  count(*),

$a = mysql_num_rows($result1); 
$b = 0;
$y=0;
while($a > $b){
$query2 = "SELECT  count($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = $eid AND  $course_trackingcolumn[weight]  = '$set[$b]' ORDER BY $course_trackingcolumn[weight]";
 $result2 = mysql_query($query2);
if (!$result2) 
	die ("result error");				
		while($row  = mysql_fetch_array($result2)) 
				{ 
												$aa = mysql_num_rows($result2); 
//echo $a;
												if($y == $aa){
																			exit;
												}else{
																				  $setrow[$b]=$row[0];
												}      
					} 

		$b++;
}
//$course_trackingcolumn[outime]
$a = mysql_num_rows($result1); 
$b = 0;
$timeover = 0;
while($a > $b){
$query3 = "SELECT  *
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] =  $eid ORDER BY $course_trackingcolumn[weight]";
 $result3 = mysql_query($query3);
if (!$result3) 
	die ("result error");				
$i =0;
		while($rowdistance = mysql_fetch_array($result3)) 
				{ 
												$a = mysql_num_rows($result3); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				  $setattime[$i]=$rowdistance[3];
																				  $setoutime[$i]=$rowdistance[6];


		   $dateatime[$i] = date('H:i:s',$setattime[$i]);
		//$dateoutime[$i] = date('H:i:s',$setoutime[$i]);

		  if($setoutime[$i]  == ""){
		           $q[$i]  =  0;

				   $dateoutime[$i] = "null";

		 }else{

           $dateoutime[$i] = date('H:i:s',$setoutime[$i]);

			$q[$i]  =  $setoutime[$i] -  $setattime[$i];
			 $f [$i]= $setoutime[$i];
        $da[$i] = date('H:i:s',$setattime[$i]);
		$dat[$i] = date('H:i:s', $f [$i]);

		$timelearn[$i] =  calcDateDiff($setattime[$i],$setoutime[$i]);

		 }
		//	$s[$i] =   $q[$i];
               //   1141870075 1141870417 
       
												}                   
											
								$i++;
					} 

		$b++;
}
$aaa = mysql_num_rows($result3); 
$b = 0;$t = 0;$f =0;$o=0;
$Hs=0;$Is=0;$ss=0;
$k = 0; $q = 0; $r = 0;
//echo  $timeover[$aaa];
while($aaa > $b){
    
//	$o   =  $o  + $q[$b];
	$times[$b] = explode(':',$timelearn[$b] );
	$b++;

}
while($aaa > $r){
    
	$Hs   =  $Hs  + 	$times[$r][0];
	$Is   =  $Is  + 	$times[$r][1];
	$Ss   =  $Ss  + 	$times[$r][2] ;
	$r++;
}

if($Ss > 60){
$Iss = $Ss % 60;                ///////// วินาทีที่ได้
$sss = $Ss / 60; 
$snow = (integer)$sss;           ///////+นาที

if($snow > 0){
    $Is= $Is + $snow;
}
if($Iss <  10){
    $Ss= '0'.$Iss;
}else{
  $Ss= $Iss;
}
}else{
	if($Ss <  10){
    $Ss= '0'.$Ss;
}else{
  $Ss= $Ss;
}
$Is=$Is;
}

if($Is > 60){
$HIs = $Is % 60;
$IIs = $Is / 60; 
$Inow = (integer)$IIs;

if($HIs <  10){
    $Is= '0'.$HIs;
}else{
  $Is= $HIs;
}
if($Inow > 0){
    $Hs= $Hs + $Inow;
}
}else{
	if($Is <  10){
    $Is= '0'.$Is;
}else{
  $Is= $Is;
}
$Hs=$Hs;
}
if($Hs < 10){
    $Hs = '0'.$Hs;
}else{
  $Hs= $Hs;
}

 $gtime =  $Hs.':'.$Is.':'.$Ss;

//ใช้ระยะเวลาในการเรียนทั้งหมด   '. $time.'
$aaa = mysql_num_rows($result3); 
$aa = mysql_num_rows($result1); 
$aaa = mysql_num_rows($result3); 
$b = 0;
$t = 0;
$f =0;
while($aa > $b){

//bas edit pook version 26/04/49
			echo "<tr align=center bgcolor=#FFFFFF>";
			echo "	<td  rowspan='$setrow[$b]'>$set[$b]</td>";
		    tabl($f , $uid ,$sid);

			for($sub_num=1;$sub_num<$setrow[$b];$sub_num++)
			{
				echo "<tr align=center bgcolor=#FFFFFF>";
				$f++;
				tabl($f , $uid ,$sid);
			}
		$f++;
		$b++;
}

}


function tabl($f,$uid,$sid){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
     $userscolumn = &$lntable['users_column'];

	$query = "SELECT  $userscolumn[uname]
	FROM $userstable WHERE $userscolumn[uid] = $uid";

	$result = $dbconn->Execute($query);

	while(list($uname) = $result->fields) {
			$result->MoveNext();
			$unames =$uname;
	}
	   $course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

$query = "SELECT  $course_enrollscolumn[eid] 
FROM $course_enrollstable 
WHERE $course_enrollscolumn[uid] = $uid AND  $course_enrollscolumn[sid] = $sid";

$result = $dbconn->Execute($query);
  
	while(list($eid) = $result->fields) {
			$result->MoveNext();
			$rets =$eid;
	}

$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

$query1 = "SELECT distinct($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = '$rets'  ORDER BY $course_trackingcolumn[weight]";

    $result1 = mysql_query($query1);

if (!$result1) 
	die ("result error");

$i =0;
		while($rowdistance = mysql_fetch_array($result1)) 
				{ 
												$a = mysql_num_rows($result1); 
												if($i == $a){
																			exit;
												}else{
																				  $set[$i]=$rowdistance[0];
												}                   
								$i++;
					} 

$a = mysql_num_rows($result1); 
$b = 0;
$y=0;
while($a > $b){

$query2 = "SELECT  count($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = '$rets' AND  $course_trackingcolumn[weight]  = '$set[$b]' ORDER BY $course_trackingcolumn[weight]";
 $result2 = mysql_query($query2);
if (!$result2) 
	die ("result error");				
		while($row  = mysql_fetch_array($result2)) 
				{ 
												$aa = mysql_num_rows($result2); 
//echo $a;
												if($y == $aa){
																			exit;
												}else{
																				  $setrow[$b]=$row[0];
												}      
					} 

		$b++;
}
//$course_trackingcolumn[outime]
$a = mysql_num_rows($result1); 
$b = 0;

while($a > $b){
$query3 = "SELECT  *
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = '$rets' ORDER BY $course_trackingcolumn[weight]";
 $result3 = mysql_query($query3);
if (!$result3) 
	die ("result error");				
$i =0;
		while($rowdistance = mysql_fetch_array($result3)) 
				{ 
												$a = mysql_num_rows($result3); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				  $setattime[$i]=$rowdistance[3];
																				  $setoutime[$i]=$rowdistance[6];

	
         $dateatime[$i] = date('H:i:s',$setattime[$i]);
		//$dateoutime[$i] = date('H:i:s',$setoutime[$i]);

		  if($setoutime[$i]  == ""){
		           $q[$i]  =  0;

				   $dateoutime[$i] = "null";

		 }else{

           $dateoutime[$i] = date('H:i:s',$setoutime[$i]);

			$q[$i]  =  $setoutime[$i] -  $setattime[$i];
			 $f [$i]= $setoutime[$i];
        $da[$i] = date('H:i:s',$setattime[$i]);
		$dat[$i] = date('H:i:s', $f [$i]);

		$timelearn[$i] =  calcDateDiff($setattime[$i],$setoutime[$i]);

		 }
					}                   
								$i++;
					} 

		$b++;
}


	echo  "<td align=center >$dateatime[$f]</td>";
	echo "	<td align=center>$dateoutime[$f]</td>";
	echo "	<td align=center >$timelearn[$f]</td></tr>";

	   }

function calcDateDiff($date1,$date2){
   if ($date1 > $date2)
       return FALSE;

   $seconds  = $date2 - $date1;

   // Calculate each piece using simple subtraction

   $weeks     = floor($seconds / 604800);
   $seconds -= $weeks * 604800;

   $days       = floor($seconds / 86400);
   $seconds -= $days * 86400;

   $hours      = floor($seconds / 3600);
   $seconds -= $hours * 3600;

   $minutes   = floor($seconds / 60);
   $seconds -= $minutes * 60;

   // Return an associative array of results
if($hours < 10)
{
	if($hours == 0)
{
 $hours = '00';
}else{
 $hours = '0'.$hours;
}
}else{
$hours = $hours;
}

if($minutes < 10)
{
	if($minutes == 0)
{
 $minutes = '00';
}else{
 $minutes= '0'.$minutes;
}
}
if($seconds < 10)
{
	if($seconds == 0)
{
 $seconds = '00';
}else{
 $seconds = '0'.$seconds;
}
}
   return  $hours.":".$minutes.":".$seconds;

	   }
function student(){
	$sid =$_GET['sid'];
	$title =$_GET['title'];
echo '<center><B><U>ตารางรายชื่อนักเรียนในหลักสูตร '.$title.'</U></B><U><BR><br>';
echo '<center><table cellpadding="3" cellspacing="1" bgcolor="#999999"  align=center name = Learn>';
echo "<tr bgcolor=#D0D0D0 align=center  width=100 >";
   
		echo "	<td width=50 align=center >NO:</td>";
		echo "	<td width=100 align=center >เลขประจำตัว:</td>";
		echo "	<td width=200 align=center>ชื่อ นามสกุล:</td>";
		echo "	<td width=100 align=center>สถานะ:</td></tr>";

         studentname($sid);
		 
echo '</TABLE>';
	  	echo '<center>';
//echo '<br><br><br><input type="button" value="BACK" onClick="goHist(-1)">';
	echo '</table>';

	echo '</TD></TR>';

echo '</TABLE>';
include 'footer.php';
}
function studentname($sid){

list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = $sid ";

    $result2 = mysql_query($query2);

	 $i =0;
	 while($row = mysql_fetch_array($result2)) 
				{ 
												$a = mysql_num_rows($result2); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				           $uid[$i]= $row[3];
												}                   
								$i++;
					} 
			
 $b = 0;
  $d = 0;
 $a = mysql_num_rows($result2); 
			while($a > $b){
			                         	$stuid[$b] = stuid($uid[$b]);
										$stuname[$b] = stuname($uid[$b]);
                                        $state[$b] = state($uid[$b],$sid);
										if($state[$b] ==1){
											$statethis = "Learning";
										}	if($state[$b] ==2){
											$statethis = "Pass";
										}	if($state[$b] ==3){
											$statethis = "Drop";
										}
							   $no = $b+1;
					        echo "<tr bgcolor=#FFFFFF><td width=50 align=center >$no</td>";
							 echo "<td width=100 align=center >$stuid[$b]</td>";
						    echo "<td width= 200 >$stuname[$b]</td>";
							  echo "<td width=100 align=center  >$statethis</td>";
						$b++;
						}
                 
}
function state($uid,$sid){

list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[sid] = $sid AND $course_enrollscolumn[uid] = $uid ";
  
	$result2 = mysql_query($query2);

if (!$result2) {
	die ("result error");
}else{
	 $i =0;
while($rets = mysql_fetch_row($result2)) {
  $state= $rets[5];
	}	
return  $state;
}
}
function stuid($uid){
	
list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

     $userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

    $query2 = "SELECT * 
	FROM $userstable 
		WHERE  $userscolumn[uid] = $uid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $number= $rets[7];
	}	
return  $number;
}
function stuname($uid){
	
list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

     $userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

    $query2 = "SELECT * 
	FROM $userstable 
		WHERE  $userscolumn[uid] = $uid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $uname= $rets[1];
	}	
return  $uname;
}
function learn(){
		$uid =$_GET['uid'];
	$uid =$_GET['uid'];
//echo $uid;
		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

    $query2 = "SELECT * 
	FROM $userstable 
		WHERE  $userscolumn[uid] = $uid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $uname= $rets[1];
	}	
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

    $query2 = "SELECT  * 
	FROM $group_membershiptable 
		WHERE  $group_membershipcolumn[uid] = $uid  ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $gid= $rets[0];
	}	
if($gid != 4){

echo '<center><B><U>ตารางรายงานการสอนของ '.$uname.'</U></B><U><BR><br>';
echo '<center><table cellpadding="1" cellspacing="1" border="1"  align=center name = Learn>';
echo "<tr bgcolor=#CCFFFF align=center  width=100  >";
   
		echo "<tr bgcolor=#FFCC99 align=center  width=100 >";
		echo "	<td width=50 align=center >NO:</td>";
		echo "	<td width=150 align=center >รหัสวิชา:</td>";
		echo "	<td width=350 align=center >ชื่อรายวิชา:</td>";
		echo "	<td width=250  align = center >จำนวนที่เปิดสอน:</td></tr>";

         datainstu($uname);
      echo '</table>';
}else{

echo '<center><B><U>ตารางรายงานการเรียนของ '.$uname.'</U></B><U><BR><br>';
echo '<center><table cellpadding="1" cellspacing="1" border="1"  align=center name = Learn>';
echo "<tr bgcolor=#CCFFFF align=center  width=100  >";
   
		echo "<tr bgcolor=#FFCC99 align=center  width=100 >";
		echo "	<td width=50 align=center >NO:</td>";
		echo "	<td width=100 align=center >รหัสวิชา:</td>";
		echo "	<td width=400 align=center >ชื่อรายวิชา:</td>";
		echo "	<td width=250 align=center>จำนวนครั้งที่เข้าเรียน:</td>";
		echo "	<td width=250  align = center >ระยะเวลาในการเรียน (h:m:s):</td></tr>";

        data($uname);

		echo '</table>';
}
	  	echo '<center>';
		echo '</table>';

		echo '</TD></TR>';

	echo '</TABLE>';

include 'footer.php';

}

function data($uname){
		$uid =$_GET['uid'];
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
    $query2 = "SELECT * 
	FROM $course_enrollstable  
		WHERE  $course_enrollscolumn[uid] = $uid ";

    $result2 = mysql_query($query2);

	 $i =0;
	 while($row = mysql_fetch_array($result2)) 
				{ 
												$a = mysql_num_rows($result2); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				           $eid[$i]= $row[0];
																						    $sid[$i]= $row[1];
												}                   
								$i++;
					} 
			
 $b = 0;
  $d = 0;
 $a = mysql_num_rows($result2); 
			while($a > $b){
				                     //  echo $eid[$b];
									  // echo $sid[$b];
									   $cid[$b] = coursecid($sid[$b]);
									//	echo $cid[$b];	
									$coursecode[$b] = coursecode($cid[$b]);
										$coursename[$b] = coursename($cid[$b]);
										$coursesum[$b] = coursesum($eid[$b]);
										if($coursesum[$b]  == 0){
										   $coursesum[$b] =0;
											$timesum[$b] = "00:00:00";
										}else{
										$timesum[$b] = timesum($eid[$b]);
											}
									//	echo $coursename[$b].'<br>';	
							   $no = $b+1;
					        echo "<tr><td width=50 align=center >$no</td>";
						
							if($coursesum[$b] == 0){
							echo "<td width=100align=center >$coursecode[$b]</td>";
					 	    echo "<td width=400>$coursename[$b]</td>";
							echo "<td width=250 align=center >$coursesum[$b]</td>";
							echo "<td width=250 align=center >$timesum[$b]</td>";
							}else{
							echo "<td width=100 align=center ><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showlearn&amp;sid=$sid[$b]&amp;eid=$eid[$b]&amp;title=$coursename[$b]&amp;uid=$uid&amp;uname=$uname \">$coursecode[$b]</td>";
					 	    echo "<td width=400><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showlearn&amp;sid=$sid[$b]&amp;eid=$eid[$b]&amp;title=$coursename[$b]&amp;uid=$uid&amp;uname=$uname\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$coursename[$b]</td>";
							echo "<td width=250 align=center ><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showlearn&amp;sid=$sid[$b]&amp;eid=$eid[$b]&amp;title=$coursename[$b]&amp;uid=$uid&amp;uname=$uname\">$coursesum[$b]</td>";
							echo "<td width=250 align=center ><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=showlearn&amp;sid=$sid[$b]&amp;eid=$eid[$b]&amp;title=$coursename[$b]&amp;uid=$uid&amp;uname=$uname\">$timesum[$b]</td>";
							}
					

						$b++;
						}

	}
	function	coursecode($cid){
			list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$ccoursescolumn = &$lntable['courses_column'];

    $query2 = "SELECT *
	FROM $coursestable  
		WHERE  $ccoursescolumn[cid] = $cid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $coursecode = $rets[1];
	}	
return $coursecode;
}
	function	timesum($eid){

		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];


$query1 = "SELECT distinct($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE  $course_trackingcolumn[eid]=  $eid  ORDER BY $course_trackingcolumn[weight]";

    $result1 = mysql_query($query1);

if (!$result1) 
	die ("result error");

$i =0;
		while($rowdistance = mysql_fetch_array($result1)) 
				{ 
												$a = mysql_num_rows($result1); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				  $set[$i]=$rowdistance[0];
												}                   
								$i++;
					} 
// echo $set[1];  count(*),
/*$course_trackingcolumneid= $prefix.'_'."eid";
	$course_trackingcolumnweight= $prefix.'_'."weight";
	$course_trackingcolumnuid= $prefix.'_'."uid";
	$course_tracking = $prefix.'_'."course_tracking";*/
		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
$a = mysql_num_rows($result1); 
$b = 0;
$y=0;
while($a > $b){
$query2 = "SELECT  count($course_trackingcolumn[weight])
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = $eid AND  $course_trackingcolumn[weight]  = '$set[$b]' ORDER BY $course_trackingcolumn[weight]";
 $result2 = mysql_query($query2);
if (!$result2) 
	die ("result error");				
		while($row  = mysql_fetch_array($result2)) 
				{ 
												$aa = mysql_num_rows($result2); 
//echo $a;
												if($y == $aa){
																			exit;
												}else{
																				  $setrow[$b]=$row[0];
												}      
					} 

		$b++;
}
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
//$course_trackingcolumn[outime]
$a = mysql_num_rows($result1); 
$b = 0;
$timeover = 0;
while($a > $b){
$query3 = "SELECT  *
FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] =  $eid ORDER BY $course_trackingcolumn[weight]";
 $result3 = mysql_query($query3);
if (!$result3) 
	die ("result error");				
$i =0;
		while($rowdistance = mysql_fetch_array($result3)) 
				{ 
												$a = mysql_num_rows($result3); 
//echo $a;
												if($i == $a){
																			exit;
												}else{
																				  $setattime[$i]=$rowdistance[3];
																				  $setoutime[$i]=$rowdistance[6];
		   $dateatime[$i] = date('H:i:s',$setattime[$i]);

		  if($setoutime[$i]  == ""){
		           $q[$i]  =  0;

				   $dateoutime[$i] = "null";

		 }else{

           $dateoutime[$i] = date('H:i:s',$setoutime[$i]);

			$q[$i]  =  $setoutime[$i] -  $setattime[$i];
			 $f [$i]= $setoutime[$i];
        $da[$i] = date('H:i:s',$setattime[$i]);
		$dat[$i] = date('H:i:s', $f [$i]);

		$timelearn[$i] =  calcDateDiff($setattime[$i],$setoutime[$i]);

		 }
												}         
								$i++;
					} 
		$b++;
}
$aaa = mysql_num_rows($result3); 
$b = 0;$t = 0;$f =0;$o=0;
$Hs=0;$Is=0;$ss=0;
$k = 0; $q = 0; $r = 0;
//echo  $timeover[$aaa];
while($aaa > $b){
    
//	$o   =  $o  + $q[$b];
	$times[$b] = explode(':',$timelearn[$b] );
	$b++;

}
while($aaa > $r){
    
	$Hs   =  $Hs  + 	$times[$r][0];
	$Is   =  $Is  + 	$times[$r][1];
	$Ss   =  $Ss  + 	$times[$r][2] ;
	$r++;
}

if($Ss > 60){
$Iss = $Ss % 60;                ///////// วินาทีที่ได้
$sss = $Ss / 60; 
$snow = (integer)$sss;           ///////+นาที

if($snow > 0){
    $Is= $Is + $snow;
}
if($Iss <  10){
    $Ss= '0'.$Iss;
}else{
  $Ss= $Iss;
}
}else{
	if($Ss <  10){
    $Ss= '0'.$Ss;
}else{
  $Ss= $Ss;
}
$Is=$Is;
}

if($Is > 60){
$HIs = $Is % 60;
$IIs = $Is / 60; 
$Inow = (integer)$IIs;

if($HIs <  10){
    $Is= '0'.$HIs;
}else{
  $Is= $HIs;
}
if($Inow > 0){
    $Hs= $Hs + $Inow;
}
}else{
	if($Is <  10){
    $Is= '0'.$Is;
}else{
  $Is= $Is;
}
$Hs=$Hs;
}
if($Hs < 10){
    $Hs = '0'.$Hs;
}else{
  $Hs= $Hs;
}

 $gtime =  $Hs.':'.$Is.':'.$Ss;
 return $gtime;
}

	function	coursesum($eid){
		list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

    $query2 = "SELECT  *
	FROM $course_trackingtable  
		WHERE  $course_trackingcolumn[eid] = $eid ";

    $result2 = mysql_query($query2);

/*    if (!$result1) {
	$coursesum = 0;
		}else{*/
while($rowdistance = mysql_fetch_array($result2)) 
				{ 
												$coursesum = mysql_num_rows($result2); 
				}
	//$coursesum = mysql_num_rows($query2); 
//		}
		return $coursesum;
}

	function	coursename($cid){
			list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$ccoursescolumn = &$lntable['courses_column'];

    $query2 = "SELECT *
	FROM $coursestable  
		WHERE  $ccoursescolumn[cid] = $cid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $coursename = $rets[3];
	}	
return $coursename;
}

function	coursecid($sid){

				list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];


    $query2 = "SELECT *
	FROM $course_submissionstable  
		WHERE  $course_submissionscolumn[sid] = $sid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $cid = $rets[1];
	}	
return $cid;
}

?>

<SCRIPT LANGUAGE="JavaScript">
<!-- hide this script tag s contents from old browsers-->
function goHist(a) 
{
   history.go(a);      // Go back one.
}
//<!-- done hiding from old browsers -->
</script>

