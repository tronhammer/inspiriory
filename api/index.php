<?php
/**
 * Inspiriory API
 * 
 * This will be the primary entry point for Inspiriory api interactions.
 *
 * @package Inspiriory
 * @subpackage Handler
 * @category Database
 * @version 0.8.1
 * @since 0.0.1
 * @author Sean Murray <tron@tronnet.com>
 * @copyright Tronnet 2013
 * @license GPLv2
 *
 *     This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License, version 2, as 
 *     published by the Free Software Foundation.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program; if not, write to the Free Software
 *     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

	global $func, $gfunc, $gvars, $pfunc, $pvars;
	
	require_once('lib/Questions.php');
	
	require_once('lib/DBHandler.php');
	
	require_once('lib/slib.php');
	require_once('get/get.php');
	require_once('post/post.php');
	
	if (isset($_GET['action'])){
		if (isset($gfunc[ $_GET['action'] ]) && function_exists($gfunc[ $_GET['action'] ]) && is_callable($gfunc[ $_GET['action'] ])){
			echo $gfunc[ $_GET['action'] ]($_GET, $gvars, $func);
			exit(0);
		} else {
			echo $func['wrap']( null, 1, 'That action is not available!');
			exit(1);
		}
	} else if (isset($_POST['action'])){
		if (isset($pfunc[ $_POST['action'] ]) && function_exists($pfunc[ $_POST['action'] ]) && is_callable($pfunc[ $_POST['action'] ])){
			echo $pfunc[ $_POST['action'] ]($_POST, $pvars, $func);
			exit(0);
		} else {
			echo $func['wrap']( null, 1, 'That action is not available!');
			exit(1);
		}
	} else {
		echo $func['wrap']( null, 1, 'You must specify an action!');
		exit(1);
	}
