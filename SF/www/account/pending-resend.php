<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id$

require($DOCUMENT_ROOT.'/include/pre.php');    

$res_user = db_query("SELECT * FROM user WHERE user_name='$form_user'");
$row_user = db_fetch_array($res_user);

// send mail
if (session_issecure()) {
    $server = 'https://'.$GLOBALS['sys_https_host'];
} else {
    $server = 'http://'.$GLOBALS['sys_default_domain'];
}
$message = "Thank you for registering on the ".$GLOBALS['sys_name']." web site. In order\n"
	. "to complete your registration, visit the following url: \n\n"
	. "$server/account/verify.php?confirm_hash=$row_user[confirm_hash]\n\n"
	. "Enjoy the site.\n\n"
	. " -- The ".$GLOBALS['sys_name']." Team\n";


// only mail if pending
list($host,$port) = explode(':',$GLOBALS['sys_default_domain']);		
if ($row_user[status] == 'P') {
    $hdrs = "From: noreply@".$host.$GLOBALS['sys_lf'];
    $hdrs .='Content-type: text/plain; charset=iso-8859-1'.$GLOBALS['sys_lf'];

    mail($row_user[email], $GLOBALS['sys_name']." Account Registration",$message,$hdrs);
	$HTML->header(array(title=>"Account Pending Verification"));
?>

<P><B>Pending Account</B>

<P>Your email confirmation has been resent. Visit the link
in this email to complete the registration process.

<P><A href="/">[Return to <?php print $GLOBALS['sys_name']; ?>]</A>
 
<?php
} else {
	exit_error("Error","This account is not pending verification.");
}

$HTML->footer(array());

?>
