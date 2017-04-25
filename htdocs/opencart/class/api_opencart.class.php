<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
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

use Luracast\Restler\RestException;


/**
 * API class for opencart object
 *
 * @smart-auto-routing true
 * @access protected 
 * @class  DolibarrApiAccess {@requires user,external}
 * 
 */
class Opencart extends DolibarrApi
{
    /**
     * Constructor
     * 
     */
    function __construct() {
        global $db, $conf;
        $this->db = $db;
    }
    
    /**
     * API Opencart
     *
     * @return  array|mixed data without useless information
     * @url GET /info
     * @throws  RestException
     *
     */
    function info() {
        return array(
            'success' => array(
                'code' => 200,
                'message' => 'Opencart module activated.'
            )
        );
    }

    /**
     * Create user account
     *
     * @param array $request_data New user data
     * @return int
     *
     * @url POST /projects
     */
    function createProject($requestData = NULL) {
        dol_syslog('Start create projet in Dol: '.json_encode($requestData), 7, 0, '_ca');
        
        require_once DOL_DOCUMENT_ROOT.'/opencart/class/opencart_project.class.php';
        $ocProject = new OpencartProject();
        /*$params = array(
            'user' => $user,
            'title' => 'Project test',
            'socid' => 9,
            'description' => '',
            'public' => '',
            'opp_amount' => 100,
            'budget_amount' => 100,
            'date_start' => dol_mktime(0,0,0,1,1,2017),
            'date_end' => dol_mktime(0,0,0,12,31,2017),
            'statut' => 1,
            'opp_status' => 1,
            'opp_percent' => 50,
        );*/
        $user = DolibarrApiAccess::$user;
        $params = array(
            'user' => $user,
            'title' => $requestData['title'],
            'socid' => $requestData['socid'],
            'description' => $requestData['description'],
            'public' => $requestData['public'],
            'opp_amount' => $requestData['opp_amount'],
            'budget_amount' => $requestData['budget_amount'],
            'date_start' => $requestData['date_start'],
            'date_end' => $requestData['date_end'],
            'statut' => 1,
            'opp_status' => 1,
            'opp_percent' => $requestData['opp_percent'],
        );
        $rs = $ocProject->create($params);
        dol_syslog('Result of create projet in Dol: '.json_encode($rs), 7, 0, '_ca');
        return [$rs];
    }

    /*public function getUser() {
        $user = new User($this->db);
        $user->fetch(2);
        return $user;
    }*/

}