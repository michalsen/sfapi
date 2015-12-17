 <?php
session_start();
error_reporting(E_ALL);
/**
 *  GNL/SF Integration
 *  Straight North 2015
 *  E.Michalsen
 *
 *  Routing:
 *    no path presents admin page
 *    postback/ queries GNL API with ID
 */

/**
 *  RedBean ORM
 */
require 'library/redbean.inc.php';

/**
 *  Create Classes
 */
require 'classes.inc.php';

/**
 *  Routing
 */
require 'routing.php';

