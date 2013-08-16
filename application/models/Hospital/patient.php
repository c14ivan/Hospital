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
class Patient extends CI_Model{
    private $tablepatients='h_patient';
    private $tableatention='h_atention';
    private $tableadiagnosis='h_diagnosis';
    private $tablerooms='h_rooms';
    private $tableunits='h_units';
    
    function __construct()
    {
        parent::__construct();
        $this->lang->load('form_validation');
        $this->lang->load('hospital');
    }
    function loadpatient($patid){
        $this->db->where('patientid',$patid);
        $query=$this->db->get($this->tablepatients);
        $response=false;
        if ($query->num_rows() > 0){
            $response = $query->row();
        }
        return $response;
    }
    function currentAtention($patid){
        $this->db->select("atentionid,entrancedate,roomnumber,familiar,familiar_phone,insurance,priority,payment_method,{$this->tableunits}.name as unitname");
        $this->db->where('patientid',$patid);
        $this->db->join($this->tablerooms,"{$this->tableatention}.roomid={$this->tablerooms}.roomid","LEFT");
        $this->db->join($this->tableunits,"{$this->tablerooms}.unitid={$this->tableunits}.unitid","LEFT");
        $query=$this->db->get($this->tableatention);
        $response=false;
        if ($query->num_rows() > 0){
            $response = $query->row();
        }
        return $response;
    }
    function searchpatient($patientid){
        $this->db->where('patid',$patientid);
        $query=$this->db->get($this->tablepatients);
        $response=false;
        if ($query->num_rows() > 0){
            $response = $query->row();
        }
        return $response;
    }
    function setpatient($id=0,$patid='',$firstname='',$lastname='',$gender='',$address='',$bornday='',$rh='',$chars=''){
        if($id==0 && $firstname=='' && $lastname=='' && $chars==''){
            return false;
        }
        $data=array();
        if($patid!=''){
            $data['patid']=$patid;
        }
        if($firstname!=''){
            $data['name']=$firstname;
        }
        if($lastname!=''){
            $data['lastname']=$lastname;
        }
        if($gender!=''){
            $data['gender']=$gender;
        }
        if($address!=''){
            $data['address']=$address;
        }
        if($bornday!=''){
            $data['bornday']=$bornday;
        }
        if($rh!=''){
            $data['rh']=$rh;
        }
        if($chars!=''){
            $data['chars']=$chars;
        }
        
        if($id>0){
            $this->db->where('patientid', $id);
            $this->db->update($this->tablepatients, $data);
            
            return $id;
        }else{
            if($this->db->insert($this->tablepatients, $data)){
                return $this->db->insert_id();
            }
            return false;
        }
    }
    function triagepatient($patientid){
        $data=array('patientid'=>$patientid,'entrancedate'=>date('Y-m-d H:i:s'));
        if($this->db->insert($this->tableatention, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function getState($patientid){
        $this->db->where('patientid',$patientid);
        $this->db->where('status',0);
        $query=$this->db->get($this->tableatention);
        if($query->num_rows()>0){
            return $query->row();
        }else{
            return false;
        }
    }
    function getpatientsinatention(){
        $this->db->select("{$this->tableatention}.patientid,{$this->tablepatients}.name,lastname,patid,entrancedate,priority,roomnumber,{$this->tableunits}.name as unitname");
        $this->db->join($this->tableatention,"{$this->tableatention}.patientid={$this->tablepatients}.patientid AND {$this->tableatention}.status=0","INNER");
        $this->db->join($this->tablerooms,"{$this->tableatention}.roomid={$this->tablerooms}.roomid","LEFT");
        $this->db->join($this->tableunits,"{$this->tablerooms}.unitid={$this->tableunits}.unitid","LEFT");
        $query=$this->db->get($this->tablepatients);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    function updateAtention($atentionid,$attrs){
        $this->db->where('atentionid', $atentionid);
        $this->db->update($this->tableatention, $attrs);
        return ($this->db->affected_rows()==1)?true:false;
    }
}