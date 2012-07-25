<?php
	function blocks_members_block($row) {
	   if (empty($row['title'])) {
			$row['title'] = 'Enroll Course';
		}

		$row['content'] = 'show enroll courses.. 55<BR>..Under construction';
		$row['mod'] = 'Courses';

		return themesidebox($row);
	}
?>