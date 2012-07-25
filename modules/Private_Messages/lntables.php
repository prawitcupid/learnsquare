<?php
/**
 * This function is called internally 
 */
function Private_Messages_lntables()
{
	global $config;
    
	$prefix = $config['prefix'];

	// Initialise table array
    $lntable = array();
	
	$privmsgs = $prefix . '_privmsgs';
	$lntable['privmsgs'] = $privmsgs;
	$lntable['privmsgs_column'] = array ('id'  => $privmsgs . '.ln_privmsgs_id',
											'type'  => $privmsgs . '.ln_privmsgs_type',
											'priority'  => $privmsgs . '.ln_privmsgs_priority',
											'subject'  => $privmsgs . '.ln_privmsgs_subject',
											'message'  => $privmsgs . '.ln_privmsgs_message',
											'from_uid'  => $privmsgs . '.ln_privmsgs_from_uid',
											'to_uid'  => $privmsgs . '.ln_privmsgs_to_uid',
											'date'  => $privmsgs . '.ln_privmsgs_date',
											'ip'  => $privmsgs . '.ln_privmsgs_ip',
											'enable'  => $privmsgs . '.ln_privmsgs_enable'
											);

    return $lntable;
}

?>