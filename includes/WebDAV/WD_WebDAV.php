<?php
/*
 * Copyright (C) Vulcan Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program.If not, see <http://www.gnu.org/licenses/>.
 *
 */

/*
 * This file is based on the MediaWiki WebDAV extension,
 * which is available at http://www.mediawiki.org/wiki/Extension:WebDAV
 *
 * This modified version was implemented by Ingo Steinbauer @ontoprise GmbH
 */

/**
 * @file
  * @ingroup RMWebDAV
  */

/**
 * This group contains all parts of the RichMedia extension that deal with WebDAV access..
 * @defgroup RMWebDAV
 * @ingroup RichMedia
 */


# Initialise common code
chdir('./../../../../');
require_once("./includes/WebStart.php");

global $wgWebDAVServerEnabled;
if(!$wgWebDAVServerEnabled){
	return;
}

$server = new WebDAVServer;
$server->handleRequest();

?>
