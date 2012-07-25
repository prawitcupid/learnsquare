<?
	@extract(@$vars);

	echo "<html>";
	echo "<head><title>Certificate No. $cerno : $coursename </title></head>";
	echo "<body><center>";
	if (file_exists("certificate/".$eid.".jpg")) {
		echo "<img src=certificate/$eid.jpg>";
	}
	else {

		$enrollinfo = lnEnrollGetVars($eid);
		$submissioninfo = lnSubmissionGetVars($enrollinfo['sid']);
		$userinfo = lnUserGetVars($enrollinfo['uid']);
		$courseinfo = lnCourseGetVars($submissioninfo['cid']);
		$start = $submissioninfo['start']; 			
		$course_length = lnCourseLength($submissioninfo['cid']) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$datecer = $from . ' - ' . $to;
		$instructorinfo = lnUserGetVars($submissioninfo['instructor']);
		
		$width=595;
		$height=835;
		$font='certificate/font/cor.ttf';
		
		$fileopen="certificate/".$eid.".jpg";

		$bg="certificate/instructor/".$instructorinfo['uname'].".jpg";

		if (!file_exists($bg)) {
			$bg="certificate/instructor/certificate.jpg";
		}

		if ($d=getimagesize($bg)) {
				$src_bg = imagecreatefromjpeg($bg);
				$dst = @imagecreatetruecolor($width, $height)  or die("Cannot Initialize new GD image stream");

				$white = imagecolorallocate($dst,255,255,255);
				$black = imagecolorallocate($dst,0,0,0);
				$blue = imagecolorallocate($dst,0,0,255);
				imagefill($dst,0,0,$white);

				imagecopyresized($dst,$src_bg,0,0,0,0,$d[0],$d[1],$d[0],$d[1]);

				$begin=253;
				$refno="Certificate No. ".$eid;
				if (empty($userinfo['name'])) {
					$studentname=$userinfo['uname'];
				}
				else {
					$studentname=$userinfo['name'];
				}

				ImageTTFText ($dst, 18, 0,$begin, 365, $black, $font,th2uni($courseinfo['code']));
				ImageTTFText ($dst, 18, 0, $begin, 410, $black, $font,th2uni($courseinfo['title']));
				ImageTTFText ($dst, 16, 0, $begin, 455, $black, $font,th2uni($studentname));
				ImageTTFText ($dst, 16, 0, $begin, 500, $black, $font,th2uni($datecer));
				ImageTTFText ($dst, 10, 0, 20, 800, $black, $font,th2uni($refno));

				imagejpeg($dst,$fileopen);
				imagedestroy($dst);
				imagedestroy($src_bg);	
		}

		echo "<img src='certificate/$eid.jpg'>";
	}




/*- - - หา center ของ text string - - -*/
function centerx($str,$size) {
	$width=595;

	$len=strlen($str);

	return ($width/2) - (($len/2)*$size);
}


/*- - - ภาษาไทยสำหรับ GD LIB - - -*/
function th2uni($sti) {

$th2unimap = array( 
'ก' => "&#3585;", 'ข' => "&#3586;", 'ฃ' => "&#3587;", 'ค' => "&#3588;", 'ฅ' => "&#3589;", 'ฆ' => "&#3590;", 'ง' => "&#3591;",
'จ' => "&#3592;", 'ฉ' => "&#3593;", 'ช' => "&#3594;", 'ซ' => "&#3595;", 'ฌ' => "&#3596;", 'ญ' => "&#3597;", 'ฎ' => "&#3598;",
'ฏ' => "&#3599;", 'ฐ' => "&#3600;", 'ฑ' => "&#3601;", 'ฒ' => "&#3602;", 'ณ' => "&#3603;", 'ด' => "&#3604;", 'ต' => "&#3605;",
'ถ' => "&#3606;", 'ท' => "&#3607;", 'ธ' => "&#3608;", 'น' => "&#3609;", 'บ' => "&#3610;", 'ป' => "&#3611;", 'ผ' => "&#3612;",
'ฝ' => "&#3613;", 'พ' => "&#3614;", 'ฟ' => "&#3615;", 'ภ' => "&#3616;", 'ม' => "&#3617;", 'ย' => "&#3618;", 'ร' => "&#3619;",
'ฤ' => "&#3620;", 'ล' => "&#3621;", 'ฦ' => "&#3622;", 'ว' => "&#3623;", 'ศ' => "&#3624;", 'ษ' => "&#3625;", 'ส' => "&#3626;",
'ห' => "&#3627;", 'ฬ' => "&#3628;", 'อ' => "&#3629;", 'ฮ' => "&#3630;", 'ฯ' => "&#3631;", 'ะ' => "&#3632;", 'ั' => "&#3633;",
'า' => "&#3634;", 'ำ' => "&#3635;", 'ิ' => "&#3636;", 'ี' => "&#3637;", 'ึ' => "&#3638;", 'ื' => "&#3639;", 'ุ' => "&#3640;",
'ู' => "&#3641;", 'ฺ' => "&#3642;", '฿' => "&#3647;", 'เ' => "&#3648;", 'แ' => "&#3649;", 'โ' => "&#3650;", 'ใ' => "&#3651;",
'ไ' => "&#3652;", 'ๅ' => "&#3653;", 'ๆ' => "&#3654;", '็' => "&#3655;", '่' => "&#3656;", '้' => "&#3657;", '๊' => "&#3658;",
'๋' => "&#3659;", '์' => "&#3660;", 'ํ' => "&#3661;", '๎' => "&#3662;", '๏' => "&#3663;", '๐' => "&#3664;", '๑' => "&#3665;",
'๒' => "&#3666;", '๓' => "&#3667;", '๔' => "&#3668;", '๕' => "&#3669;", '๖' => "&#3670;", '๗' => "&#3671;", '๘' => "&#3672;",
'๙' => "&#3673;", '๚' => "&#3674;", '๛' => "&#3675;");

    $sto = '';
    $len = strlen($sti);
    for ($i = 0; $i < $len; $i++) {
        if ($th2unimap[$sti{$i}])

            $sto .= $th2unimap[$sti{$i}];
        else
            $sto .= $sti{$i};
    }
    return $sto;
}
?>