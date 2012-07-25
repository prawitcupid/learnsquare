<?php
function blocks_school_block($row) {
	if (!lnSecAuthAction(0, 'School::', "::", ACCESS_READ)) {
		return false;
	}

    if (empty($row['title'])) {
        $row['title'] = 'Schools';
    }

	 list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$school = $lntable['schools'];
    $column = &$lntable['schools_column'];
    $query = "SELECT $column[code] code,
				$column[sid] sid,
				$column[name] name,
				$column[description] description ,
				$column[logo] logo 
              FROM $school ORDER BY $column[sid]";

	$result = $dbconn->Execute($query);

	if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return false;
    }

//	$logo_school = lnBlockImage('Schools','school'); 
	$logo_school = '<IMG SRC="images/global/bl_red.gif" ALIGN="absmiddle"> <B>'.$row['title'].'</B>'; 
	$content='';
	if (!empty($logo_school)) {
		$content .= $logo_school;
	}
	else {
		$content .= "<B>".$row['title']."</B>";
	}

	$flag=0;
	$flag1=0;

	$content .='<table width="100%" border="0" cellspacing="2" cellpadding="0">';
	$content .=' <tr><td width="50%" valign="top">';
	
	 for($i=0; !$result->EOF; $i++) {
        $data = $result->GetRowAssoc(false);

        if ($i%2 == 0) {
			
		$content .= '<div align="right">';

		//$content .= "<TD WIDTH=50%>";
		$logo= lnBlockImage('Schools',$data['logo']);
		
		$content .= '<table><tr><td align="right">';
		$content .= '<B><A HREF="index.php?mod=Courses&sch='.$data['sid'].'">'.stripslashes($data['name']).'</A></B><BR>';
		$content .= stripslashes($data['description']);
		$content .= '</td><td>';

		if (!empty($logo)) {
		//$content .= '<td><A HREF="index.php?mod=Courses">'.$logo.'</A></td>';
		$content .= '<A HREF="index.php?mod=Courses&sch='.$data['sid'].'">'.$logo.'</A>';

		}
		
		
		$content .= '</td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td height="5" background="images/global/line1.gif"></td></tr></table>';
		$content .= '</DIV>';
		
		} //end  if ($i%2 == 0)
		
		 		
		if($flag==0)
		{

		$content1 = '</td><td width="5" background="images/global/line1.gif"></td><td weight="50%" valign="top">';
		$flag=1;
		}	    

		
		if ($i%2 == 1) {
			
			if($flag1==0)
			{
			$content2 ='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td height="30"></td></tr></table>';
			$flag1=1;
			}
			
			
		$logo= lnBlockImage('Schools',$data['logo']);
		
		$content2 .= '<table><tr><td>';
		if (!empty($logo)) {
		//$content .= '<td><A HREF="index.php?mod=Courses">'.$logo.'</A></td>';
		$content2 .= '<A HREF="index.php?mod=Courses&sch='.$data['sid'].'">'.$logo.'</A>';

		}		
		$content2 .= '</td><td>';		
		$content2 .= '<B><A HREF="index.php?mod=Courses&sch='.$data['sid'].'">'.stripslashes($data['name']).'</A></B><BR>';
		$content2 .= stripslashes($data['description']);
			
		$content2 .= '</td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td height="5" background="images/global/line1.gif"></td></tr></table>'; 
  
		}

        $result->MoveNext();
    }

	//if ($i % 2) 
	$content_final = $content.$content1.$content2; 
		$content_final .="</TD></TR>";
	$content_final .="</TABLE>";




	$row['content'] =$content_final;

	return themesidebox($row);
 }
?>