<?php
/**
 *  GNL/SF Routing
 */

/**
 *  Updating SF Credentials
 *
 */

if (isset($_REQUEST['sf_credentials'])) {
  //print_r($_REQUEST);
  R::wipe('sfcredentials');
  $creds = R::dispense('sfcredentials');
  $creds->usr = $_REQUEST['usr'];
  $creds->pwd = $_REQUEST['pwd'];
  $creds->key = $_REQUEST['key'];
  $id = R::store($creds);
}


/**
 *  Create admin page
 */
if ($_SERVER['REQUEST_URI'] == '/') {
  if (isset($_SESSION['loggedin'])) {
    require 'page.tpl.php';
  }
   else {
    require 'login.tpl.php';
   }
}


/*
A Few Phone Call Leads:
465314 - *
465366 - *

A Few form Submissions:
465991  - *
465992 - *
*/


/**
 *  Postback call
 */
if (preg_match('/postback/', $_SERVER['REQUEST_URI'])) {

// We need to grab the keys for the SF lead
$sf = R::find('fields', 'service = ?', array('mapped') );
  foreach ($sf as $key => $value) {
     $sf_keys .= $value->key;
  }
  $sf_fields = explode(',', $sf_keys);

// And we need to grab the keys for the GNL lead
$gnl = R::find('fields', 'service = ?', array('gnl') );
  foreach ($gnl as $key => $value) {
     $gnl_keys .= $value->key;
  }
  $gnl_fields = explode(',', $gnl_keys);


// What is the lead ID
$parsed = explode('/', $_SERVER['REQUEST_URI']);

/**
 *  parsed[2] for testing
 *  $_POST['leadID'] from GNL
 */
if (isset($parsed[2])) {
  $lead = $parsed[2];
}

if (isset($_POST['lead_id'])) {
  $lead = $_POST['lead_id'];
}
/*
foreach ($_POST as $key => $value) {
 $msg .= $key . ': ' . $value . "\n";
}
mail('emichalsen@straightnorth.com', 'sf lead test', $msg);
*/
// Nice! Now get the lead data!

if (isset($lead)) {
$gnl_lead = new GetLead($lead);

// This is crazy, but we need to walk through the array, no matter the
// levels, and get the lead data.
$objTmp = (object) array('item' => array());
array_walk_recursive($gnl_lead, create_function('&$v, $k, &$t', '$t->item[] = $k . \'::\' . $v;'), $objTmp);

// Now we need to take that data and build an array of the lead data
$lead_data = array();
foreach ($objTmp->item as $key => $value) {
  $row = explode('::', $value);
  if (strlen($row[1]) > 0 ) {
    $lead_data[] = array($row[0] => $row[1]);
  }
}


//print '<pre>';
//print_r($lead_data);
/**
 * OK, we have the lead data, and the mapping keys.
 * Let's do this!
*/


$LeadID = $lead_data[0]['id'];

// This array will be our SF data
$lead = array();

foreach ($lead_data as $key => $value) {
  foreach ($value as $key1 => $value1) {

   // Find a name for the required Last Name field
      if (!isset($last_name) && $key1 == 'seogroup-form-name') {
          $last_name = $value1;
          //print 'last name: ' . $last_name . '<br>';
       }

     foreach ($gnl_fields as $gnl_key => $gnl_value) {
      if ($key1 == $gnl_value) {
           foreach ($sf_fields as $sf_key => $sf_value) {
             if ($sf_key == $gnl_key) {
               if (!isset($lead[$sf_value])) {
                 if (isset($value1)) {
                  $lead[$sf_value] = $value1;
                  unset($value1);
                   }
                 }
               }
             }
          }
        }
      }
    }


/**
 *  Hardcoded Keys
 */
$usr = '';
$pss = '';
$key = '';

require_once ('soapclient/SforceEnterpriseClient.php');
$enterprise_wsdl = "soapclient/enterprise.wsdl.xml";


try {

  $ENT = new SforceEnterpriseClient();
  $ENT_client = $ENT->createConnection($enterprise_wsdl);
  $ENT_login  = $ENT->login($usr, $pss.$key);

  $fields = $ENT->describeSObject('Lead')->fields;

  $new_lead[0]            = new stdClass;
  foreach ($lead as $key => $value) {
      $new_lead[0]->$key = $value;
    }

  // We have some required fields
  $new_lead[0]->Company = 'N/A';

  if (!isset($new_lead[0]->LastName)) {
    if (isset($new_lead[0]->FirstName)) {
    $new_lead[0]->LastName = $new_lead[0]->FirstName;
    }
     else {
    $new_lead[0]->LastName = 'N/A';
     }
  }

    $new_lead[0]->LastName = 'N/A';
  unset($new_lead[0]->FirstName);
  // TESTING
  //$new_lead[0]->LastName = NULL;

  $gnl_lead = $lead['SNLID__c'];
  $check_lead = new CheckLead($gnl_lead);

    print '<pre>';
  // CREATE/UPDATE LEAD
  if (!isset($check_lead->checklead)) {
    $response = $ENT->create($new_lead, 'lead');
    if (isset($response[0]->errors)) {
      $record = array($gnl_lead, NULL, $response[0]->errors[0]->message . ' ' . $response[0]->errors[0]->statusCode, 'error');
      $dispense = new DispenseRecord($record);
    }
    else
    {
      $record = array($gnl_lead, $response[0]->id, 'success', 'new');
       $dispense = new DispenseRecord($record);
     }
   } 
     else {
       // Update Lead
       $gnlObject = new stdClass();
       $gnlObject->ID = $check_lead->checklead;
       $gnlObject->Classification__c = $lead['Classification__c'];
       $gnlObject->Lead_Date__c = $lead['Lead_Date__c'];
       $gnlObject->Phone_Call_Notes__c = $lead['notes'];
       $response = $ENT->update(array ($gnlObject), 'Lead');
       $record = array($gnl_lead, $check_lead->checklead, 'success', 'update');
       $dispense = new DispenseRecord($record);
   }



    } catch (Exception $e) {
      print '<br><strong>SF Fail</strong><br>';
      print '<pre>';
       print '<br>line: ' . __LINE__ . '<br>';
      print_r($ENT);
       print '<br>line: ' . __LINE__ . '<br>';
      print_r($e);
    }
  }
}
