<?php
/************************************************************/
/* Function themeheader()													 */
/************************************************************/
function themeheader() {
	global $index,$expand;
	$thistheme = lnConfigGetVar('Default_Theme');

?>
<!-- Start Outer Table-->
<TABLE WIDTH="100%"  HEIGHT="100%" BGCOLOR="#ff9999" CELLPADDING="0" CELLSPACING="0">

<!-- Start Top Bar -->
<TR>
	<TD ALIGN="CENTER" VALIGN="TOP">

	<TABLE border="0" cellpadding="0" cellspacing="0" width="800">
	<TR>
		<TD>
			<table border="0" cellpadding="0" cellspacing="0" width="1000">
			<tr height="150">
			<?
 			echo "<td align='right' background='themes/";
			echo $thistheme;
			echo "/images/top_bar.jpg' valign='bottom'>";
			echo "</td>";
			?>			
			</tr>
			</table>
		</TD>
	</TR>
	</TABLE>

	</TD>
 </TR>
 <!-- End Top Bar -->
 
 <!-- Start Main Menu -->
 <TR>
 	<TD ALIGN="CENTER" VALIGN="TOP">
    
		<table border="0" cellpadding="0" cellspacing="0" width="800" bordercolor="#FFFFFF">
			<TR height="25" bgcolor="#FFFFFF">

			<?
			echo '<td background="themes/'.$thistheme.'/images/bg_main_menu.gif" align="right">';
			mainmenu();
			echo '</td>';
			?>
		
			</TR>
		</table>
        
	</TD>
</TR>
<!-- End Main Menu -->   

   
<TR>
	<TD ALIGN="CENTER" VALIGN="TOP">
    
<!--Start Content in the Middle--> 
 		<table border="0" cellpadding="3" cellspacing="0" width="800"> 
		<tr>
        
		<!-- Start position of left block -->
		<? 	if ($GLOBALS['expand']!=1) { ?>
			<td width="150" valign="top" align="center" bgcolor="#FFFFFF">
		<?
			blocks(left);
		?>
			</td>
		<? } ?>
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
		echo "<td width=\"150\" valign=\"top\" align=\"center\" bgcolor=\"#FFFFFF\">\n";
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
	<TD WIDTH="800" BGCOLOR="#CCCCCC"  VALIGN="MIDDLE" ALIGN="CENTER">
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


	</TD> 
</TR>
<!-- Tag Closed for Main Content Center -->

</TABLE> 
<!-- End of Outer Table -->

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
		<table border="0" cellpadding="0" cellspacing="0"  width="150">
			<tr>
					<?
                    echo '<td background="themes/'.$thistheme.'/images/bg_blue.gif" width="150" height="30" align="center">';
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
			<tr>
			<td colspan="3" width="150">
            <?
            echo '<img src="themes/'.$thistheme.'/images/b_block_top.gif">';
			?>
			</td>
			</tr>
			<tr>
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side1.gif"></td>';
            ?>
			<td width="140"><?=$content?></td>
            <?            
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side2.gif"></td>';
			?>
			</tr>
			<tr>
            <?
			echo '<td colspan="3" width="150"><img src="themes/'.$thistheme.'/images/b_block_bottom.gif"></td>';
			?>
			</tr>
		</table>
		
		<br>
	<?
	}

	else if($block['position'] == 'r') {
	?>
		<table border="0" align="center" width="150" height="30" cellpadding="0" cellspacing="0">
			<tr>
					<?
						echo '<td background="themes/'.$thistheme.'/images/bg_blue.gif" width="150" height="20" align="center">';
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
			<tr><td colspan="3" width="150">
            <?
            echo '<img src="themes/'.$thistheme.'/images/b_block_top.gif">';
			?>
			</td></tr>
			<tr >
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side1.gif"></td>';
			?>
			<td width="140"><?=$content?></td>
            <?
			echo '<td width="5" background="themes/'.$thistheme.'/images/b_block_side2.gif"></td>';
			?>
			</tr>
			<tr>
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