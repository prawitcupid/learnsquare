<?php
/************************************************************/
/* Function themeheader()													 */
/************************************************************/
function themeheader() {
	global $index,$expand;
	$thistheme = lnConfigGetVar('Default_Theme');

?>
<table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
<TR><TD VALIGN="TOP">

<!-- Start Outer Table-->
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
  <tr>
    <td colspan="3"><table colspan="3" width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr height="150">
      		
			<?
 			echo "<td width='100' align='right' background='themes/";
			echo $thistheme;
			echo "/images/top_bar01.gif' valign='bottom'>";
			echo "</td>";			
			
			
 			echo "<td width='800' align='right' background='themes/";
			echo $thistheme;
			echo "/images/top_bar.gif' valign='bottom'>";
			echo "</td>";
			
 			echo "<td width='100' align='right' background='themes/";
			echo $thistheme;
			echo "/images/top_bar02.gif' valign='bottom'>";
			echo "</td>";			
			
			?>	
           	
      </tr>
    </table></td>
  </tr>
  
  
  <tr>
  
  	<!--Left Side of Background in Outer Table -->
    <td width="100" rowspan="2" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr bgcolor="#FF9900">
            <?
            echo '<td height="300" background="themes/'.$thistheme.'/images/side_left_outer_table.gif"></td>';
			?>
          </tr>
          <tr>
            <td></td>
          </tr>
        </table>
    </td>
    
    <td><table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr height="25">
        	<?
			echo '<td background="themes/'.$thistheme.'/images/bg_main_menu.gif" align="right">';
			mainmenu();
			echo '</td>';
			?>
      </tr>
    </table></td>
    
    <!--Right Side of Background in Outer Table -->
    <td width="100" rowspan="2" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr bgcolor="#FF9900">
          	<?
            echo '<td height="300" background="themes/'.$thistheme.'/images/side_right_outer_table.gif"></td>';
			?>
          </tr>
          <tr>
            <td></td>
          </tr>
        </table>
    </td>
    
  </tr>
  <tr>
    <td>
    
 <!--Start Content in the Middle-->    
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <!-- Start position of left block -->
		<? 	if ($GLOBALS['expand']!=1) { 
            echo '<td width="150" valign="top" align="center">';
			blocks(left);
			echo '</td>';
		 } 
		?>
		<!-- End position of left block -->


		<!-- Start position of center block -->
			<td valign="top" bgcolor="#FFFFFF">
		<?	
			if ($GLOBALS['index'] == 1) {
			blocks('center');
			}
}//end function ThemeHeader()
?>

<?
/************************************************************/
/* Function themefooter()*/
/************************************************************/
function themefooter() {
$thistheme = lnConfigGetVar('Default_Theme');
echo "</td>";
	//<!-- end position of center block -->

	// <!-- Start position of right block  -->
    if ($GLOBALS['index'] == 1) { 
		echo '<td width="150" valign="top" align="center">';
		blocks(right);
		echo "</td>\n";
	}
	// <!-- End position of right block  -->

	echo "</tr></table>";
?>
<!-- End of Content in the Middle -->

    
<!-- Start Footer -->
<TABLE WIDTH="800" BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR HEIGHT="30">
	<TD WIDTH="800" BGCOLOR="#FFFFFF"  VALIGN="MIDDLE" ALIGN="CENTER">
				<? footmsg(); 
				echo "<a href='themes/";
				echo $thistheme;
				echo "/license.html' target='_blank'><U>license agreement</U></a>";
    			?>
    			Version : 
				<?php
					$filename = "version.txt";
					$handle = fopen($filename, "r");
					$contents = fread($handle, filesize($filename));
					fclose($handle); 
					echo $contents;
				 ?>
    </TD>
	</TR>
</TABLE>
<!-- End Footer -->

      
      </td>
  </tr>
</table>
<!-- End of Outer Table -->

</TD></TR></TABLE>

<?
} //*** end Function themefooter()




/************************************************************/
/* Function themesidebox()												*/
/************************************************************/
function themesidebox($block) {
	$thistheme = lnConfigGetVar('Default_Theme');
	$title = $block['title'];
	$content = $block['content'];
	$module = strtolower($block['bkey']);

	if($block['position'] == 'c') {  // center block
	?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr><td width="100%" bgcolor="#0099CC">
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
					<tr><td width="100%" bgcolor="#ffffff">
						<?=$content?>
					</td></tr>
				</table>
		</td></tr>
		</table>


		<CENTER>
		<table border="0" cellpadding="0" cellspacing="0" width="95%">
		<tr>
        <?
        echo "<td width='100%' background='themes/";
		echo $thistheme;
		echo "/images/line.gif' height='1'></td>";
		?>
        
        </tr>
		</table>
		</CENTER>
        
        
	<?
	}
	
	if($block['position'] == 'l') {
	?>
		<table border="0" cellpadding="0" cellspacing="0"  width="150" align="center" height="30">
				<tr>
					<?
					
					
                    echo '<td background="themes/'.$thistheme.'/images/bg_topic.gif" width="150" height="30" align="center">';
						if (file_exists("themes/".$thistheme."/images/$module/block_title.jpg")) {
							echo "<img src=\"themes/".$thistheme."/images/$module/block_title.jpg\" border=0>";
						}
						else {
							echo "&nbsp;<font color=\"#000000\"><b> ".$title."</b></font>";
						}
					?>
			</td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0"  width="150">
			<tr bgcolor="FFFFFF"><td colspan="3" width="150">
            <?
            echo '<img height="15" src="themes/'.$thistheme.'/images/b_block_top.gif">';
			?>
			</td></tr>
			<tr bgcolor="FFFFFF">
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side1.gif"></td>';
			?>
			<td bgcolor="FFFFFF" width="140"><?=$content?></td>
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side2.gif"></td>';
			?>
			</tr>
			<tr bgcolor="FFFFFF">
            <?
			echo '<td colspan="3" width="150"><img src="themes/'.$thistheme.'/images/b_block_bottom.gif"></td>';
			?>
			</tr>
		</table>

		
		
	<?
	}

	else if($block['position'] == 'r') {
	?>
		<table border="0" align="center" width="150" height="30" cellpadding="0" cellspacing="0">
			<tr>
					<?
						echo '<td background="themes/'.$thistheme.'/images/bg_topic.gif" width="150" height="20" align="center">';
						if (file_exists("modules/".$thistheme."/images/$bkey.jpg")) {
							echo "<img src=\"modules/".$thistheme."/images/$bkey.jpg\" border=0>";
						}
						else {
							echo "&nbsp;<font color=\"#000000\"> <b>".$title."</b></font>";
						}
					?>
			</td></tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0"  width="150">
			<tr bgcolor="FFFFFF"><td colspan="3" width="150">
            <?
            echo '<img src="themes/'.$thistheme.'/images/b_block_top.gif">';
			?>
			</td></tr>
			<tr bgcolor="FFFFFF">
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side1.gif"></td>';
			?>
			<td bgcolor="FFFFFF" width="140"><?=$content?></td>
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side2.gif"></td>';
			?>
			</tr>
			<tr bgcolor="FFFFFF">
            <?
			echo '<td colspan="3" width="150"><img src="themes/'.$thistheme.'/images/b_block_bottom.gif"></td>';
			?>
			</tr>
		</table>
	<?		
		//echo '<IMG SRC="themes/'.$thistheme.'/images/page_flip_r.gif" WIDTH="150" HEIGHT="15" BORDER="0" ALT="">';
	}
}

/************************************************************/
/* Function opentable()														*/
/************************************************************/
function OpenTable() {
	$bgcolor1 = "#ffffff";
	$bgcolor2 = "#000000";

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" bgcolor=\"$bgcolor1\" height=\"350\"><tr><td valign=\"top\">\n";
}

function CloseTable() {
    echo "</td></tr></table></td></tr></table>\n";
}

function OpenTable2() {
	$bgcolor3 = "#ffffff";
	$bgcolor4 = "#FF9900";
    echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor4\" align=\"center\"><tr><td>\n";
    echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"$bgcolor3\"><tr><td>\n";
}

function CloseTable2() {
    echo "</td></tr></table></td></tr></table>\n";
}

?>