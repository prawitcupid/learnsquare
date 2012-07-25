<?php
/**
 * This function is called internally 
 */
function Forums_lntables()
{
	global $config;
    
	$prefix = $config['prefix'];

	// Initialise table array
    $lntable = array();
	
	$forums = $prefix . '_forums';
	$lntable['forums'] = $forums;
	$lntable['forums_column'] = array ('fid'         => $forums . '.ln_fid',
									   'sid'        => $forums . '.ln_sid',
									   'tid'     =>$forums . '.ln_tid',
									   'tix'         => $forums . '.ln_tix',
									   'uid'         => $forums . '.ln_uid',
									   'subject'         => $forums . '.ln_subject',
									   'post_text'         => $forums . '.ln_post_text',
									   'icon'         => $forums . '.ln_icon',
									   'ip'         => $forums . '.ln_ip',
									   'post_time'         => $forums . '.ln_post_time',
										'options'    => $forums . '.ln_options');
    // Return the table information
    return $lntable;
}

?>