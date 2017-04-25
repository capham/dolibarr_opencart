<?php
/* Copyright (C) 2017 Ca Pham <capha.vn@gmail.com>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/opencart/class/notify.class.php
 *      \ingroup    notification
 *      \brief      File of class to manage notifications
 */
require_once DOL_DOCUMENT_ROOT .'/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT .'/user/class/usergroup.class.php';


/**
 *  Class to manage notifications
 */
class OcNotify
{
    public $db;

    /**
     *  Constructor
     *
     *  @param  DoliDB  $db Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     *  Check if notification are active for couple action/company.
     *  If yes, send mail and save trace into llx_notify.
     *
     *  @param  string  $notifcode      Code of action in llx_c_action_trigger (new usage) or Id of action in llx_c_action_trigger (old usage)
     *  @param  Object  $object         Object the notification deals on
     *  @return int                     <0 if KO, or number of changes if OK
     */
    function send($notifcode, $object)
    {
        // return 0; // For test
        global $user,$conf,$langs,$mysoc,$dolibarr_main_url_root;
        dol_syslog('Trigger, notifcode: '.$notifcode, 7, 0, '_ca');
        $notifCodes = [
            'ORDER_CREATE',
            'ORDER_APPROVE',
            'ORDER_SUPPLIER_CREATE',
            'ORDER_SUPPLIER_APPROVE',
            'PROJECT_CREATE'
        ];
        $userGroups = ['hr'=>1, 'dev'=>2];
        if (!in_array($notifcode, $notifCodes)) {
            return 0;
        }

        /*$emailTemplateType = [
            'oc_new_order_send' => 'To send mail new order in Opencart',
            'oc_approved_order_send' => 'To send mail appreved order in Opencart',
            'oc_new_project_send' => 'To send mail new project in Opencart',
        ];*/

        switch ($notifcode) {
            case 'ORDER_CREATE':
            case 'ORDER_SUPPLIER_CREATE':
                $emailTemplate = $this->getTemplateEmail('oc_new_order_send');
                $emails = $this->getEmailInfo($userGroups['hr']);
                foreach ($emails as $email) {
                    $info = [
                        'sendto' => $email,
                        'replyto' => $email
                    ];
                    $mail = $this->makeEmail($emailTemplate, $info);
                    dol_syslog('Trigger, notify send mail to: '.json_encode($email), 7, 0, '_ca');
                    dol_syslog('Trigger, mail info: '.json_encode($mail), 7, 0, '_ca');
                    $rs = $this->sendEmail($mail);
                    if(!$rs) {
                        dol_syslog('Cannot send email to: '.$email, 7, 0, '_ca');
                    } else {
                        dol_syslog('Successful send email to: '.$email, 7, 0, '_ca');
                    }
                }
                break;
            case 'ORDER_APPROVE':
            case 'ORDER_SUPPLIER_APPROVE':
                // $emailTemplate = $this->getTemplateEmail('oc_approved_order_send');
                // $emailInfo = $this->getEmailInfo($userGroups['dev']);
                // $email = $this->makeEmail($emailTemplate, $emailInfo);
                // $this->sendEmail($email);
                break;
            case 'PROJECT_CREATE':
                $emailTemplate = $this->getTemplateEmail('oc_new_project_send');
                $emails = $this->getEmailInfo($userGroups['dev']);
                foreach ($emails as $email) {
                    $info = [
                        'sendto' => $email,
                        'replyto' => $email
                    ];
                    $mail = $this->makeEmail($emailTemplate, $info);
                    $rs = $this->sendEmail($mail);
                    if(!$rs) {
                        dol_syslog('Cannot send email to: '.$email, 7, 0, '_ca');
                    } else {
                        dol_syslog('Successful send email to: '.$email, 7, 0, '_ca');
                    }
                }
                break;
            default:
                break;
        }
            // Todo
        // $email = $this->makeEmail($emailTemplate, $emailInfo);
        // $this->sendEmail($email);
    }

    function getTemplateEmail($tempType)
    {
        $emailTemp = [
            'subject' => '',
            'message' => ''
        ];
        $sql = "SELECT rowid, label, topic, content, lang, position FROM ".MAIN_DB_PREFIX.'c_email_templates'." WHERE type_template='".$tempType."' AND active = 1 ORDER BY position,lang,label DESC LIMIT 0,1";
        $resql = $this->db->query($sql);
        if ($resql->num_rows < 1) {
            return $emailTemp;
        }
        $obj = $this->db->fetch_object($resql);
        $emailTemp = [
            'subject' => $obj->topic,
            'message' => $obj->content
        ];
        return $emailTemp;
    }

    function getEmailInfo($groupId)
    {
        $listEmail = [];
        $object = new Usergroup($this->db);
        $object->fetch($groupId);
        if (!empty($object->members))
        {
            foreach($object->members as $useringroup) {
                $email = $useringroup->email;
                // $listEmail[$useringroup->id] = $email;
                $listEmail[] = $email;
            }
        }
        return $listEmail;
    }

    function makeEmail($template, $info)
    {
        $subject = empty($template['subject']) ? 'Email send from system by event of Opencart' : $template['subject'];
        // $message = str_replace();
        $message = $template['message'];
        $email = [
            // 'subject' => str_replace('{subject}', $info['subject'],$template['subject']),
            'subject' => $subject,
            'sendto' => $info['sendto'],
            'replyto' => $info['replyto'],
            'message' => $message,
        ];
        return $email;
    }

    function sendEmail($email)
    {
        try {
            $mailfile = new CMailFile(
                $email['subject'],
                $email['sendto'],
                $email['replyto'],
                $email['message'],
                array(),
                array(),
                array(),
                '',
                '',
                0,
                -1
            );
        
            $rs = $mailfile->sendfile();
            return $rs;
        } catch (\Exception $e) {
            dol_syslog('Send email: '.$e->getMessage(), 7, 0, '_ca');
            return false;
        }
    }

}