<?php 
// File: $Id: language.php,v 1.1.1.1 2007/02/22 03:17:41 mrmeaw Exp $ $Name: HEAD $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the Post-Nuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Gregor J. Rothfuss
// Purpose of file: Provide ML functionality for the installer.
// ----------------------------------------------------------------------
/**
 * Loads the required language file for the installer
 */
function installer_get_language()
{
    global $currentlang;
    if (!isset($currentlang)) {
        $currentlang = 'tha'; // english is the fallback
    } 
    if (file_exists($file = "install/lang/$currentlang/global.php")) {
        @include $file;
    } elseif (file_exists($file = "install/lang/$language/global.php")) {
        @include $file;
    } 
    if (file_exists($file = "language/$currentlang/global.php")) {
        @include $file;
    } elseif (file_exists($file = "language/$language/global.php")) {
        @include $file;
    } 
} 
// Make common language selection dropdown (from Tim Litwiller)
// =======================================
function lang_dropdown()
{
    global $currentlang;
    echo "<select name=\"alanguage\" class=\"pn-text\">";
    $lang = languagelist();
    $handle = opendir('install/lang');
    while ($f = readdir($handle)) {
        if (is_dir("install/lang/$f") && @$lang[$f]) {
            $langlist[$f] = $lang[$f];
        } 
    } 
    asort($langlist);
    foreach ($langlist as $k => $v) {
        echo '<option value="' . $k . '"';
        if ($currentlang == $k) {
            echo ' selected';
        } 
        echo '>' . $v . '</option> ';
    } 
    echo "</select>";
} 
// list of all availabe languages (from Patrick Kellum <webmaster@ctarl-ctarl.com>)
// ==============================
function languagelist()
{ 
    // All entries use ISO 639-2/T
    $lang['eng'] = 'English'; // English
    $lang['tha'] = 'Thai'; // Thai

	// end of list
    return $lang;
} 

?>