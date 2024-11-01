<?php
/*
Plugin Name: WPBookmark
Plugin URI: 
Description: Wordpress plugin to allow users to keep bookmark.
Version: 1.0.4
Author: binnash
Author URI: http://binnash.blogspot.com
License : http://binnash.blogspot.com/2012/01/end-user-license.html
*/
//Direct access to this file is not permitted
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
    exit("Do not access this file directly.");

require_once  ('class-binnash-wpbookmark.php');
define("WPBOOKMARK_VER", "1.0.3");
define('WPBOOKMAR_FOLDER', dirname(plugin_basename(__FILE__)));
define('WPBOOKMARK_URL', WP_PLUGIN_URL. '/'. WPBOOKMAR_FOLDER); 
define('WPBOOKMARK_DIR', WP_PLUGIN_DIR .'/'. WPBOOKMAR_FOLDER);
$wpBookmark = new WPBookmark();
