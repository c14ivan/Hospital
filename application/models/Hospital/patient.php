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
    function currentAtended($doctorid){
        $this->db->where('doctor',$doctorid);
        $this->db->where('status',0);
        $query=$this->db->get($this->tableatention);
        $response=false;
        if ($query->num_rows() > 0){
            $response = $query->row();
        }
        return $response;
    }
    function currentAtention($patid){
        $this->db->select("atentionid,entrancedate,roomnumber,familiar,familiar_phone,doctor,insurance,priority,payment_method,{$this->tableunits}.name as unitname");
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
    function getAtention($atentionid){
        $this->db->where('atentionid',$atentionid);
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
    /**
     * 
     * @param number $hospitalized if this is zero then returns all patients in atention
     *                             where it's one then return the hospitalized patients
     *                             where it's two then return just the triage patients
     * @return boolean
     */
    function getpatientsinatention($hospitalized=0){
        $this->db->select("{$this->tableatention}.patientid,{$this->tablepatients}.name,lastname,patid,chars,entrancedate,priority,roomnumber,{$this->tableunits}.name as unitname");
        $this->db->join($this->tableatention,"{$this->tableatention}.patientid={$this->tablepatients}.patientid AND {$this->tableatention}.status=0","INNER");
        $this->db->join($this->tablerooms,"{$this->tableatention}.roomid={$this->tablerooms}.roomid","LEFT");
        $this->db->join($this->tableunits,"{$this->tablerooms}.unitid={$this->tableunits}.unitid","LEFT");
        if($hospitalized==1){
            $this->db->where("{$this->tableatention}.roomid IS NOT NULL");
        }elseif($hospitalized==2){
            $this->db->where("{$this->tableatention}.roomid IS NULL");
        }
        $query=$this->db->get($this->tablepatients);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    function updateAtention($atentionid,$attrs){
        if(intval($atentionid)>0){
            $this->db->where('atentionid', $atentionid);
            $this->db->update($this->tableatention, $attrs);
            return ($this->db->affected_rows()==1)?true:false;
        }
        return false;
    }
    function asignDoctor($atentionid,$doctorid){
        $this->db->where('atentionid',$atentionid);
        $this->db->update($this->tableatention, array('doctor'=>$doctorid));
        return ($this->db->affected_rows()==1)?true:false;
    }
    function getDiagnosis($diagnosisid){
        $this->db->where('diagnosisid',$diagnosisid);
        $query=$this->db->get($this->tableadiagnosis);
        if($query->num_rows()>0){
            return $query->row();
        }else{
            return false;
        }
    }
    function diagnosis($atentionid,$symptoms,$treatment,$doctor,$room){
        $this->db->where('atentionid',$atentionid);
        $this->db->where('roomid',$room);
        $queryprev=$this->db->get($this->tableadiagnosis);
        if($queryprev->num_rows()>0){
            return false;
        }else{
            $data=array(
                    'atentionid'=>$atentionid,
                    'roomid'=>$room,
                    'symptoms'=>$symptoms,
                    'treatment'=>$treatment,
                    'doctor'=>$doctor,
                    'diagnosistime'=>date('Y-m-d H:i:s'),
            );
            if($this->db->insert($this->tableadiagnosis, $data)){
                return $this->db->insert_id();
            }
            return false;
        }
    }
    function getAvailableRoom($unitid,$roomtype){
        //obtener los cuartos ocupados con las caracteristicas dadas
        $this->db->select("{$this->tablerooms}.roomid,{$this->tablerooms}.roomnumber");
        $this->db->join($this->tableatention,"{$this->tableatention}.status=0 AND {$this->tableatention}.roomid = {$this->tablerooms}.roomid");
        $this->db->join($this->tableunits,"{$this->tableunits}.unitid={$this->tablerooms}.unitid");
        $this->db->where("{$this->tableunits}.unitid",$unitid);
        $this->db->where("{$this->tablerooms}.roomtype",$roomtype);
        $query=$this->db->get($this->tablerooms);
        $busyrooms=array();
        if($query->num_rows()>0){
            $busyrooms=$query->result_array();
        }
        $roomsnotin=array();
        foreach ($busyrooms as $busyroom){
            $roomsnotin[]=$busyroom['roomid'];
        }
        $this->db->select("{$this->tablerooms}.roomid,{$this->tablerooms}.roomnumber");
        $this->db->join($this->tableunits,"{$this->tableunits}.unitid={$this->tablerooms}.unitid");
        $this->db->where("{$this->tableunits}.unitid",$unitid);
        $this->db->where("{$this->tablerooms}.roomtype",$roomtype);
        if(!empty($roomsnotin)){
            $this->db->where_not_in("{$this->tablerooms}.roomid",$roomsnotin);
        }
        $queryavailable=$this->db->get($this->tablerooms);
        if($queryavailable->num_rows()>0){
            return $queryavailable->row();
        }else{
            return false;
        }
    }
}