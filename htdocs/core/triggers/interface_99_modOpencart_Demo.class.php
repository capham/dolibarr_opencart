<?php
/* Copyright (C) 2017 Ca Pham <capham.vn@gmail.com>
 *
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
 *  \file       htdocs/core/triggers/interface_99_modOpencart_Nofitication.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *                                         ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for demo module
 */
class InterfaceDemo extends DolibarrTriggers
{

    public $family = 'opencart';
    public $picto = 'technic';
    public $description = "Trigger for module opencart.";
    public $version = self::VERSION_DOLIBARR;

    /**
     * Function called when a Dolibarrr business event is done.
     * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
     *
     * @param string        $action     Event action code
     * @param Object        $object     Object concerned. Some context information may also be provided into array property object->context.
     * @param User          $user       Object user
     * @param Translate     $langs      Object langs
     * @param conf          $conf       Object conf
     * @return int                      <0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
        if (!$conf->opencart->enabled) {
            return 0;
        }
        dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

        require_once DOL_DOCUMENT_ROOT .'/opencart/class/ocnotify.class.php';

        $notify = new OcNotify($this->db);
        $notify->send($action, $object);
        

        return 1;
    }

    public function notification()
    {
        // Send email
        $subject = 'Ca test send mail via trigger';
        $sendto = 'vanca.vnn@gmail.com';
        $from = 'Ca Pham <capham.vn@gmail.com>';
        $message = 'Content email';
        $sendtocc = '';
        $sendtobcc = '';
        $deliveryreceipt = '';
        $replyto = 'Carl Pham <vanca.vnn@gmail.com>';
        $trackid = '';
        dol_syslog('Start send email: ...', 7, 0, '_ca');
        include DOL_DOCUMENT_ROOT.'/opencart/sendmail.php';
        dol_syslog('End send email.', 7, 0, '_ca');
    }

}
