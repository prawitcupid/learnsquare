<?php
	function blocks_courses_block($row) {
	   if (empty($row['title'])) {
			$row['title'] = 'Courses';
		}


		$row['content'] = '....Under construction';
	
		$row['mod'] = 'Courses';

		return themesidebox($row);
	}
?>