<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
*/
class Building extends CI_Model{
    private $tableunits='units';
    function getUnits(){
        $this->db->get($this->tableunits);
        return $this->db->result_array();
    }
}