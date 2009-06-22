<?php
/*  Copyright 2008, ontoprise GmbH
*  This file is part of the RichMedia-Extension.
*
*   The RichMedia-Extension is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 3 of the License, or
*   (at your option) any later version.
*
*   The RichMedia-Extension is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * This file contains the settings that configure the upload converter extension.
 * It can convert uploaded PDF and MS Word documents to text.
 * 
 * @author Thomas Schweitzer
 */

global $smwgRMIP;
// Definition of converters for various mime types.

// check OS is windows?
if (strpos(strtolower(php_uname('s')), "win") !== false) {
	$smwgUploadConverter = array(
		'application/pdf' => "$smwgRMIP/bin/xpdf/pdftotext.exe -enc UTF-8 -layout {infile} {outfile}",
		'application/msword' => $smwgRMIP.'/bin/antiword/antiword.exe -m UTF-8.txt "{infile}" > "{outfile}"'
	);
}
// some Unix flavour
else {
	$smwgUploadConverter = array();
	// check for antiword
	$exe = @exec('which antiword');
	if (strlen($exe) > 0) {
		$smwgUploadConverter['application/msword'] = $exe.' -m '.$smwgRMIP.'/bin/antiword/UTF-8.txt "{infile}" > "{outfile}"';
	}
	$exe = @exec('which pdftotext');
	if (strlen($exe) > 0) {
		$smwgUploadConverter['application/pdf'] = $exe.' -enc UTF-8 -layout "{infile}" "{outfile}"';
	}	
}