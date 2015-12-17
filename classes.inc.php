<?php
/**
 *  Get GNL Lead
 */

class LeadKeys {


  public function __construct() {

    // Check for pre-existing fields in db
    $gnl_count    = R::count( 'fields', ' service=? ', array('gnl') );
    $sf_count     = R::count( 'fields', ' service=? ', array('sf') );
    $mapped_count = R::count( 'fields', ' service=? ', array('mapped') );

    // If no GNL keys mapped, use the following array and store in db
     if ($gnl_count < 1 ) {
         $key_items = '0,1,caller_city,caller_name,caller_number,caller_postal_code,caller_state,classification,comments,date,duration,email,first_name,help,how_can_we_help,id,im_budget,interested_in,name,notes,phone,seogroup-form-email,seogroup-form-keyword,seogroup-form-name,source,status,trackable_number,type,website_budget,website_url';
           $w = R::dispense('fields');
           $w->service = 'gnl';
           $w->key = $key_items;
           R::store($w);
       }
        else {
         $leads = R::find('fields', 'service = ?', array('gnl') );
          foreach ($leads as $key => $value) {
            $key_items .= $value->key;
          }
       }
       $gnl_fields = explode(',', $key_items);
       unset($key_items);

     // If no SF keysk call GetSFKey which grabs the keys from SF
     if ($sf_count < 1 ) {
         $leads = new GetSFKey();
         $sf_fields = $leads->sf_fields;
         foreach ($sf_fields as $key => $value) {
           $key_items .= $value->name . ',';
         }
         $sf_fields = explode(',', $key_items);
       }
        else {
         $leads = R::find('fields', 'service = ?', array('sf') );
          foreach ($leads as $key => $value) {
            $key_items = $value->key;
          }
         $sf_fields = explode(',', $key_items);
       }

     // Mapped field
     if ($mapped_count > 0 ) {
         $mapped = R::find('fields', 'service = ?', array('mapped') );
          foreach ($mapped as $key => $value) {
            $mapped_keys .= $value->key;
          }
          $mapped_fields = explode(',', $mapped_keys);
       }


     $this->gnl_fields = $gnl_fields;
     $this->sf_fields = $sf_fields;
     $this->mapped_fields = $mapped_fields;
   }


}

/**
 *  Get SF Lead
 */
class GetSFKey {

  public function __construct() {

/**
 * Salesforce Enterprise WSDL
 */
    $cred = new SalesforceLogin();
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/soapclient/SforceEnterpriseClient.php');
    $enterprise_wsdl = "soapclient/enterprise.wsdl.xml";
    $mySforceConnection = new SforceEnterpriseClient();
    $myConnection = $mySforceConnection->createConnection($enterprise_wsdl);
    $myLogin = $mySforceConnection->login($cred->SFcredentials['usr'], $cred->SFcredentials['pwd'].$cred->SFcredentials['key']);
    $fields = $mySforceConnection->describeSObject('Lead')->fields;
    $this->sf_fields = $fields;
  }
}

/**
 *  Get GNL Lead
 */
class GetLead {
  public $id = array();

/**
 *  Create Object
 */
  public function __construct($leadId) {
    $lead = $this->callGnl($leadId);
    $lead_array = $this->parseXML($lead);
    $this->keys = $this->keyArray($lead);
    $this->id = $this->parseXML($lead);

  }

/**
 *  Decode JSON to an Array
 */
  private function parseXML($lead) {
    return json_decode($lead, true);
  }

/**
 *  Create Array of Keys
 */
  public function keyArray($keys) {
    $key_array = json_decode($keys, true);
    $keys = array();
    foreach ($key_array['data'] as $key => $value) {
      if (!is_array($value)) {
            $keys[] = $key;
        }
         else {
          foreach ($value as $subkey => $subvalue) {
              $keys[] = $subkey;
           }
        }
    }

    return $keys;
  }

/**
 *  CURL call to GNL API for lead
 */
  public function callGnl($leadId)
  {
     $ch = curl_init();
     $ret = curl_setopt($ch, CURLOPT_URL, "https://api.gonorthreporting.com/v1/10001/leads/" . $leadId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
               'X-ApiToken: 8a8687e38772c24c4beb1e6753b1df37a975028948cb1204b51a6d07f485dc87',
               'Content-Type: application/json',
               'Accept: application/json',
              )
            );
     $ret = curl_exec($ch);
     return $ret;
  }

}

/**
 *  Get SF Login
 */
class SalesforceLogin {
  public $SFcredentials = array();

  public function __construct()
    {
      $creds = R::load('sfcredentials', 1);
      $sf_cred = array();
          foreach ($creds as $key => $value) {
            $sf_cred[$key] = $value;
          }
      $this->SFcredentials = $this->getSalesforce($sf_cred);
    }

  public function getSalesforce($cred)
    {
      return $cred;
    }

}

/**
 *  Check Lead History
 */
class CheckLead {
  public $checklead = array();

  public function __construct($checklead)
    {
      $check  = R::find( 'history', ' gnl_lead = ? ', [ $checklead ] );
      $this->checklead = $this->checklead($check);
    }

  public function checklead($checklead)
  {
    foreach ($checklead as $key => $value) {
      print 'sf: ' . $value->sf_lead . '<br>';
       if (strlen($value->sf_lead) > 0) {
         return $value->sf_lead;
       }
    }
      //return $checklead;
    }
}

/**
 *  Add / Update db record
 */
class DispenseRecord {
  public $dispense = array();

  public function __construct($dispense)
    {
      $this->dispense = $this->record($dispense);
    }

  public function record($dispense)
  {
    $record = R::dispense('history');
    $record->gnl_lead = $dispense[0];
    $record->sf_lead  = $dispense[1];
    $record->response = $dispense[2];
    $record->status   = $dispense[3];

    $id = R::store($record);
      return $dispense;
    }
}
