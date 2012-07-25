<?php
if (preg_match("/lnMod.php/i",$_SERVER['PHP_SELF'])) {
   die ("You can't access this file directly...");
}
/**
 * see if a module is available
 * @returns bool
 * @return true if the module is available, false if not
 */
function lnModAvailable($modname)
{
    if (empty($modname)) {
        return false;
    }

    static $modstate = array();
    if (isset($modstate[$modname])) {
        if ($modstate[$modname] == _LNMODULE_STATE_ACTIVE) {
            return true;
        } else {
            return false;
        }
    }

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];
    $query = "SELECT $modulescolumn[state]
              FROM $modulestable
              WHERE $modulescolumn[name] = '" . lnVarPrepForStore($modname) . "'";
	

    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        $modstate[$modname] = _LNMODULE_STATE_MISSING;
        return false;
    }

    list($state) = $result->fields;
    $result->Close();

    $modstate[$modname] = $state;
    if ($state == _LNMODULE_STATE_ACTIVE) {
        return true;
    } else {
        return false;
    }
}

/**
 * load a module
 * @param name - name of module to load
 * @param type - type of functions to load
 * @returns string
 * @return name of module loaded, or false on failure
 */
function lnModLoad($modname,$file)
{
    static $loaded = array();

	if (empty($modname)) {
        return false;
    }
	
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];

    if (!empty($loaded["$modname"])) {
        // Already loaded from somewhere else
        return $modname;
    }

    $query = "SELECT $modulescolumn[directory],
                     $modulescolumn[state]
              FROM $modulestable
              WHERE $modulescolumn[name] = '" . lnVarPrepForStore($modname) . "'";

    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }

    list($directory, $state) = $result->fields;
    $result->Close();

    // Load the module and module language files
    $osfile = "modules/$directory/$file.php";

	if (!file_exists($osfile)) {
        // File does not exist
        return false;
    }

    $loaded["$modname"] = 1;

    $defaultlang = lnConfigGetVar('language');
    if (empty($defaultlang)) {
        $defaultlang = 'tha';
    }


    $currentlang = lnUserGetLang();
    if (file_exists("modules/$directory/language/$currentlang/global.php")) {
        include "modules/$directory/language/" . lnVarPrepForOS($currentlang) . "/global.php";
    } 
	elseif (file_exists("modules/$directory/language/$defaultlang/global.php")) {
        include "modules/$directory/language/" . lnVarPrepForOS($defaultlang) . "/global.php";
    }

 // Load datbase info for new module
    lnModDBInfoLoad($modname, $directory);
	
	// Return the module 
    return $osfile;

}

/**
 * load datbase definition for a module
 * @param name - name of module to load database definition for
 * @param directory - directory that module is in (if known)
 * @returns bool
 */
function lnModDBInfoLoad($modname, $directory='')
{
    static $loaded = array();

    // Check to ensure we aren't doing this twice
    if (isset($loaded[$modname])) {
        return true;
    }

    // Get the directory if we don't already have it
    if (empty($directory)) {
        list($dbconn) = lnDBGetConn();
        $lntable = lnDBGetTables();
        $modulestable = $lntable['modules'];
        $modulescolumn = &$lntable['modules_column'];
        $sql = "SELECT $modulescolumn[directory]
                FROM $modulestable
                WHERE $modulescolumn[name] = '" . lnVarPrepForStore($modname) . "'";
        $result = $dbconn->Execute($sql);
		echo $sql;
        if($dbconn->ErrorNo() != 0) {
            return;
        }

        if ($result->EOF) {
            return false;
        }

        $directory = $result->fields[0];
        $result->Close();
    }

    // Load the database definition if required
    $ospntablefile = 'modules/' . lnVarPrepForOS($directory) . '/lntables.php';

	// Ignore errors for this, if it fails we'll find out and handle
    // it when we look for the function itself
	
	//echo "<pre>";
    //@include_once $ospntablefile;
	//echo $ospntablefile."\n";
    //$tablefunc = $modname . '_' . 'lntables';
	//echo "::" . $tablefunc . "::\n";
	//if (function_exists($tablefunc)) {
     //   global $lntable;
      //  $lntable = array_merge($lntable, $tablefunc());
		
	//	echo "??????????????????????????????????????????????????????????????????????????????????????????????????????";
	//	print_r($lntable);
	//	echo "</pre>";
    //}
    $loaded[$modname] = true;

    return true;
}

/**
 * get information on module
 * @param id
 * @returns array
 * @ return array of module information or false if core ( id = 0 )
 */
function lnModGetInfo($modid)
{
    // a $modid of 0 is associated with core ( ln_blocks.mid, ... ).
    if ( $modid == 0 ) {
        return false;
    }

    static $modinfo = array();
    if (isset($modinfo[$modid])) {
        return $modinfo[$modid];
    }

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];
    $query = "SELECT $modulescolumn[name],
                     $modulescolumn[type],
                     $modulescolumn[directory],
                     $modulescolumn[displayname],
                     $modulescolumn[description],
                     $modulescolumn[version]
              FROM $modulestable
              WHERE $modulescolumn[id] = " . lnVarPrepForStore($modid);
    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        $modinfo[$modid] = false;
        return false;
    }

    list($resarray['name'],
         $resarray['type'],
         $resarray['directory'],
         $resarray['displayname'],
         $resarray['description'],
         $resarray['version']) = $result->fields;
    $result->Close();

    $modinfo[$modid] = $resarray;
    return $resarray;
}

/*
 * lnModGetIDFromName - get module ID given its name
 * Takes one parameter:
 * - the name of the module
 */
function lnModGetIDFromName($module)
{
    if (empty($module)) {
        return false;
    }

    static $modid = array();
    if (isset($modid[$module])) {
        return $modid[$module];
    }

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];
    $query = "SELECT $modulescolumn[id]
              FROM $modulestable
              WHERE $modulescolumn[name] = '" . lnVarPrepForStore($module) . "'";
    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        $modid[$module] = false;
        return false;
    }

    list($id) = $result->fields;
    $result->Close();

    $modid[$module] = $id;
    return $id;
}

/**
 * get list of administration modules
 * @returns array
 * @return array of module information arrays
 */
function lnModGetAdminMods()
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];

    $query = "SELECT $modulescolumn[name],
                     $modulescolumn[type],
                     $modulescolumn[directory],
                     $modulescolumn[displayname],
                     $modulescolumn[description],
                     $modulescolumn[version]
              FROM $modulestable
              WHERE $modulescolumn[state] = " . _LNMODULE_STATE_ACTIVE . "
              AND $modulescolumn[admin_capable] = 1
              AND $modulescolumn[directory] != 'Admin'
              ORDER BY $modulescolumn[name]";

    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return false;
    }

    $resarray = array();
    while(list($name,
               $modtype,
               $directory,
               $displayname,
               $description,
               $version) = $result->fields) {
        $result->MoveNext();

        $tmparray = array('name' => $name,
                          'type' => $modtype,
                          'directory' => $directory,
                          'displayname' => $displayname,
                          'description' => $description,
                          'version' => $version);

        array_push($resarray, $tmparray);
    }
    $result->Close();

    return $resarray;
}

?>