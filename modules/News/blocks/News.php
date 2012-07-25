<?php

function blocks_news_block($row) {
	   
	   if (empty($row['title'])) {
			$row['title'] = _TITLE_NEWS;
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
	
		$newstable = $lntable['news'];
		$newscolumn = &$lntable['news_column'];
		$result = $dbconn->Execute("SELECT $newscolumn[idq], $newscolumn[titleq], $newscolumn[nameq], $newscolumn[dateq] FROM $newstable ORDER BY $newscolumn[idq] DESC");
		
	if ($result ->RecordCount()) {
				$row['content'] .= '<IMG SRC="images/global/bl_red.gif" ALIGN="absmiddle"> <B>'.$row['title'].'</B><BR>&nbsp;';
				$row['content'] .= '<table width="100%" cellpadding="2" cellspacing="0">';

				$countrow=0;

		while((list($idq,$titleq,$nameq,$dateq) = $result->fields) && ($countrow++<5))
		{
		$result->MoveNext();

		$date = date('d-M-Y H:i', $dateq);
		//$date =  Date_Calc::dateFormat2($dateq, "%e %b %y");

		$row['content'] .= '<tr valign=middle>';
		$row['content'] .= '<td><IMG SRC="images/global/arrow.gif" ALIGN="absmiddle"><A HREF="index.php?mod=News&amp;op=add_aform&amp;idq='.$idq.'"> '.$titleq.'</A> (<b>โดย</b> '.$nameq.' -- '.$date.' )</td>';
		$row['content'] .= '</tr>';
		}
				
			$row['content'] .= '</table>';
			$row['mod'] = 'News';
			$row['content'] .= '<p align="right"><IMG SRC="images/global/view.gif"><a href="index.php?mod=News"> ดูข่าวทั้งหมด</a></p>';
	                return themesidebox($row);
		
		}

	}

?>
