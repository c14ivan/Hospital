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
    private $tableunits='h_units';
    private $tablerooms='h_rooms';
    
    function __construct()
    {
        parent::__construct();
        $this->lang->load('form_validation');
        $this->lang->load('hospital');
    }
    
    function getUnits(){
        $query=$this->db->get($this->tableunits);
        $response=array();
        if ($query->num_rows() > 0){
            $response = $query->result_array();
        }
        return $response;
    }
    function getUnit($id){
        $this->db->where('id', $id);
        $query=$this->db->get($this->tableunits);
        return $query->row();
    }
    function addUnit($name,$description){
        $data = array(
                'name' => $name,
                'description' => $description,
        );
        
        if($this->db->insert($this->tableunits, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateUnit($id,$name,$description){
        $data = array(
                'name' => $name,
                'description' => $description,
        );
        $this->db->where('id', $id);
        $this->db->update($this->tableunits, $data);
        
        return ($this->db->affected_rows()==1)?true:false;
    }
    function deleteUnit($id){
        $this->db->delete($this->tableunits, array('id'=>$id));
        
        return ($this->db->affected_rows()==1)?true:false;
    }
    function getRooms(){
        $this->db->select("{$this->tablerooms}.id,{$this->tableunits}.name as unit,{$this->tablerooms}.name,{$this->tablerooms}.description");
        $this->db->join($this->tableunits,"{$this->tableunits}.id={$this->tablerooms}.unitid");
        $query=$this->db->get($this->tablerooms);
        $response=array();
        if ($query->num_rows() > 0){
            $response = $query->result_array();
        }
        return $response;
    }
    function getRoom($id){
        $this->db->where('id', $id);
        $query=$this->db->get($this->tablerooms);
        return $query->row();
    }
    function addRoom($name,$description,$unit){
        $data = array(
                'name' => $name,
                'description' => $description,
                'unitid'=>$unit
        );
    
        if($this->db->insert($this->tablerooms, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateRoom($id,$name,$description,$unit){
        $data = array(
                'name' => $name,
                'description' => $description,
                'unitid'=>$unit,
        );
        $this->db->where('id', $id);
        $this->db->update($this->tablerooms, $data);
    
        return ($this->db->affected_rows()==1)?true:false;
    }
    function deleteRoom($id){
        $this->db->delete($this->tablerooms, array('id'=>$id));
        
        return ($this->db->affected_rows()==1)?true:false;
    }
}