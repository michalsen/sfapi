<?php

/**
 *  Create Lead Keys
 */
  $check = new LeadKeys();
  $gnl_fields = $check->gnl_fields;
  $sf_fields = $check->sf_fields;
  $mapped_fields = $check->mapped_fields;

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<title>GNL / SF Mapping</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script language="javascript" type="text/javascript" src="js/scripts.js"></script></head>

<body>

<div class="GNL_KEYS">
<h3>GNL Keys</h3>
<ul id="sortableFields_gnl">
<?php
  foreach ($gnl_fields as $key => $value) {
    print '<li class="ui-state-default" id="' . $value . '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $value . '</li>';
  }
?>
</ul>
</div>

<div class="MAPPED_KEYS">
<h3>Mapped</h3>
<ul id="mapped_fields" class="list2">
<?php
  foreach ($mapped_fields as $key => $value) {
    print '<li class="" id="' . $value . '"><span class=""></span>' . $value . '</li>';
  }
?>
</ul>
</div>

<div class="SF_KEYS">
<h3>SF Keys</h3>
<ul id="sortableFields_sf" class="list1">
<?php
  foreach ($sf_fields as $key => $value) {
    print '<li class="item" id="' . $value . '">' . $value . '</li>';
  }
?>
</ul>
</div>


<div class"footer">
  <fieldset><legend>SF Credentials</legend>
<?php
  $cred = new SalesforceLogin();
  if (isset($cred->SFcredentials['usr'])) {
    print '<strong>Credentials in place.</strong>';
  }
   else {
?>
    <form method=POST action="/">
      <p><label class="field">User:</label><input type=text name="usr" id="user"><br>
      <p><label class="field">Pass:</label><input type=text name="pwd" id="pass"><br>
      <p><label class="field">Key:</label><input type=text name="key" id="key"><br>
      <p><input type=submit name="sf_credentials" value="Save">
    </form>
<?php
  }
?>
  </fieldset>

</footer>

</body>


</html>
