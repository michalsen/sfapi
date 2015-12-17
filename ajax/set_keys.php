<?php

/**
 *  RedBean ORM
 */
require '../library/redbean.inc.php';

/**
 *  Fields re-arrange
 */

if(isset($_REQUEST['keys'])) {
  //$w = R::wipe('fields');

  $find = R::findOne('fields', "service = ?", array($_REQUEST['service_type']));

   if ($find) {
      // EDIT
     $bean = R::load('fields', $find->id);
     $bean->key  = $_REQUEST['keys'];
     R::store($bean, $find->id);
   }
    else {
      // NEW
     $w = R::dispense('fields');
     $w->service = $_REQUEST['service_type'];
     $w->key = $_REQUEST['keys'];
     R::store($w);
    }


}

