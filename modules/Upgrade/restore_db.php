<?php
/**
*  Course submission
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */

	$cpath = $_SERVER["SCRIPT_FILENAME"];
	$cpath = substr($cpath,0,strlen($cpath)-10);
	
	include $cpath."/config.php";
	
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

	if ($handle = opendir($cpath.'/backup/backup/db')) {
		
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				//echo "$file\n";
				$filescript = $file;
				if(is_file($file))
				{
					break;
				}
			}
		}
	}
    closedir($handle);
	$import_file =$cpath.'/backup/backup/db/'.$filescript;
	$data_queries=array();
    $table_old=array();
    $table_count=0;
    $table=FALSE;
    $lineCounter=0;
	
	$countpure=0;

	$lines="";

	while($line=pure($lines,$import_file,$lineCounter++)) { // helpfkt is defined above
        // if line is not empty
        $line=trim($line);
			if ($line) {                        
				// the last line did not belong to a 'create' sql query
				if (!$table) {          
        
                    // this line does not, too
                    if (strtolower(substr($line,0,6))=="insert") {
						$data_queries[]=substr($line,0,strlen($line)-1);
            
                    // this line does!
                    } elseif (strtolower(substr($line,0,12))=="create table") {
						$table=TRUE;
                        $table_count++;
                        $table_old[]=$line."\n";
            
                    // this line does not (it is a comment)
                    } elseif (strtolower(substr($line,0,1))=="#" || substr($line,0,2)=="--") {
                        continue;                    
                            
                    // this line does not, too (it is something like "use table" or "drop table")
                    } else {
                        $table_old[]=substr($line,0,strlen($line)-1);
                    }                
            
                    // the current line belongs to a create sql query
                } else {
            
                        // create sql query ending in this line
                        if (strtolower(substr($line,0,1))==")") 
							$table=FALSE;
                        $table_old[count($table_old)-1] .= $line."\n";
                }
            }           
    }

	$res=mysql_query("show table status");
    $all_tables=array();

    while($row=mysql_fetch_array($res)) $all_tables[]=$row;

	$prefix = $config['prefix'];

	foreach ($all_tables as $table) 
	{
		$sql = "DROP TABLE `".$table['Name']."`";
		mysql_query($sql) or die("error");
    }
	

	for($i=0;$i<count($table_old);$i++)	
	{
		echo $table_old[$i]."<br><br>";
		mysql_query($table_old[$i]) or die("error can't create table");
		$str_pure = explode(" ",$table_old[$i]);
		echo $str_pure[2]."<br>";
		$table_name=$str_pure[2];

		//insert data
		foreach($data_queries as $s)
		{
			if(strpos($s,$table_name))	
			{
				$sql = $s;
				echo "$sql<br><br><br>";
				mysql_query($sql) or die("error");
			}
		}
	}
			

function getline($import_file)
{
	if (!isset($GLOBALS['lnFile'])) $GLOBALS['lnFile']=null;

	if ($GLOBALS['lnFile']==null) {
		$GLOBALS['lnFile']=fopen($import_file, "r");
    }
        
    if (!feof($GLOBALS['lnFile'])) {
        return fgets($GLOBALS['lnFile']);
    } else {
        fclose($GLOBALS['lnFile']);
        $GLOBALS['lnFile']=null;
        return null;
    }
}

function pure($lines,$import_file,$lineCounter) {
    if ($lines=="") {
        return getline($import_file);
    } else {
        if ($lineCounter>=count($lines)) return null;
        if ($lines[$lineCounter]=="") return " "; else return $lines[$lineCounter];
    }
}
	

###############################################


?>