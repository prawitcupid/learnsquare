<?php

function blocks_mining_block($row) {
	   
	   	if (empty($row['title'])) {
			$row['title'] = 'MINING';
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
	
		$miningtable = $lntable['xestat'];
		$miningcolumn = &$lntable['xestat_column'];
		
		$sql = "SELECT * FROM ( ";
		$sql .= "SELECT ip, time, url, lesson_id, course_id, ln_title AS lesson_title, count( lesson_id ) AS count_lesson FROM ( ";
		$sql .= "SELECT * , substring( lid1, 5 ) AS lesson_id, substring( cid1, 5 ) AS course_id FROM ( ";
		$sql .= "SELECT * , if( instr( lid, '&' ) =0, lid, left( lid, instr( lid, '&' ) -1 ) ) AS lid1, left( cid, instr( cid, '&' ) -1 ) AS cid1 FROM ( ";
		$sql .= "SELECT * , substring( url, instr( url, 'lid=' ) ) AS lid, substring( url, instr( url, 'cid=' ) ) AS cid FROM ln_xestat ";
		$sql .= "WHERE url LIKE '%&lid=%' ";
		$sql .= "AND year( time ) = '".date('Y')."' ";
		//$sql .= "AND month( time ) = '11' ";
		$sql .= ") AS lidtbl ";
		$sql .= ") AS lidtbl1 ";
		$sql .= ") AS lidtbl2, ln_lessons ";
		$sql .= "WHERE lesson_id = ln_lid ";
		$sql .= "GROUP BY ln_lid ";
		$sql .= "ORDER BY ln_lid ";
		$sql .= ") AS lidtble3 ";
		$sql .= "WHERE count_lesson >=5 ";
		$sql .= "ORDER BY count_lesson DESC ";
		$sql .= "LIMIT 0 , 5 ";
		
		$result = $dbconn->Execute($sql);
		
		//$row['content'] = "dddd";
		$row['mod'] = 'Mining';
		$row['content'] .= '<ul style="list-style:decimal;margin:0;margin-left:20px;padding:0">';
		$row['content'] .= 'ประจำปี';
		while (list($ip, $time, $url, $lesson_id, $course_id, $lessons_title, $count_lesson) = $result->fields){
			$row['content'] .= '<li><a href="'.$url.'" style="font-size:10px">'.$lessons_title.'</a></li>';
			$result->MoveNext();
		}
		
		$row['content'] .= '</ul>';
		
		return themesidebox($row);
	}

?>
