<?php
/**
*  Config dynamic user properties
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}

/* options */
if ($op) {
	$vars= array_merge($_GET,$_POST);
	switch($op) {
		case "deactivate_property": 
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				deactivate_property($vars); break;
		case "activate_property":
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				activate_property($vars); break;
		case "increase_weight":
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				increase_weight($vars); break;
		case "decrease_weight":
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
					return false;
				}
				decrease_weight($vars); break;
		case "delete_property":
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_DELETE)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to delete ".$mod." module!</h1></CENTER>";
					return false;
				}
				delete_property($vars); break;
		case "add_property":
				if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_ADD)) {
					echo "<CENTER><h1>"._NOAUTHORIZED." to add ".$mod." module!</h1></CENTER>";
					return false;
				}
				add_property($vars); break;
	}
}


/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USERDYCONFIG);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&amp;file=userdyconfig');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=User&amp;file=userdyconfig"><B>'._USERDYCONFIG.'</B></A><BR>&nbsp;';
	 
 echo '<table class="list" cellpadding="3" cellspacing="1" border="0" width="100%">'
 .'<tr align="center">'
 .'<td class="head">'._ACTIVE.'</td>'.'<td colspan=2  class="head">'._FLABEL.'</td>'.'<td  class="head">'._WEIGHT.'</td>'.'<td  class="head">'._DTYPE.'</td>'.'<td class="head">'._LENGTH.'</td>'.'<td class="head">'._DELETE.'</td></tr>';


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$table = $lntable['user_property'];
	$column = &$lntable['user_property_column'];

	$result = $dbconn->Execute("SELECT $column[prop_id], $column[prop_label],$column[prop_dtype],
                              $column[prop_length], $column[prop_weight], $column[prop_validation]
                              FROM $table ORDER BY $column[prop_weight]");
    
	if ($dbconn->ErrorNo() <> 0) {
        echo $dbconn->ErrorNo() . "List User Properties: " . $dbconn->ErrorMsg() . "<br>";
        error_log ($dbconn->ErrorNo() . "List User Properties: " . $dbconn->ErrorMsg() . "<br>");
        return;
    } 
    $active_count = 0;
    $true_count = 0;
    $total_count = $result->PO_RecordCount();
    $prop_weight = 0;
    while (list($prop_id, $prop_label, $prop_dtype, $prop_length, $prop_weight, $prop_validation) = $result->fields) {
        $result->MoveNext();

		$true_count++;
        if ($prop_weight <> 0) {
            $active_count++;
            $next_prop_weight = $active_count + 1;
        } 

	   echo '<tr bgcolor=#FFFFFF>';
	   echo '<td align=center>';

		switch (true) {
				// Mandatory Images can't be disabled (-1)
				case ($prop_dtype == -1):
					$img_cmd = '<img src="images/global/green_dot.gif" border=0 ALT="' . _FIELD_REQUIRED . '">';
					break;
				case ($prop_weight <> 0):
					$img_cmd = "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=deactivate_property&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src="images/global/green_dot.gif" border=0 ALT="' . _FIELD_DEACTIVATE . '">' . '</a>';
					break;
				default:
					$img_cmd = "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=activate_property&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src="images/global/red_dot.gif" border=0 ALT="' . _FIELD_ACTIVATE . '">' . '</a>';
	   } 

	  echo $img_cmd.'</td>';
	  echo '<td>'.$prop_label.'</td>';
	  $eval_cmd = "\$prop_label_text=$prop_label;";
	@eval($eval_cmd); 
	  echo '<td>'.$prop_label_text.'</td>';

	    switch (true) {
            case ($active_count == 0):
                $arrows = "&nbsp";
                break;
            case ($active_count == 1):
                $arrows = "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=increase_weight&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src=images/global/down.gif border=0>' . '</a>';
                break;
            case ($true_count == $total_count):
                $arrows = "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=decrease_weight&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src=images/global/up.gif border=0>' . '</a>';
                break;
            default:
//                $arrows = '<img src=images/global/up.gif>&nbsp;<img src=images/global/down.gif>';
                $arrows = "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=decrease_weight&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src=images/global/up.gif border=0>' . '</a>&nbsp;' . "<a href=index.php?mod=User&amp;file=userdyconfig&amp;op=increase_weight&amp;property=$prop_id&amp;weight=$prop_weight>" . '<img src=images/global/down.gif border=0>' . '</a>';
        } 

	  echo '<td align=center>'.$arrows.'</td>';

	     switch ($prop_dtype) {
            case -1:
                $data_type_text = 'Core Required';
                $data_length_text = 'N/A';
                break;
            case 0:
                $data_type_text = 'Core';
                $data_length_text = 'N/A';
                break;
            case 1:
                $data_type_text = 'String';
                $data_length_text = $prop_length;
                break;
            case 2:
                $data_type_text = 'text';
                $data_length_text = 'N/A';
                break;
            case 3:
                $data_type_text = 'float';
                $data_length_text = 'N/A';
                break;
            case 4:
                $data_type_text = 'integer';
                $data_length_text = 'N/A';
                break;
            default:
                $data_length_text = "";
                $data_type_text = "";
        } 
	  echo '<td>'.$data_type_text.'</td>';
	  echo '<td>'.$data_length_text.'</td>';

	   if (($prop_dtype == -1) || ($prop_dtype == 0)) {
            $del_text = 'N/A';
        } else {
            $del_text = "<a href=\"javascript:if(confirm('Delete $prop_label ?')) window.open('index.php?mod=User&amp;file=userdyconfig&amp;op=delete_property&amp;property=$prop_id','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\"". _DELETE ."\"></a>";
        } 

	   echo '<td align=center>'.$del_text.'</td>';
	   echo '</tr>';
	 }	   
	echo '</table>';

	print "<br>";

	echo  '<center><BR>'
	. '<table cellpadding="3" cellspacing="1" border=0 bgcolor="#d3d3d3">'
	. '<form action="index.php" method="post">'
	. '<input type="hidden" name="mod" value="User">'
	. '<input type="hidden" name="file" value="userdyconfig">'
	. '<input type="hidden" name="op" value="add_property">'
	. '<input type="hidden" name="prop_weight" value="' . $next_prop_weight . '">'
	.'<tr><td colspan=2 class="head" align=center bgcolor=808080>' . _ADDFIELD . '</td></tr>'
	. '<tr><td>'
	._FIELDLABEL.'</td><td>'.'<input type="text" name="label" value="" size="20" maxlength="20">' . '&nbsp;' . _ADDINSTRUCTIONS
	.'</td></tr><tr><td>'
	._FIELDTYPE.'</td><td>' . '<select name="dtype">' . '<option value="1">' . _UDT_STRING . '</option>' . "\n" . '<option value="2">' . _UDT_TEXT . '</option>' . "\n" . '<option value="3">' . _UDT_FLOAT . '</option>' . "\n" . '<option value="4">' . _UDT_INTEGER . '</option>' . "\n" . '</select>'
	.'</td></tr><tr><td>'
	._FIELDLENGTH.'</td><td>'.'<input type="text" name="prop_len" value="" size="3" maxlength="3">' . '&nbsp;' . _STRING_INSTRUCTIONS
	.'</td></tr><tr><td>'
	.'&nbsp;</td><td><BR><INPUT CLASS="button_org" TYPE="submit" value="'._ADDFIELD.'"><BR>&nbsp;</td></tr>'
	. '</table></center></form>';

	CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */


// deactive a user property
function deactivate_property($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['property'])) {
        $column = &$lntable['user_property_column'];

        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=0
                                    WHERE $column[prop_id]='" . $var['property'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate User Property 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User Property 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=$column[prop_weight]-1
                                    WHERE $column[prop_weight]>'" . $var['weight'] . "'");

        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate User Property 2: " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User Property 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 
    lnRedirect("index.php?mod=User&file=userdyconfig");
} 

// active a user property
function activate_property ($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['property'])) {
        $max_weight = 0;
        $column = &$lntable['user_property_column'];
        $result = $dbconn->Execute("SELECT MAX($column[prop_weight]) max_weight FROM $lntable[user_property]");

        if (!$result->EOF) {
            list($max_weight) = $result->fields;
        } 
        $max_weight++;
        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=" . lnVarPrepForStore($max_weight) . "
                                    WHERE $column[prop_id]='" . $var['property'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Activate User Property 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Activate User Property 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 
    lnRedirect("index.php?mod=User&file=userdyconfig");
} 

function increase_weight($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['property']) && !empty($var['weight'])) {
        $new_weight = $var['weight'] + 1.5;
        $column = &$lntable['user_property_column'];
        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=" . lnVarPrepForStore($new_weight) . "
                                    WHERE $column[prop_id]='" . $var['property'] . "' AND $column[prop_weight]='" . $var['weight'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase Weight 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase Weight 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=" . $var['weight'] . "
                                    WHERE $column[prop_id]<>'" . $var['property'] . "' AND $column[prop_weight]='" . lnVarPrepForStore($new_weight) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase Weight 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase Weight 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 
	resequenceUserProperty();
    lnRedirect("index.php?mod=User&file=userdyconfig");
} 

function decrease_weight($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['property']) && !empty($var['weight'])) {
        $new_weight = $var['weight'] - 1.5;
        $column = &$lntable['user_property_column'];
        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=" . lnVarPrepForStore($new_weight) . "
                                    WHERE $column[prop_id]='" . $var['property'] . "' AND $column[prop_weight]='" . $var['weight'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease Weight 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease Weight 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[user_property] SET $column[prop_weight]=" . $var['weight'] . "
                                    WHERE $column[prop_id]<>'" . $var['property'] . "' AND $column[prop_weight]='" . lnVarPrepForStore($new_weight) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease Weight 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease Weight 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        }
    } 
	resequenceUserProperty();
    lnRedirect("index.php?mod=User&file=userdyconfig");
} 


function delete_property($var)
{
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $propertiestable = $lntable['user_property'];
    $propcolumns = &$lntable['user_property_column'];
    $datatable = $lntable['user_data'];
    $datacolumns = &$lntable['userdata_column']; 

	$result = $dbconn->Execute("SELECT $propcolumns[prop_id], $propcolumns[prop_label], $propcolumns[prop_weight]
           FROM $propertiestable
           WHERE $propcolumns[prop_id] = '$var[property]'");

     if (!$result->EOF) {
        list($pid, $name, $pweight) = $result->fields;
    } 
	else {
       return false;
    } 

	// Prevent deletion of core fields (duh)
    if (empty($name) || ($name == '_UID') || ($name == '_EMAIL') ||
            ($name == '_PASSWORD') || ($name == '_UNAME')) {
        return false;
    } 

    // get property id for cascading delete later
    $query = "SELECT $propcolumns[prop_id] FROM $propertiestable
              WHERE $propcolumns[prop_label] = '" . lnVarPrepForStore($name) . "'";
    $result = $dbconn->Execute($query);

    if ($result->PO_RecordCount() == 0) {
        return false;
    } 

    list ($id) = $result->fields; 
    // Remove variable from properties
    $query = "DELETE FROM $propertiestable
              WHERE $propcolumns[prop_label] = '" . lnVarPrepForStore($name) . "'";
    $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
    // Remove variable from user data
    $query = "DELETE FROM $datatable
              WHERE $datacolumns[uda_propid] = '" . lnVarPrepForStore($pid) . "'";
    $dbconn->Execute($query); 
    

     lnRedirect("index.php?mod=User&file=userdyconfig");
} 

function add_property($var)
{
	$name=$var[label];
	$type=$var[dtype];
	$length=$var[prop_len];
	$weight=$var[prop_weight];

	 // Prevent bogus entries
    if (empty($name) || ($name == 'uid') || ($name == 'email') ||
            ($name == 'password') || ($name == 'uname')) {
        return false;
    } 

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $propertiestable = $lntable['user_property'];
    $columns = &$lntable['user_property_column'];

    // Don't want duplicates either
    $query = "SELECT $columns[prop_label] FROM $propertiestable
              WHERE $columns[prop_label] = '" . lnVarPrepForStore($name) . "'";
    $result = $dbconn->Execute($query);

    if ($result->PO_RecordCount() != 0) {
        return false;
    } 
    // datatype checks
    if (($type != "1") && ($type != "2") && ($type != "3") && ($type != "4")) {
        return false;
    } 
    // further checks
    if (($type == "1") && (!is_numeric($length) || ($length <= 0))) {
        return false;
    } 

	if ($length > 255) {
		$length = 255;
	}

    if (!is_numeric($weight)) {
        return false;
    } 

    $query = "INSERT INTO $propertiestable
                  ($columns[prop_label],
                   $columns[prop_dtype],
                   $columns[prop_length],
                   $columns[prop_weight],
                   $columns[prop_validation])
                  VALUES ('" . lnVarPrepForStore($name) . "',
                          '" . lnVarPrepForStore($type) . "',
                          '" . lnVarPrepForStore($length) . "',
                          '" . lnVarPrepForStore($weight) . "',
                          '" . lnVarPrepForStore($validation) . "')";
    $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    } 

    return true;
} 

function resequenceUserProperty() {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $user_propertytable = $lntable['user_property'];
    $user_propertycolumn = &$lntable['user_property_column'];
	
    // Get the information
    $query = "SELECT  $user_propertycolumn[prop_id],
                      $user_propertycolumn[prop_weight]
					 FROM  $user_propertytable WHERE  $user_propertycolumn[prop_weight] <> '0' 
					ORDER BY  $user_propertycolumn[prop_weight]";
    $result = $dbconn->Execute($query);

// Fix sequence numbers
    $seq=1;
    while(list($id, $curseq) = $result->fields) {
        $result->MoveNext();
            $query = "UPDATE $user_propertytable
                      SET $user_propertycolumn[prop_weight]='" . lnVarPrepForStore($seq) . "'
                      WHERE $user_propertycolumn[prop_id]='" . lnVarPrepForStore($id)."'";
            $dbconn->Execute($query);
        $seq++;
    }

   $result->Close();

    return true;
}

?>