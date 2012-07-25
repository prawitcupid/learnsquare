<?php
/** 
 * change the function name so themes remain compatable 
 */
function blocks($side)
{
    global $blocks_modules, $blocks_side;

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $currentlang = lnUserGetLang();

    $side = strtolower($side[0]);
    $blocks_side = $side;
    $column = &$lntable['blocks_column'];
	$modulescolumn=&$lntable['modules_column'];
	$query ="SELECT $column[bid] as bid, $column[bkey] as bkey, $column[mid] as mid, $column[title] as title, $column[content] as content, $column[position] as position, $column[weight] as weight, $column[active] as active, $modulescolumn[state] as modstate";
	$query .=" FROM $lntable[blocks] LEFT JOIN $lntable[modules]  ON  $modulescolumn[id]=$column[mid] and $modulescolumn[state] = "._LNMODULE_STATE_ACTIVE;
	$query .=" WHERE  $column[position]='".lnVarPrepForStore($side)."' AND $column[active]=1 ORDER BY $column[weight]";

	$result = $dbconn->Execute($query);


	if ($result->EOF) 
		echo "&nbsp;";

    while(!$result->EOF) {
        $row = $result->GetRowAssoc(false);
		if (!(!empty($row['bkey']) && $row['modstate'] != _LNMODULE_STATE_ACTIVE)) {
			$modinfo = lnModGetInfo($row['mid']);
			if (!$modinfo) {
				$modinfo['name'] = 'Core';             // Assume core
			}
			echo lnBlockShow($modinfo['name'], $row['bkey'], $row); ///////////////////////ด้านขวามือ
	
		}
        $result->MoveNext();
    }
}

/**
 * show a block
 * @param the module name
 * @param the name of the block
 * @param block information parameters
 */
function lnBlockShow($modname, $block, $blockinfo=array())
{
    global $blocks_modules;
	
    lnBlockLoad($modname, $block);
    $displayfunc = "blocks_{$block}_block";
	if (function_exists($displayfunc)) {
        return $displayfunc($blockinfo);
    }
	else {
		$blockinfo['content'] = lnShowContent($blockinfo['content'],LN_BLOCK_IMAGE_DIR);
		//$blockinfo['content'] = $blockinfo['content'];
		return themesidebox($blockinfo);
		
	}
}

/**
 * load a block
 * @param the module name
 * @param the name of the block
 */
function lnBlockLoad($modname, $block)
{
    global $blocks_modules;

    static $loaded = array();

    if (isset($loaded["$modname$block"])) {
        return true;
    }
    if ((empty($modname)) || ($modname == 'Core')) {
        $modname = 'Core';
        $moddir = 'includes/blocks';
        $langdir = 'includes/language/blocks';
    } else {
        $modinfo = lnModGetInfo(lnModGetIdFromName($modname));
        $moddir = 'modules/' . lnVarPrepForOS($modinfo['directory']) . '/blocks';
        $langdir = 'modules/' . lnVarPrepForOS($modinfo['directory']) . '/language';
    }

    // Load the block
    $incfile = $block . ".php";;
    $filepath = $moddir . '/' . lnVarPrepForOS($incfile);
//	echo $filepath;
	if (!file_exists($filepath)) {
        return false;
    }
    include_once $filepath;
    $loaded["$modname$block"] = 1;

    // Load the block language files
    $currentlangfile = $langdir . '/' . lnVarPrepForOS(lnUserGetLang()) . '/' . lnVarPrepForOS($incfile);
    $defaultlangfile = $langdir . '/' . lnVarPrepForOS(lnConfigGetVar('language')) . '/' . lnVarPrepForOS($incfile);
    if (file_exists($currentlangfile)) {
        include $currentlangfile;
    } elseif (file_exists($defaultlangfile)) {
        include "$defaultlangfile";
    }


    return true;
}

/**
 * load all blocks
 */
function lnBlockList(){
    global $blocks_modules;

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];
    $sql = "SELECT $modulescolumn[name],
                   $modulescolumn[directory],
                   $modulescolumn[id]
            FROM $modulestable WHERE $modulescolumn[state]="._LNMODULE_STATE_ACTIVE."
			ORDER BY $modulescolumn[name]";

    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    while (list($name, $directory, $mid) = $result->fields) {
        $result->MoveNext();

        $blockdir = 'modules/' . lnVarPrepForOS($directory) . '/blocks';
        if (!@is_dir($blockdir)) {
            continue;
        }
        $dib = opendir($blockdir);
        while($f = readdir($dib)) {
            if (preg_match('/\.php$/', $f)) {
                $block= preg_replace('/\.php$/', '', $f);
                // Get info on the block
				$blocks_modules["$name"]['module'] = $name;
				$blocks_modules["$name"]['bkey'] = $block;
				$blocks_modules["$name"]['mid'] = $mid;
            }
        }
    }
    $result->Close();
   
	// Return information gathered
    return $blocks_modules;
}

function lnBlockLoaded($mid){

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $blockstable = $lntable['blocks'];
    $blockscolumn = &$lntable['blocks_column'];
    $sql = "SELECT $blockscolumn[bid]
            FROM $blockstable WHERE $blockscolumn[mid]='".$mid."'";

    $result = $dbconn->Execute($sql);

	if ($dbconn->ErrorNo() != 0) {
        return;
    }
	
	$numrows = $result->PO_RecordCount();
	 
	 if ($numrows) {
		 return true;
	 }
	 else {
		 return false;
	 }
}

// show icon at user module
function lnBlockImage($mod,$image) {
	$thistheme = lnConfigGetVar('Default_Theme');
	
	// Check images at themes first
	$file1 = "themes/$thistheme/images/$mod/$image.";

	// if not found using module images
	$file2 = "modules/$mod/images/$image.";

	$val = lnBlockCheckImage($file1,$file2,'');

	return $val;
}

// show image title
function lnBlockTitle($mod,$ext='') {
	$thistheme = lnConfigGetVar('Default_Theme');
	$lang = lnConfigGetVar('language');
	
	if (empty($ext)) {
		$title = "title.";
	}
	else {
		$title = "title_$ext.";
	}

	// Check images at themes first
 $file1 = "themes/$thistheme/images/$mod/$lang/$title";

	// if not found using module images
	$file2 = "modules/$mod/images/$lang/$title";

	$val = lnBlockCheckImage($file1,$file2,$mod);
	return $val;
}

// show image admin at control panel
function lnBlockAdmin($mod) {
	$thistheme = lnConfigGetVar('Default_Theme');

	// Check images at themes first
	$file1 = "themes/$thistheme/images/$mod/admin.";

	// if not found using module images
	$file2 = "modules/$mod/images/admin.";
	
	$val = lnBlockCheckImage($file1,$file2,$mod);
	
	return $val;
}

// show image button
function lnBlockButton($button) {
	$thistheme = lnConfigGetVar('Default_Theme');
	$lang = lnConfigGetVar('language');

	$file1 = "themes/$thistheme/images/button/$lang/$button.";
	$file2 = "images/button/$lang/$button.";

	$val = lnBlockCheckImage($file1,$file2,$button);
	echo $val;
}

function lnBlockCheckImage($file1,$file2,$text) {
	// Check images at themes first
	if (file_exists($file1.'gif'))
		$imgfile = $file1.'gif';
	elseif (file_exists($file1.'jpg'))
		$imgfile = $file1.'jpg';
	elseif (file_exists($file1.'png'))
		$imgfile = $file1.'png';

	// if not found using module images
	if ( empty($imgfile) && file_exists($file2.'gif'))
		$imgfile = $file2.'gif';
	elseif (empty($imgfile) && file_exists($file2.'jpg'))
		$imgfile = $file2.'jpg';
	elseif (empty($imgfile) && file_exists($file2.'png'))
		$imgfile = $file2.'png';

	if (!empty($imgfile)) {
		$ret = '<img src='.$imgfile.' border=0>';
	}
	else{
		$ret = "<B>$text</B>";
	}

	return $ret;
}

function lnBlockNav($menus,$links) {
	$list = array();
	echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER="0" ALT=""> ';
	for ($i=0; $i<count($menus); $i++) {
		$menus[$i]=stripslashes($menus[$i]);
		if ($i == count($menus)-1) {
			$list[] =  '<B><A HREF="'.$links[$i].'">'.$menus[$i].'</A></B>';
		}
		else {
			$list[] =  '<A HREF="'.$links[$i].'">'.$menus[$i].'</A>';
		}
	}
	$lists = join('&nbsp;&gt;&nbsp;',$list);

	echo $lists.'<BR>&nbsp;';
}

?>