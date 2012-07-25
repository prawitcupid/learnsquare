<?php
function blocks_online_block($row) {
	if (!lnSecAuthAction(0, 'Onlineblock::', "::", ACCESS_READ)) {
		return false;
	}

	$content = lnGetUserNumber('number');
	$row['content'] = $content;

	return themesidebox($row);
}

?>