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

        // $date_start=dol_mktime(0,0,0,GETPOST('projectstartmonth','int'),GETPOST('projectstartday','int'),GETPOST('projectstartyear','int'));
        // $date_end=dol_mktime(0,0,0,GETPOST('projectendmonth','int'),GETPOST('projectendday','int'),GETPOST('projectendyear','int'));

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

        $projetId = $object->create($user);
        dol_syslog('Ca test here projetId: '.$projetId, 7, 0, '_ca');
        $devs = $this->getUserDev();
        dol_syslog('Ca test here devs: '.json_encode($devs), 7, 0, '_ca');
        if (!empty($devs)) {
            $result = [];
            foreach ($devs as $dev) {
                $result[] = $object->add_contact($dev['user_id'], $dev["type"], $dev["source"]);
            }
            dol_syslog('Rs of add contact to projet: '.json_encode($result), 7, 0, '_ca');
            // Create task and assign to first dev
            $now = time();
            $ref = 'TK'.date('ymd', $params['date_start']).'-PJ'.$projetId.'-'.$now;
            $mainJob = 'Build Website';
            $params = [
                'project_id' => $projetId,
                'ref' => $ref,
                'label' => $mainJob,
                'description' => '',
                'planned_workload' => 0,
                'fk_task_parent' => 0,
                'date_start' => date('Y-m-d H:i:s', $params['date_start']),
                'date_end' => date('Y-m-d H:i:s', $params['date_end']),
                'progress' => 0,
                // For contact
                'contact' => [
                    'user_id' => $devs[0]['user_id'],
                    'type' => 180, //180: Task executive, 181: Contributor
                    'source' => 'internal'
                ]
            ];
            dol_syslog('Start create task with params: '.json_encode($params), 7, 0, '_ca');
            $this->createTask($params);
        }
        return $result;
    }

    public function getUserDev() {
        $output = [];
        // Group dev id is 2
        $devGroupId = 2;
        $object = new Usergroup($this->db);
        $object->fetch($devGroupId);
        if (!empty($object->members))
        {
            foreach($object->members as $useringroup) {
                $output[] = [
                    'user_id' => $useringroup->id,
                    'type' => 161, // 160: Project leader; 161: Contributor
                    'source' => 'internal' // internal; external
                ];
            }
        }
        return $output;
    }

    public function createTask($params) {
        $task = new Task($this->db);

        $task->fk_project = $params['project_id'];
        $task->ref = $params['ref'];
        $task->label = $params['label'];
        $task->description = $params['description'];
        $task->planned_workload = $params['planned_workload'];
        $task->fk_task_parent = $params['fk_task_parent'];
        $task->date_c = dol_now();
        $task->date_start = $params['date_start'];
        $task->date_end = $params['date_end'];
        $task->progress = $params['progress'];
        $taskid = $task->create(DolibarrApiAccess::$user);
        dol_syslog('Task Id: '.$taskid, 7, 0, '_ca');
        // Add contact
        $ctParams = [
            $params['contact']['user_id'], 
            $params['contact']['type'], 
            $params['contact']['source']
        ];
        dol_syslog('Task ctParams: '.json_encode($ctParams), 7, 0, '_ca');
        $task->fetch($taskid, $task->ref);
        $rs = $task->add_contact($ctParams);
        dol_syslog('Task Assign rs: '.$rs, 7, 0, '_ca');
        return $taskid;
    }
}