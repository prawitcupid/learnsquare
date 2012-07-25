<?php
/**
 * This function is called internally 
 */
function Calendar_lntables()
{
	global $config;
    
	$prefix = $config['prefix'];

	// Initialise table array
    $lntable = array();
	
	$calendar = $prefix . '_calendar';
	$lntable['calendar'] = $calendar;
	$lntable['calendar_column'] = array ('calid'         => $calendar . '.ln_calid',
                                   'type'        => $calendar . '.ln_type',
                                   'title'       => $calendar . '.ln_title',
                                   'uid'         => $calendar . '.ln_uid',
                                   'note'         => $calendar . '.ln_note',
                                   'date'    => $calendar . '.ln_date',
                                   'timestart'      => $calendar . '.ln_timestart',
                                   'timeend'     => $calendar . '.ln_timeend',
                                   'timetype' =>$calendar . '.ln_timetype');

	// Return the table information
    return $lntable;
}

?>