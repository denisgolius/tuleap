<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id$

require($DOCUMENT_ROOT.'/include/pre.php');    // Initial db and session library, opens session
$HTML->header(array(title=>"Privacy Policy"));

include(util_get_content('tos/privacy'));

$HTML->footer(array());

?>
