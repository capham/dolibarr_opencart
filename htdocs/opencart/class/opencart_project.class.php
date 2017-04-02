<?php
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/project/modules_project.php';

class OpencartProject
{
    public function __construct() {
        global $db, $conf;
        $this->db = $db;
        $this->conf = $conf;
    }


    public function create($params = array()) {
        $db = $this->db;
        $user = $params['user'];
        $object = new Project($db);

        $date_start=dol_mktime(0,0,0,GETPOST('projectstartmonth','int'),GETPOST('projectstartday','int'),GETPOST('projectstartyear','int'));
        $date_end=dol_mktime(0,0,0,GETPOST('projectendmonth','int'),GETPOST('projectendday','int'),GETPOST('projectendyear','int'));

        $classname = 'mod_project_simple';
        require_once DOL_DOCUMENT_ROOT.'/core/modules/project/'.$classname.'.php';
        $modProject = new $classname;
        $thirdparty=new Societe($db);
        $defaultref = $modProject->getNextValue($thirdparty,$object);

        $object->ref             = $defaultref;
        $object->title           = $params['title'];
        $object->socid           = $params['socid'];// Carl Pham Customer
        $object->description     = $params['description'];
        $object->public          = $params['public'];// Project contacts
        $object->opp_amount      = price2num($params['opp_amount']);
        $object->budget_amount   = price2num($params['budget_amount']);
        $object->datec=dol_now();
        $object->date_start=$params['date_start'];
        $object->date_end=$params['date_end'];
        $object->statut         = $params['statut'];
        $object->opp_status      = $params['opp_status'];//1.Prospection, 2.Qualification, 3.Proposal, 4.Negociation, 6.Won, 7.Lost
        $object->opp_percent     = $params['opp_percent'];

        $result = $object->create($user);
        return $result;
    }
}