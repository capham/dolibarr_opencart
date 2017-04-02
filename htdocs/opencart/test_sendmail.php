<?php

require '../main.inc.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
// require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
// require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

// $socid = GETPOST('socid','int');
// if (!empty($socid)) {
// 	echo 'Done!';
// 	die;
// }
// require_once DOL_DOCUMENT_ROOT .'/societe/class/societe.class.php';

// require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
// $object = new Usergroup($db);
// $object->fetch(1);
// if (! empty($object->members))
// {
// 	$listEmail = [];
// 	foreach($object->members as $useringroup)
// 	{
// 		$email = $useringroup->email;
// 		$listEmail[$useringroup->id] = $email;
// 	}
// }
// die;
// require_once DOL_DOCUMENT_ROOT .'/core/class/CMailFile.class.php';
// $mailfile = new CMailFile(
//     'Ca test',
//     'vanca.ptit@gmail.com',
//     'vanca.ptit@gmail.com',
//     'Content test email',
//     array(),
//     array(),
//     array(),
//     '',
//     '',
//     0,
//     -1
// );
        
// $rs = $mailfile->sendfile();
// var_dump($rs);
// die;

//////////////////////////////////////////////////////////////////////
// Test Template Email
//////////////////////////////////////////////////////////////////////
// $ocTemplateEmail = 'oc_new_order_send';
// $sql = "SELECT rowid, label, topic, content, lang, position FROM ".MAIN_DB_PREFIX.'c_email_templates'." WHERE type_template='".$ocTemplateEmail."' AND active = 1 ORDER BY position,lang,label ASC LIMIT 0,1";
// $resql = $db->query($sql);
// $emailTemp = $db->fetch_object($resql);
// var_dump($resql);
// die;
//////////////////////////////////////////////////////////////////////
// Test Send mail
//////////////////////////////////////////////////////////////////////

$id=9;
$actiontypecode='AC_OTH_AUTO';
// $trigger_name='COMPANY_SENTBYMAIL';
$paramname='socid';
$mode='emailfromthirdparty';

$action = 'send';
// $object = new Societe($db);

$_POST['fromname'] = 'Ca Pham';
$_POST['frommail'] = 'capham.vn@gmail.com';
$_POST['sendto'] = 'vanca.vnn@gmail.com';
$_POST['subject'] = 'Ca test send mail '.time();
$_POST['message'] = 'Content of email';

// $_POST['receivercc'] = '-1';
// $_POST['deliveryreceipt'] = '0';
// $_POST['returnurl'] = '/societe/soc.php?socid=9';

// dol_syslog('Ca test debug log', 7, 0, '_ca');
include DOL_DOCUMENT_ROOT.'/opencart/sendmail.php';
// include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';