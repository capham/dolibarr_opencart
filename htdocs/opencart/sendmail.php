<?php
$subject = 'Ca test send mail via trigger';
$sendto = 'vanca.vnn@gmail.com';
$from = 'Ca Pham <capham.vn@gmail.com>';
$message = 'Content email';
$sendtocc = '';
$sendtobcc = '';
$deliveryreceipt = '';
$replyto = 'Carl Pham <vanca.vnn@gmail.com>';
$trackid = '';

// Create form object
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
$formmail = new FormMail($db);
$formmail->trackid = $trackid;      // $trackid must be defined

$attachedfiles=$formmail->get_attached_files();
$filepath = $attachedfiles['paths'];
$filename = $attachedfiles['names'];
$mimetype = $attachedfiles['mimes'];

// Feature to push mail sent into Sent folder
// if (! empty($conf->dolimail->enabled))
// {
// 	$mailfromid = explode("#", $_POST['frommail'],3);	// $_POST['frommail'] = 'aaa#Sent# <aaa@aaa.com>'	// TODO Use a better way to define Sent dir.
// 	if (count($mailfromid)==0) $from = $_POST['fromname'] . ' <' . $_POST['frommail'] .'>';
// 	else
// 	{
// 		$mbid = $mailfromid[1];

// 		/*IMAP Postbox*/
// 		$mailboxconfig = new IMAP($db);
// 		$mailboxconfig->fetch($mbid);
// 		if ($mailboxconfig->mailbox_imap_host) $ref=$mailboxconfig->get_ref();
	
// 		$mailboxconfig->folder_id=$mailboxconfig->mailbox_imap_outbox;
// 		$mailboxconfig->userfolder_fetch();
	
// 		if ($mailboxconfig->mailbox_save_sent_mails == 1)
// 		{
		
// 			$folder=str_replace($ref, '', $mailboxconfig->folder_cache_key);
// 			if (!$folder) $folder = "Sent";	// Default Sent folder
		
// 			$mailboxconfig->mbox = imap_open($mailboxconfig->get_connector_url().$folder, $mailboxconfig->mailbox_imap_login, $mailboxconfig->mailbox_imap_password);
// 			if (FALSE === $mailboxconfig->mbox) 
// 			{
// 				$info = FALSE;
// 				$err = $langs->trans('Error3_Imap_Connection_Error');
// 				setEventMessages($err,$mailboxconfig->element, null, 'errors');
// 			} 
// 			else 
// 			{
// 				$mailboxconfig->mailboxid=$_POST['frommail'];
// 				$mailboxconfig->foldername=$folder;
// 				$from = $mailfromid[0] . $mailfromid[2];
// 				$imap=1;
// 			}
		
// 		} 
// 	}
// }

// Send mail
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
$mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,$sendtobcc,$deliveryreceipt,-1,'','',$trackid);

if ($mailfile->error) {
	dol_syslog('Ca send mail fail!: '.json_encode($mailfile->error), LOG_ERR, 0, '_ca');
} else {
	$result = $mailfile->sendfile();

	if ($result) {
		dol_syslog('Ca send mail successful!', LOG_INFO, 0, '_ca');
	} else {
		dol_syslog('Ca send mail fail.', LOG_ERR, 0, '_ca');
	}
}
	