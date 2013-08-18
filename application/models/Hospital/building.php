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
    private $tableatention='h_atention';
    
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
        $this->db->where('unitid', $id);
        $query=$this->db->get($this->tableunits);
        return $query->row();
    }
    function addUnit($name){
        $data = array(
                'name' => $name,
        );
        
        if($this->db->insert($this->tableunits, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateUnit($id,$name){
        $data = array(
                'name' => $name,
        );
        $this->db->where('unitid', $id);
        $this->db->update($this->tableunits, $data);
        
        return ($this->db->affected_rows()==1)?true:false;
    }
    function deleteUnit($id){
        $this->db->delete($this->tableunits, array('unitid'=>$id));
        
        return ($this->db->affected_rows()==1)?true:false;
    }
    function getRooms(){
        $this->db->select("{$this->tablerooms}.roomid as id,{$this->tableunits}.name as unit,{$this->tablerooms}.roomnumber,{$this->tablerooms}.roomtype");
        $this->db->join($this->tableunits,"{$this->tableunits}.unitid={$this->tablerooms}.unitid");
        $query=$this->db->get($this->tablerooms);
        $response=array();
        if ($query->num_rows() > 0){
            $response = $query->result_array();
        }
        return $response;
    }
    function getRoom($id){
        $this->db->where('roomid', $id);
        $query=$this->db->get($this->tablerooms);
        return $query->row();
    }
    function addRoom($name,$type,$unit){
        $data = array(
                'roomnumber' => $name,
                'roomtype' => $type,
                'unitid'=>$unit
        );
    
        if($this->db->insert($this->tablerooms, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateRoom($id,$name,$type,$unit){
        $data = array(
                'roomnumber' => $name,
                'roomtype' => $type,
                'unitid'=>$unit,
        );
        $this->db->where('roomid', $id);
        $this->db->update($this->tablerooms, $data);
    
        return ($this->db->affected_rows()==1)?true:false;
    }
    function deleteRoom($id){
        $this->db->delete($this->tablerooms, array('roomid'=>$id));
        
        return ($this->db->affected_rows()==1)?true:false;
    }
    function getOcupation(){
        $this->db->select("{$this->tableunits}.unitid,name,roomtype,count(roomtype) as total, count(atentionid) as busy");
        $this->db->join($this->tableunits,"{$this->tablerooms}.unitid={$this->tableunits}.unitid","INNER");
        $this->db->join($this->tableatention,"{$this->tableatention}.status=0 AND {$this->tableatention}.roomid={$this->tablerooms}.roomid","LEFT");
        $this->db->group_by("name,roomtype");
        $query=$this->db->get($this->tablerooms);
        $data= $query->result_array();
        $response=array();
        foreach($data as $row){
            if(!isset($response[$row['unitid']])){
                $response[$row['unitid']]=array('name'=>$row['name'],'rooms'=>0,'busy'=>0,'roomtypes'=>array());
            }
            
            $response[$row['unitid']]['rooms']+=$row['total'];
            $response[$row['unitid']]['busy']+=$row['busy'];
            $response[$row['unitid']]['roomtypes'][$row['roomtype']]=array('total'=>$row['total'],'busy'=>$row['busy']);
        }
        return $response;
    }
}