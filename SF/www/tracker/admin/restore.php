<?php
//
// Copyright (c) Xerox Corporation, CodeX Team, 2001-2003. All rights reserved
//
// $Id$
//
//
//
//  Written for CodeX by Stephane Bouhet
//

require($DOCUMENT_ROOT.'/include/pre.php');
require($DOCUMENT_ROOT.'/../common/include/GroupFactory.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactTypeFactory.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactType.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactCanned.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactFieldFactory.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactField.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactReport.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactReportFactory.class');
require($DOCUMENT_ROOT.'/../common/tracker/ArtifactReportField.class');
require($DOCUMENT_ROOT.'/../common/tracker/Artifact.class');
require('../include/ArtifactTypeHtml.class');
require('../include/ArtifactCannedHtml.class');
require('../include/ArtifactReportHtml.class');
require('../include/ArtifactHtml.class');

session_require(array('group'=>'1','admin_flags'=>'A'));


	switch ( $func ) {
	case 'restore':
	        $group = group_get_object($group_id);	
		$ath =  new ArtifactType($group, $atid);
		if (!$ath->restore()) {
		  $feedback = 'Restore operation failed';
		} else {
		  $feedback = 'Restored tracker';
		}
		break;
		
	case 'delay':
	        $group = group_get_object($group_id);	
		$ath =  new ArtifactType($group, $atid);
		// just check date >= today

		if (!$ath->delay($delay_date)) {
		  if ($ath->isError())
		    exit_error('Error',$ath->getErrorMessage()." | Delay operation failed");
		  exit_error('Error','Delay operation failed');
		} else {
		  $feedback = 'Delayed deletion';
		}
		break;

		
	case 'delete':
		// Create field factory
	        $group = group_get_object($group_id);	
		$ath =  new ArtifactType($group, $atid);
		$atf = new ArtifactTypeFactory($group);
		$art_field_fact = new ArtifactFieldFactory($ath);

		// Then delete all the fields informations
		if ( !$art_field_fact->deleteFields($atid) ) {
			exit_error('Error',$art_field_fact->getErrorMessage());
			return false;
		}
		
		// Then delete all the reports informations
		// Create field factory
		$art_report_fact = new ArtifactReportFactory();

		if ( !$art_report_fact->deleteReports($atid) ) {
			exit_error('Error',$art_report_fact->getErrorMessage());
			return false;
		}
		
		// Delete the artifact type itself
		if ( !$atf->deleteArtifactType($atid) ) {
			exit_error('Error',$atf->getErrorMessage());
		}
		$feedback = "Tracker deleted";
		break;


	default:  
	  break;
	} // switch
$group = group_get_object(1);	
$ath = new ArtifactTypeHtml($group);

$HTML->header(array('title'=>"Pending Tracker Deletions"));
$atf = new ArtifactTypeFactory($group);
$ath->displayPendingTrackers();
$HTML->footer(array());

?>
