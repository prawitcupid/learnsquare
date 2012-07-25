<?php
function lnwriteexcel($title,$header,$data)
{
	
	require_once "includes/writeexcel/class.writeexcel_workbook.inc.php";
	require_once "includes/writeexcel/class.writeexcel_worksheet.inc.php";
	require_once "includes/writeexcel/functions.writeexcel_utility.inc.php";
	//echo xl_rowcol_to_cell(0,1);
	//echo "<PRE>";
	//print_r($header);
	//print_r($data);
	//echo "</PRE>";
	//exit();
	$token = md5(uniqid(rand(), true));
	$fname= "courses/$token.xls";
	$workbook = new writeexcel_workbook($fname);
	
	$worksheet =& $workbook->addworksheet($title);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	## Set Format  ##
	$xlscelldesc_header =& $workbook->addformat();
	//$xlscelldesc_header->set_font('Angsana New');
	$xlscelldesc_header->set_size(16);
	$xlscelldesc_header->set_color('black');
	$xlscelldesc_header->set_fg_color('write');
	$xlscelldesc_header->set_bold(1);
	$xlscelldesc_header->set_text_v_align(1);
	$xlscelldesc_header->set_merge(1);

	$xlscelldesc_header1 =& $workbook->addformat();
	//$xlscelldesc_header->set_font('Angsana New');
	$xlscelldesc_header->set_size(16);
	$xlscelldesc_header->set_color('black');
	$xlscelldesc_header->set_fg_color('write');
	$xlscelldesc_header->set_bold(1);
	$xlscelldesc_header->set_align('left');
	$xlscelldesc_header->set_merge(1);
	
	$xlsCellDesc =& $workbook->addformat();
	//$xlsCellDesc->set_font('Angsana New');
	$xlsCellDesc->set_size(14);
	$xlsCellDesc->set_color('black');
	$xlsCellDesc->set_bold(1);
	$xlsCellDesc->set_align('left');
	$xlsCellDesc->set_text_v_align(1);
	## End of Set Format ##

	## Set Column Width & Height  กำหนดความกว้างของ Cell
	//$worksheet->set_column('A:B', 2);
	//$worksheet->set_column('B:C', 4);
	//$worksheet->set_column('C:D', 11.29);
	//$worksheet->set_column('D:E', 21);
	//$worksheet->set_column('E:F', 15);
	//$worksheet->set_column('F:G', 32);
	$celldesc_h = 16.50;

	## Writing Data  เพิ่มข้อมูลลงใน Cell
	//$worksheet->write_blank(A1,$xlscelldesc_header);
	$worksheet->write(xl_rowcol_to_cell(0,0),iconv("UTF-8","TIS-620",$title), $xlscelldesc_header1);
	//$worksheet->write_blank(C1,$xlscelldesc_header);
	//$worksheet->write_blank(D1,$xlscelldesc_header);
	//$worksheet->write_blank(E1,$xlscelldesc_header);
	//$worksheet->write_blank(F1,$xlscelldesc_header);

	# กำหนดความสูงของ Cell และสร้าง header
	$worksheet->set_row(1, $celldesc_h);
	for($i=0;$i <= count($header);$i++)
	{
		
		$worksheet->write(xl_rowcol_to_cell(1,$i),iconv("UTF-8","TIS-620",$header[$i]));
	}

	

	# ตรงนี้คือดึงข้อมูลจาก mysql มาใส่ใน Cell
	for($i=0;$i <= count($data) -1;$i++)//row
	{
		if(is_array($data[$i]))
		{
			for($j=0;$j <= count($data[$i]);$j++)//column
			{	
				$worksheet->set_row($i+2, 19.80);
				$worksheet->write(xl_rowcol_to_cell($i+2,$j), iconv("UTF-8","TIS-620",$data[$i][$j]), $xlsCellDesc);
			}
		}else
		{
			$worksheet->set_row(2, 19.80);
			$worksheet->write(xl_rowcol_to_cell(2,$i), iconv("UTF-8","TIS-620",$data[$i]), $xlsCellDesc);
		}
	}
	# เสร็จแล้วก็ส่งไฟล์ไปยัง Browser ครับแค่นี้ก็เสร็จแล้ว
	$workbook->close();
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".basename("class_data.xls").";");
	header("Content-Transfer-Encoding: binary ");
	header("Content-Length: ".filesize($fname));
	readfile($fname);
	unlink($fname);
	return true;
}
	?>