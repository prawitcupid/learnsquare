<?php
class Scorm_function
{
		var $cmi_core_lesson_status;
		var $cmi_core_exit;
		var $cmi_core_total_time;
		var $cmi_core_score_raw;
		var $cmi_suspend_data;
		var $cmi_launch_data;
		var $cmi_core_student_id;
		var $cmi_core_student_name;
		var $cmi_core_lesson_location;
	
	function Scorm_function()
	{
		$this->cmi_core_student_id = lnSessionGetVar('uid');
		$this->cmi_core_student_name=lnSessionGetVar('uname');
		$this->cmi_core_lesson_location='';
		$this->cmi_core_lesson_status='not attempted';
		$this->cmi_core_exit='';
		$this->cmi_core_lesson_status='';
		$this->cmi_core_total_time='';
		$this->cmi_core_score_raw='';
		$this->cmi_suspend_data='';
		$this->cmi_launch_data='';

	}
}

class Scos
{
	var $id;
}
class SCORM
{
	var $auto;
}
?>