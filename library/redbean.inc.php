<?php

/**
 *  RedBean ORM
 */

require $_SERVER['DOCUMENT_ROOT'] . '/RedBean/rb.php';
R::setup( 'mysql:host=localhost;dbname=' . $db . ', ' . $user . ', ' . $pass ); //for both mysql or mariaDB

