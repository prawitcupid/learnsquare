<?php
/**
*  Upgrade
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'config.php';

	if ($config['encoded']) {
        $config['dbuname'] = base64_decode($config['dbuname']);
        $config['dbpass'] = base64_decode($config['dbpass']);
        $config['encoded'] = 0;
    }

	$dbtype = $config['dbtype'];
    $dbhost = $config['dbhost'];
    $dbname = $config['dbname'];
    $dbuname = $config['dbuname'];
    $dbpass = $config['dbpass'];

	$conn = mysql_connect($dbhost, $dbuname, $dbpass) or die ('Error connecting to mysql');
	mysql_select_db($dbname);

	mysql_select_db($db);

	$error = false;

	$out = "";

	// get auto_increment values and names of all tables
        $res=mysql_query("show table status");
        $all_tables=array();

        while($row=mysql_fetch_array($res)) $all_tables[]=$row;

		$result = mysql_query("show create table `ln_users`");
		$tmp=mysql_fetch_array($result);
        $table_sql[$table['Name']]=$tmp["Create Table"];

        // get table structures
        foreach ($all_tables as $table) {
            $res1=mysql_query("SHOW CREATE TABLE `".$table['Name']."`");
            $tmp=mysql_fetch_array($res1);
            $table_sql[$table['Name']]=$tmp["Create Table"];
        }

	   // find foreign keys
	   $pp = 1;
        $fks=array();
        if (isset($table_sql)) {
            foreach($table_sql as $tablenme=>$table) {
                $tmp_table=$table;
                 // save all tables, needed for creating this table in $fks
                while (($ref_pos=strpos($tmp_table," REFERENCES "))>0) {
                    $tmp_table=substr($tmp_table,$ref_pos+12);
                    $ref_pos=strpos($tmp_table,"(");
                    $fks[$tablenme][]=substr($tmp_table,0,$ref_pos);
                }
            }
        }


		// order $all_tables
        $all_tables=order_sql_tables($all_tables,$fks);

		// as long as no error occurred
        if (!$error) {
            foreach ($all_tables as $row) {
                $tablename=$row['Name'];	
				 // export tables
					
                    $out.="### structure of table `".$tablename."` ###\n\n";
                    if ($drop) $out.="DROP TABLE IF EXISTS `".$tablename."`;\n\n";
                    $out.=$table_sql[$tablename];
                    $out.=" ;";
					$out.="\n\n\n";

                // export data
                if (!$error) {
                    $out.="### data of table `".$tablename."` ###\n\n";

                    // check if field types are NULL or NOT NULL
                    $res3=mysql_query("show columns from `".$tablename."`");

                    $res2=mysql_query("select * from `".$tablename."`");
                    for ($j=0;$j<mysql_num_rows($res2);$j++){
                        $out .= "insert into `".$tablename."` values (";
                        $row2=mysql_fetch_row($res2);
                        // run through each field
                        for ($k=0;$k<$nf=mysql_num_fields($res2);$k++) {
                            // identify null values and save them as null instead of ''
                            if (is_null($row2[$k])) $out .="null"; else $out .="'".mysql_escape_string($row2[$k])."'";
                            if ($k<($nf-1)) $out .=", ";
                        }
                        $out .=");\n";
                    }
					$out .="\n";
				}
			}
		}
				


					//save all structure and data to backupfile
					$backupfile = "modules/Upgrade/backup/db/".$dbname."_".date("y-m-d")."_".date("h-i-s").".sql";
					if(save_to_file($backupfile,$out,"w"))
					{
						echo "Backup Complete file name = $backupfile<br>";

					}


function order_sql_tables($tables,$fks) {
    // do not order if no contraints exist
    if (!count($fks)) return $tables;

    // order
    $new_tables=array();
    $existing=array();
    $modified=TRUE;
    while(count($tables) && $modified==TRUE) {
        $modified=FALSE;
        foreach($tables as $key=>$row) {
            // delete from $tables and add to $new_tables
            if (isset($fks[$row['Name']])) {
                foreach($fks[$row['Name']] as $needed) {
                    // go to next table if not all needed tables exist in $existing
                    if(!in_array($needed,$existing)) continue 2;
                }
            }
            
            // delete from $tables and add to $new_tables
            $existing[]=$row['Name'];
            $new_tables[]=$row;
            prev($tables);
            unset($tables[$key]);
            $modified=TRUE;
        }
    }

    if (count($tables)) {
        // probably there are 'circles' in the constraints, because of that no proper backups can be created
        // This will be fixed sometime later through using 'alter table' commands to add the constraints after generating the tables.
        // Until now I just add the lasting tables to $new_tables, return them and print a warning
        foreach($tables as $row) $new_tables[]=$row;
        echo "<div class=\"red_left\">THIS DATABASE SEEMS TO CONTAIN 'RING CONSTRAINTS'. pMBP DOES NOT SUPPORT THEM. PROBABLY THE FOLLOWING BACKUP IS BROKEN!</div>";
    }
    return $new_tables;
}

	


// returns list of databases on $host host using $user user and $passwd password
function get_db_list() {
    global $CONF;

    // if there is given the name of a single database
    if ($dbname) {
        @mysql_connect($dbhost,$dbuname,$dbpass);
        if (@mysql_select_db($dbname)) $dbs=array($dbname);
            else $dbs=array();
        return $dbs;
    }
    
    // else try to get a list of all available databases on the server
    $list=array();
    @mysql_connect($dbhost,$dbuname,$dbpass);
    $db_list=@mysql_list_dbs();
    while ($row=@mysql_fetch_array($db_list))
        if (@mysql_select_db($row['Database'])) $list[]=$row['Database'];
    return $list;
}

###############################################

function save_to_file($backupfile,$fileData,$mode){
	if ($zp=@fopen($backupfile,$mode)) {
            @fwrite($zp,$fileData);
            @fclose($zp);
            return $backupfile;
    } else {
        return FALSE;
    }

}


?>