<?php
require_once(str_replace('\\\\','/',dirname(__FILE__)).'/../class/config.class.php');
require_once(str_replace('\\\\','/',dirname(__FILE__)).'/../class/util.class.php');

$spawvar = new SpawVars();
$spawconfig = new SpawConfig();
// sets physical filesystem directory of web site root
// if calculation fails (usually if web server is not apache) set this manually
if (@strstr($_ENV["OS"], "Win"))
{
	$spawconfig->setStaticConfigItem('DOCUMENT_ROOT', str_replace("\\","/",$spawvar->getServerVar("DOCUMENT_ROOT")));
}
else
{
	$spawconfig->setStaticConfigItem('DOCUMENT_ROOT', str_replace($spawvar->getServerVar("SCRIPT_NAME"),'',$spawvar->getServerVar("SCRIPT_FILENAME")));
} 
if (!preg_match('/\/$/i', $spawconfig->getStaticConfigValue('DOCUMENT_ROOT')))
  $spawconfig->setStaticConfigItem('DOCUMENT_ROOT', $spawconfig->getStaticConfigValue('DOCUMENT_ROOT').'/');
// sets physical filesystem directory where spaw files reside
// should work fine most of the time but if it fails set SPAW_ROOT manually by providing correct path
$spawconfig->setStaticConfigItem('SPAW_ROOT', str_replace("\\","/",realpath(dirname(__FILE__)."/..").'/'));
// sets virtual path to the spaw directory on the server
// if calculation fails set this manually
$spawconfig->setStaticConfigItem('SPAW_DIR', '/'.str_replace($spawconfig->getStaticConfigValue("DOCUMENT_ROOT"),'',$spawconfig->getStaticConfigValue("SPAW_ROOT")));

//$spawconfig->setStaticConfigItem('SPAW_DIR', 'modules/spaw/');

//echo 'SPAW_ROOT='.$spawconfig->getStaticConfigValue("SPAW_ROOT").'<br>';
//echo 'SPAW_DIR='.$spawconfig->getStaticConfigValue("SPAW_DIR").'<br>';
//echo 'DOCUMENT_ROOT='.$spawconfig->getStaticConfigValue("DOCUMENT_ROOT").'<br>';
//echo 'SCRIPT_NAME='.$spawconfig->getStaticConfigValue("SCRIPT_NAME").'<br>';

/*
// semi-automatic path calculation
// comment the above settings of DOCUMENT_ROOT, SPAW_ROOT and SPAW_DIR
// and use this block if the above fails.
// set SPAW_DIR manually. If you access demo page by http://domain.com/spaw2/demo/demo.php
// then set SPAW_DIR to /spaw2/
$spawconfig->setStaticConfigItem('SPAW_DIR', '/spaw2/');
// and the following settings will be calculated automaticly
$spawconfig->setStaticConfigItem('SPAW_ROOT', str_replace("\\","/",realpath(dirname(__FILE__)."/..").'/'));
$spawconfig->setStaticConfigItem('DOCUMENT_ROOT', substr($spawconfig->getStaticConfigValue('SPAW_ROOT'),0,strlen($spawconfig->getStaticConfigValue('SPAW_ROOT'))-strlen($spawconfig->getStaticConfigValue('SPAW_DIR'))));
*/

/*
// under IIS you will probably need to setup the above paths manually. it would be something like this
$spawconfig->setStaticConfigItem('DOCUMENT_ROOT', 'c:/inetpub/wwwroot/');
$spawconfig->setStaticConfigItem('SPAW_ROOT', 'c:/inetpub/wwwroot/spaw2/');
$spawconfig->setStaticConfigItem('SPAW_DIR', '/spaw2/');
*/

// DEFAULTS used when no value is set from code
// language 
$spawconfig->setStaticConfigItem('default_lang','en');
// output charset (empty strings means charset specified in language file)
$spawconfig->setStaticConfigItem('default_output_charset','');
// theme 
$spawconfig->setStaticConfigItem('default_theme','spaw2');
// toolbarset  default : standard
$spawconfig->setStaticConfigItem('default_toolbarset','all');
// stylesheet
$spawconfig->setStaticConfigItem('default_stylesheet',$spawconfig->getStaticConfigValue('SPAW_DIR').'wysiwyg.css');
// width 
$spawconfig->setStaticConfigItem('default_width','100%');
// height 
$spawconfig->setStaticConfigItem('default_height','320px');

// specifies if language subsystem should use iconv functions to convert strings to the specified charset
$spawconfig->setStaticConfigItem('USE_ICONV',true);
// specifies rendering mode to use: "xhtml" - renders using spaw's engine, "builtin" - renders using browsers engine
$spawconfig->setStaticConfigItem('rendering_mode', 'xhtml', SPAW_CFG_TRANSFER_JS);
// specifies that xhtml rendering engine should indent it's output
$spawconfig->setStaticConfigItem('beautify_xhtml_output', true, SPAW_CFG_TRANSFER_JS);
// specifies host and protocol part (like http://mydomain.com) that should be added to urls returned from file manager (and probably other places in the future) 
$spawconfig->setStaticConfigItem('base_href', '', SPAW_CFG_TRANSFER_JS);
// specifies if spaw should strip domain part from absolute urls (IE makes all links absolute)
$spawconfig->setStaticConfigItem('strip_absolute_urls', true, SPAW_CFG_TRANSFER_JS);
// specifies in which directions resizing is allowed (values: none, horizontal, vertical, both)
$spawconfig->setStaticConfigItem('resizing_directions', 'vertical', SPAW_CFG_TRANSFER_JS);
// specifies that special characters should be converted to the respective html entities
$spawconfig->setStaticConfigItem('convert_html_entities', false, SPAW_CFG_TRANSFER_JS);

// data for style (css class) dropdown list
$spawconfig->setStaticConfigItem("dropdown_data_core_style",
  array(
    '' => 'Normal',
    'style1' => 'Style No.1',
    'style2' => 'Style No.2',
  )
);
// data for style (css class) dropdown in table properties dialog
$spawconfig->setStaticConfigItem("table_styles",
  array(
    '' => 'Normal',
    'style1' => 'Style No.1',
    'style2' => 'Style No.2',
  )
);
// data for style (css class) dropdown in table cell properties dialog
$spawconfig->setStaticConfigItem("table_cell_styles",
  array(
    '' => 'Normal',
    'style1' => 'Style No.1',
    'style2' => 'Style No.2',
  )
);
// data for fonts dropdown list
$spawconfig->setStaticConfigItem("dropdown_data_core_fontname",
  array(
    'Arial' => 'Arial',
    'Courier' => 'Courier',
    'Tahoma' => 'Tahoma',
    'Times New Roman' => 'Times',
    'Verdana' => 'Verdana'
  )
);
// data for fontsize dropdown list
$spawconfig->setStaticConfigItem("dropdown_data_core_fontsize",
  array(
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6'
  )
);
// data for paragraph dropdown list
$spawconfig->setStaticConfigItem("dropdown_data_core_formatBlock",
  array(
    'Normal' => 'Normal',
    '<H1>' => 'Heading 1',
    '<H2>' => 'Heading 2',
    '<H3>' => 'Heading 3',
    '<H4>' => 'Heading 4',
    '<H5>' => 'Heading 5',
    '<H6>' => 'Heading 6',
    '<pre>' => 'Preformatted',
    '<address>' => 'Address',
    '<p>' => 'Paragraph'    
  )
);
// data for link targets drodown list in hyperlink dialog
$spawconfig->setStaticConfigItem("a_targets",
  array(
    '_self' => 'Self',
    '_blank' => 'Blank',
    '_top' => 'Top',
    '_parent' => 'Parent'
  )
);


// toolbar sets (should start with "toolbarset_"
// standard core toolbars
$spawconfig->setStaticConfigItem('toolbarset_standard',
  array(
    "format" => "format",
    "style" => "style",
    "edit" => "edit",
    "table" => "table",
    "plugins" => "plugins",
    "insert" => "insert",
    "tools" => "tools"
  ) 
);
// all core toolbars
$spawconfig->setStaticConfigItem('toolbarset_all',
  array(
    "format" => "format",
    "style" => "style",
    "edit" => "edit",
    "table" => "table",
    "plugins" => "plugins",
    "insert" => "insert",
    "tools" => "tools",
    "font" => "font"   
  ) 
);
// mini core toolbars
$spawconfig->setStaticConfigItem('toolbarset_mini',
  array(
    "format" => "format_mini",
    "edit" => "edit",
    "tools" => "tools"
  ) 
);

// colorpicker config
$spawconfig->setStaticConfigItem('colorpicker_predefined_colors',
  array(
    'black',
    'silver',
    'gray',
    'white',
    'maroon',
    'red',
    'purple',
    'fuchsia',
    'green',
    'lime',
    'olive',
    'yellow',
    'navy',
    'blue',
    '#fedcba',
    'aqua'
  ),
  SPAW_CFG_TRANSFER_SECURE
);

// SpawFm plugin config:

// global filemanager settings
$spawconfig->setStaticConfigItem(
  'PG_SPAWFM_SETTINGS',
  array(
    'allowed_filetypes'   => array('any'),  // allowed filetypes groups/extensions
    'allow_modify'        => true,         // allow edit filenames/delete files in directory
    'allow_upload'        => true,         // allow uploading new files in directory
    'chmod_to'          => 0644,          // change the permissions of an uploaded file if allowed
                                            // (see PHP chmod() function description for details), or comment out to leave default
    'max_upload_filesize' => 0,             // max upload file size allowed in bytes, or 0 to ignore
    'max_img_width'       => 0,             // max uploaded image width allowed, or 0 to ignore
    'max_img_height'      => 0,             // max uploaded image height allowed, or 0 to ignore
    'recursive'           => true,         // allow using subdirectories
    'allow_modify_subdirectories' => true, // allow renaming/deleting subdirectories
    'allow_create_subdirectories' => true, // allow creating subdirectories
    'forbid_extensions'   => array('php','js'),  // disallow uploading files with specified extensions
    'forbid_extensions_strict' => true,     // disallow specified extensions in the middle of the filename
  ),
  SPAW_CFG_TRANSFER_SECURE
);
if(isset($_GET['paths']))
	$pathpics = $_GET['paths'];
else
	$pathpics = "";
// directories
$spawconfig->setStaticConfigItem(
  'PG_SPAWFM_DIRECTORIES',
  array(
    array(
      'dir'     => $pathpics,
      'caption' => 'Flash movies', 
      'params'  => array(
        'allowed_filetypes' => array('flash')
      )
    ),
    array(
      'dir'     => $pathpics,
      'caption' => 'Images',
      'params'  => array(
        'default_dir' => true, // set directory as default (optional setting)
        'allowed_filetypes' => array('images')
      )
    ),
    /*
    array(
      'dir'     => $pathpics,
      //'fsdir'   => $spawconfig->getStaticConfigValue('SPAW_ROOT').'uploads/files/', // optional absolute physical filesystem path
      'caption' => 'Videos', 
      'params'  => array(
        'allowed_filetypes' => array('video')
      )
    ),
    */
    array(
      'dir'     => $pathpics,
      //'fsdir'   => $spawconfig->getStaticConfigValue('SPAW_ROOT').'uploads/files/', // optional absolute physical filesystem path
      'caption' => 'Files', 
      'params'  => array(
        'allowed_filetypes' => array('any')
      )
    ),
    
  ),
  SPAW_CFG_TRANSFER_SECURE
);
?>
