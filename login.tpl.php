<?php
/**
 *  Login Required
 */

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<title>GNL / SF Mapping Login</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">


<body>
<html>
    <form method=POST action="/check.php">
      <p><label class="field">User:</label><input type=text name="user" id="user"><br>
      <p><label class="field">Pass:</label><input type=text name="pass" id="pass"><br>
      <p><input type=submit value="Login">
    </form>
</html>
</body>
