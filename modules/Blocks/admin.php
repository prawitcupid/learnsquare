<?php
/**
* Blocks Module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::".@$bid, ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}

/* options */
if ($op) {
	$vars= array_merge($_GET,$_POST);
	switch($op) {
		case "deactivate_block": 
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				deactivate_block($vars); break;
		case "activate_block":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				activate_block($vars); break;
		case "increase_weight": 
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				increase_weight($vars); break;
		case "decrease_weight":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				decrease_weight($vars); break;
		case "shift_left":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				shift_left($vars); break;
		case "shift_right":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				shift_right($vars); break;
		case "delete_block":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_DELETE)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				delete_block($vars); break;
		case "add_block":
				if (!lnSecAuthAction(0, 'Blocks::', "::$bid", ACCESS_ADD)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				add_block($vars); break;	

	}
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_BLOCKADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Blocks&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

// List Modules
list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$blockstable = $lntable['blocks'];
$blockscolumn = &$lntable['blocks_column'];
 $sql = "SELECT $blockscolumn[bid],
                   $blockscolumn[bkey],
                   $blockscolumn[title],
                   $blockscolumn[content],
                   $blockscolumn[position],
                   $blockscolumn[weight],
                   $blockscolumn[mid],
                   $blockscolumn[active]
            FROM $blockstable
            ".@$where."
            ORDER BY $blockscolumn[position],
                     $blockscolumn[weight]";

$result = $dbconn->Execute($sql);
$numrows = $result->PO_RecordCount();

echo '<center><table class="list" cellspacing=1 cellpadding=3 width=100%>'
 .'<tr align=center><td class=head>'._ORDER.'</td><td class=head>'._STATE.'</td><td class=head>'. _POSITION.'</td><td class=head>'._TITLE.'</td><td class=head>'._MODULE.'</td><td class=head>'._CONTENT.'</td><td class=head>&nbsp;</td></tr>';

$rownum = 1;
$lastpos = '';
$active_count = 0;

while(list($bid, $bkey, $title, $content, $position, $weight, $mid, $active) = $result->fields) {
	$result->MoveNext();
	 $prev_active_count = $active_count;
	 $active_count++;

	// Show state
	if ($active == "1" ) {
		if ($mid !="10")
		{
			$active_icon = "<a href=index.php?mod=Blocks&amp;file=admin&amp;op=deactivate_block&amp;bid=$bid&amp;weight=$weight>" . '<img src="images/global/green_dot.gif" border=0 ALT="' . _ACTIVATE . '">' . '</a>';
		}
		else
		{
		$active_icon = '<img src="images/global/gray_dot.gif" border=0 ALT="' . _DEACTIVATE . '">';
		}
	}
	else {
		$active_icon = "<a href=index.php?mod=Blocks&amp;file=admin&amp;op=activate_block&amp;bid=$bid&amp;weight=$weight>" . '<img src="images/global/red_dot.gif" border=0 ALT="' . _DEACTIVATE . '">' . '</a>';
	  } 

	// Show order arrows
	// Sneaky lookahead
	if (!isset($result->fields[4])) {
		$nextpos = '';
	} else {
		$nextpos = $result->fields[4];
	}
	$down = "<a href=index.php?mod=Blocks&amp;file=admin&amp;op=increase_weight&amp;bid=$bid&amp;weight=$weight>" . '<img src=images/global/down.gif border=0>' . '</a>';
	$up = "<a href=index.php?mod=Blocks&amp;file=admin&amp;op=decrease_weight&amp;bid=$bid&amp;weight=$weight>" . '<img src=images/global/up.gif border=0>' . '</a>';
	switch($rownum) {
	case 1:
		if ($nextpos != $position) {
			$arrows = '';
		} else {
			$arrows = "$down";
		}
		break;
	case $numrows:
		if ($lastpos != $position) {
			$arrows = '';
		} else {
			$arrows = "$up";
		}
		break;
	default:
		// Sneaky bit of lookahead here...
		if ($result->fields[4] != $position) {
			$arrows = "$up";
		} elseif ($position != $lastpos) {
			$arrows = "$down";
		} else {
			$arrows = "$up $down";
		}
		break;
	}
	$rownum++;
	$lastpos = $position;

	// Position name
	$shift_pos_right='<A HREF="index.php?mod=Blocks&amp;file=admin&amp;op=shift_right&amp;bid='.$bid.'&amp;position='.$position.'"><IMG SRC="images/global/right.gif" WIDTH="7" HEIGHT="11" BORDER="0" ALT="'._SHIFTRIGHT.'"></A>';
	$shift_pos_left='<A HREF="index.php?mod=Blocks&amp;file=admin&amp;op=shift_left&amp;bid='.$bid.'&amp;position='.$position.'"><IMG SRC="images/global/left.gif" WIDTH="7" HEIGHT="11" BORDER="0" ALT="'._SHIFTLEFT.'"></A>';
	switch($position) {
			case 'l':
				$pos = _LEFT.' '.$shift_pos_right;
				break;
			case 'r':
				$pos = $shift_pos_left.'  '._RIGHT;
				break;
			case 'c':
				$pos = $shift_pos_left.' '._CENTER.' '.$shift_pos_right;
				break;
		}

	// Get Module name
	$modinfo = lnModGetInfo($mid);
	$modname = $modinfo['name'];
	
	// show actions
	$actions ="";
	 if (lnSecAuthAction(0, 'Permissions::', "Group::".@$gid, ACCESS_ADD)) {
		$actions .="<A HREF=\"index.php?mod=Blocks&amp;file=admin&amp;op=add_block_form&amp;item=$active_count&amp;weight=$weight\"><IMG SRC=\"images/global/insert.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._INSERT."\"></A> ";
	 }
	 if (lnSecAuthAction(0, 'Permissions::', "Group::".@$gid, ACCESS_EDIT)) {
		$actions .="<A HREF=\"index.php?mod=Blocks&amp;file=admin&amp;op=edit_block_form&amp;item=$active_count\"><IMG SRC=\"images/global/edit.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._EDIT."\"></A> ";
	 }
	 if (lnSecAuthAction(0, 'Permissions::', "Group::".@$gid, ACCESS_DELETE)) {
		 $actions .="<A HREF=\"javascript:if(confirm('Delete $title?')) window.open('index.php?mod=Blocks&amp;file=admin&amp;op=delete_block&amp;bid=$bid','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._DELETE."\"></A>";
	 }


	if (!($op == "edit_block_form" && $active_count == $item)) {
		echo '<tr bgcolor=#FFFFFF align=center><td>'.$arrows.'</td><td>'.$active_icon.'</td><td>'. $pos.'</td><td>'.$title.'</td><td>'.$modname.'</td>';
		if ($modname) {
			echo '<td>&nbsp;</td>';
		}
		else {
			echo "<td><INPUT class=button_org TYPE=button VALUE=Editor onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Blocks&amp;bid=$bid','_blank',750,480)\"></td>";
		}
		echo '<td>'.$actions.'</td></tr>';
	}

	if (($op == "edit_block_form" || $op == "add_block_form") && $active_count == $item) {
		echo '<FORM METHOD=POST ACTION="index.php">'
					.'<INPUT TYPE="hidden" NAME="mod" VALUE="Blocks">'
					.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
					.'<INPUT TYPE="hidden" NAME="op" VALUE="add_block">'
					.'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$weight.'">';

		if ($op == "edit_block_form") {
				echo '<INPUT TYPE="hidden" NAME="action" VALUE="update">';
				echo '<INPUT TYPE="hidden" NAME="bid" VALUE="'.$bid.'">';
		}
		else {
			$arrows="-";
			$active_icon='-';
			$title='';
			$content='';
			$mid=0;
	
			echo '<INPUT TYPE="hidden" NAME="action" VALUE="insert">';			
		}
				
		echo '<tr bgcolor=#AAAAAA align=center valign=top><td>'.$arrows.'</td><td>'.$active_icon.'</td>';
		echo '<td>';

		$sel_position['l'] ='';
		$sel_position['r'] ='';
		$sel_position['c'] ='';
		$sel_position[$position] = ' selected';
		echo '<SELECT NAME="position">';
		echo '<OPTION VALUE="l" '.$sel_position['l'].'>'._LEFT.'</OPTION>';
		echo '<OPTION VALUE="c" '.$sel_position['c'].'>'._CENTER.'</OPTION>';
		echo '<OPTION VALUE="r" '.$sel_position['r'].'>'._RIGHT.'</OPTION>';
		echo '</SELECT>';
		echo '</td>';

		echo '<td><INPUT TYPE="text" NAME="title" size=20 value="'.$title.'"></td>';
		
		echo '<td>';
		echo '<SELECT NAME="binfo">';
		$block_list = lnBlockList();
		echo "<OPTION VALUE=0 selected>-</OPTION>";
		foreach ($block_list as $key => $blockinfo) {	
			if ($blockinfo[mid] == $mid) {
				echo '<OPTION VALUE="'.$blockinfo[mid].'#'.$blockinfo[bkey].'" selected>'.$blockinfo[module].'</OPTION>';
			}
			else if (!lnBlockLoaded($blockinfo[mid])) {
				echo '<OPTION VALUE="'.$blockinfo[mid].'#'.$blockinfo[bkey].'">'.$blockinfo[module].'</OPTION>';
			}
		}

		echo '</SELECT>';
		echo '</td>';
		echo '<td></td>';
		echo '<td>';
		echo '<INPUT class="button_gray" TYPE="submit" VALUE="'._SUBMIT.'"> ';
		echo '<INPUT class="button_gray" TYPE="button" VALUE="'._CANCEL.'" onclick="javascript:window.open(\'index.php?mod=Blocks&amp;file=admin\',\'_self\')">';
		echo '</td></tr>';
		echo '</FORM>';
	}
	
}

echo '</table>';

echo '<DIV ALIGN=LEFT>'
.'<UL TYPE=circle> '
.'<LI><IMG SRC="images/global/insert.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._INSERT.'"> '._INSERT
.'&nbsp;&nbsp;<IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"> '._EDIT
.'&nbsp;&nbsp;<IMG SRC="images/global/delete.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._DELETE.'"> '._DELETE
.'<LI><img src="images/global/green_dot.gif" border=0 ALT="' . _ACTIVATE . '"> ' . _ACTIVATE 
.'&nbsp;&nbsp;<img src="images/global/red_dot.gif" border=0 ALT="' . _DEACTIVATE . '"> ' . _DEACTIVATE
.'</UL></DIV>';


include 'footer.php';
/* - - - - - - - - - - - */


///Funtions/////////////////////////////////////////////////////////////////////////////////
// deactive a user property
function deactivate_block($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['bid'])) {
        $column = &$lntable['blocks_column'];

        $result = $dbconn->Execute("UPDATE $lntable[blocks] SET $column[active]=0
                                    WHERE $column[bid]='" . $var['bid'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate Blocks 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate Blocks 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
	}

    lnRedirect("index.php?mod=Blocks&file=admin");
} 

// active a user property
function activate_block($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['bid'])) {
        $column = &$lntable['blocks_column'];

        $result = $dbconn->Execute("UPDATE $lntable[blocks] SET $column[active]=1
                                    WHERE $column[bid]='" . $var['bid'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate Blocks 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate Blocks 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
	}

    lnRedirect("index.php?mod=Blocks&file=admin");
} 

function increase_weight($var) {
   // Get arguments from argument array
    extract($var);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];

    // Get info on current position of block
    $sql = "SELECT $blockscolumn[weight],
                   $blockscolumn[position]
            FROM $blockstable
            WHERE $blockscolumn[bid]='" . (int)lnVarPrepForStore($bid)."'";
    $result = $dbconn->Execute($sql);
    if ($result->EOF) {
        return false;
    }

    list($seq, $position) = $result->fields;
    $result->Close();

	// Get info on displaced block
    $sql = "SELECT $blockscolumn[bid],
                   $blockscolumn[weight]
            FROM $blockstable
            WHERE $blockscolumn[weight]>'" . lnVarPrepForStore($seq) . "'
            AND   $blockscolumn[position]='" . lnVarPrepForStore($position) . "'
            ORDER BY $blockscolumn[weight] ASC";
    $result = $dbconn->SelectLimit($sql, 1);
   
	if ($result->EOF) {
        return false;
    }
    list($altbid, $altseq) = $result->fields;
    $result->Close();
	
    // Swap sequence numbers
    $sql = "UPDATE $blockstable
            SET $blockscolumn[weight]=$seq
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($altbid)."'";
    $dbconn->Execute($sql);
    $sql = "UPDATE $blockstable
            SET $blockscolumn[weight]=$altseq
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'";
    $dbconn->Execute($sql);

    return true;
}

function decrease_weight($var) {
   // Get arguments from argument array
    extract($var);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];

    // Get info on current position of block
    $sql = "SELECT $blockscolumn[weight],
                   $blockscolumn[position]
            FROM $blockstable
            WHERE $blockscolumn[bid]='" . (int)lnVarPrepForStore($bid)."'";
    $result = $dbconn->Execute($sql);
    if ($result->EOF) {
        return false;
    }

    list($seq, $position) = $result->fields;
    $result->Close();
	
    // Get info on displaced block
    $sql = "SELECT $blockscolumn[bid],
                   $blockscolumn[weight]
            FROM $blockstable
            WHERE $blockscolumn[weight]<'" . lnVarPrepForStore($seq) . "'
            AND   $blockscolumn[position]='" . lnVarPrepForStore($position) . "'
            ORDER BY $blockscolumn[weight] DESC";
    $result = $dbconn->SelectLimit($sql, 1);
   
	if ($result->EOF) {
        return false;
    }
    list($altbid, $altseq) = $result->fields;
    $result->Close();
	
    // Swap sequence numbers
    $sql = "UPDATE $blockstable
            SET $blockscolumn[weight]=$seq
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($altbid)."'";
    $dbconn->Execute($sql);
    $sql = "UPDATE $blockstable
            SET $blockscolumn[weight]=$altseq
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'";
    $dbconn->Execute($sql);

    return true;
}

function shift_left($var) {
   // Get arguments from argument array
    extract($var);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];
	
	if ($position == 'c') {
		$next_pos = 'l';
	}
	else if ($position == 'r') {
		$next_pos = 'c';
	}

	$sql = "UPDATE $blockstable
            SET $blockscolumn[position]='".lnVarPrepForStore($next_pos)."' 
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'";
    $result = $dbconn->Execute($sql);

	resequenceBlocks($position);
	resequenceBlocks($next_pos);
}

function shift_right($var) {
   // Get arguments from argument array
    extract($var);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];
	
	if ($position == 'c') {
		$next_pos = 'r';
	}
	else if ($position == 'l') {
		$next_pos = 'c';
	}

	$sql = "UPDATE $blockstable
            SET $blockscolumn[position]='".lnVarPrepForStore($next_pos)."' 
            WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'";
    $result = $dbconn->Execute($sql);
	
	resequenceBlocks($position);
	resequenceBlocks($next_pos);
}

function delete_block($var) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];
	
	if (!empty($var['bid'])) {
		$query = "DELETE FROM $blockstable
				  WHERE $blockscolumn[bid] = '" . lnVarPrepForStore($var['bid']) . "'";
		$dbconn->Execute($query);

		if ($dbconn->ErrorNo() != 0) {
			return false;
		} 
		resequenceBlocksAll();
	}		

     lnRedirect("index.php?mod=Blocks&file=admin");
} 

function add_block($var) {

   // Get arguments from argument array
    extract($var);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];

	list($mid,$bkey)=explode("#",$binfo);

	if ($action == "insert") {
		$weight += 0.5;
		$query = "INSERT INTO $blockstable
					  ($blockscolumn[bkey],
					   $blockscolumn[title],
					   $blockscolumn[content],
					   $blockscolumn[mid],
					   $blockscolumn[position],
					   $blockscolumn[weight],
					   $blockscolumn[active],
						$blockscolumn[last_update])
						VALUES ('" . lnVarPrepForStore($bkey) . "',
							  '" . lnVarPrepForStore($title) . "','',
							  '" . lnVarPrepForStore($mid) . "',
							  '" . lnVarPrepForStore($position) . "',
							 '" . lnVarPrepForStore($weight) . "',
							  '1',unix_timestamp( ))";
	}
	else if ($action == "update") {
		$query = "UPDATE  $blockstable SET
					   $blockscolumn[bkey]='" . lnVarPrepForStore($bkey) . "',
					   $blockscolumn[position]='" . lnVarPrepForStore($position) . "',
					   $blockscolumn[title]='" . lnVarPrepForStore($title) . "',
					   $blockscolumn[mid]='" . lnVarPrepForStore($mid) . "'
					   WHERE $blockscolumn[bid]='" . lnVarPrepForStore($bid) . "'";		  
	}
	
    if (!empty($action)) {
		$dbconn->Execute($query);
	}

    if ($dbconn->ErrorNo() != 0) {
        return false;
    } 

	resequenceBlocksAll();

    return true;
} 

function resequenceBlocksAll() {
		resequenceBlocks('l');
		resequenceBlocks('c');
		resequenceBlocks('r');
}

function resequenceBlocks($position) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];
	
    // Get the information
    $query = "SELECT $blockscolumn[bid],
                     $blockscolumn[weight]
					 FROM $blockstable 
					 WHERE $blockscolumn[position]= '". lnVarPrepForStore($position)."'
               ORDER BY $blockscolumn[weight]";
    $result = $dbconn->Execute($query);

    // Fix sequence numbers
    $seq=1;
    while(list($bid, $curseq) = $result->fields) {

        $result->MoveNext();
        if ($curseq != $seq) {
            $query = "UPDATE $blockstable
                      SET $blockscolumn[weight]='" . lnVarPrepForStore($seq) . "'
                      WHERE $blockscolumn[bid]='" . lnVarPrepForStore($bid)."'";
            $dbconn->Execute($query);
        }
        $seq++;
    }
    $result->Close();

    return true;
}
?>