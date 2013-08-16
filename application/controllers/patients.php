<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends MY_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Hospital/Building');
        $this->load->model('Hospital/Patient');
        $this->config->load('hospital');
    }
    function register(){
        $patient=$this->input->post();
        if(!empty($patient)){
            $patientid=$this->Patient->setpatient($patient['patientid'],$patient['patid'],$patient['names'],$patient['lastnames'],$patient['gender'],$patient['address'],$patient['bornday'],$patient['rh'],$patient['patientchar']);
            if(intval($patientid)>0){
                // verificar que no este siendo atendido, si es asi no registrar el triage y avisar?
                if(!$this->Patient->getState($patientid)){
                    //registrar el ingreso
                    $this->Patient->triagepatient($patientid);
                }
                $this->session->set_flashdata('patientid', $patientid);
            }
            redirect('patients/patientstatus');
        }
        $this->twig->display('hospital/register');
    }
    function patientstatus(){
        $patientid = $this->session->flashdata('patientid');
        
        $this->twig->display('hospital/patientstatus',array('patientid'=>$patientid));
    }
    function diet(){
        $this->twig->display('hospital/patientstatus',array('nopayinfo'=>true,'nomodify'=>true));
    }
    function searchpatient(){
        if(!$this->input->is_ajax_request()) redirect();
        $patientid=$this->input->post('patientid');
        $patient=$this->Patient->searchpatient($patientid);
        echo json_encode(array('patient'=>$patient));
    }
    function loadpatient(){
        if(!$this->input->is_ajax_request()) redirect();
        $patientid=$this->input->post('patientid');
        $patient=$this->Patient->loadpatient($patientid);
        echo json_encode(array('patient'=>$patient));
    }
    function loadpatients(){
        if(!$this->input->is_ajax_request()) redirect();
        $patients=$this->Patient->getpatientsinatention();
        echo json_encode(array('patients'=>$patients));
    }
    function getpatientstate(){
        if(!$this->input->is_ajax_request()) redirect();
        $patientid=$this->input->post('patient');
        $patientinfo=$this->Patient->loadpatient($patientid);
        $currentatention=$this->Patient->currentAtention($patientid);
        echo json_encode(array('basicinfo'=>$patientinfo,'atentioninfo'=>$currentatention));
    }
    function updatepatientinfo(){
        if(!$this->input->is_ajax_request()) redirect();
        $patient=$this->input->post();
        $res=false;
        if(!empty($patient)){
            $patientid=$this->Patient->setpatient($patient['patientid'],$patient['patid'],$patient['names'],$patient['lastnames'],$patient['gender'],$patient['address'],$patient['bornday'],$patient['rh'],$patient['patientchar']);
            if(intval($patientid)>0){
                $res=true;
            }
        }
        echo json_encode(array('ok'=>$res));
    }
    function updatepaymentinfo(){
        if(!$this->input->is_ajax_request()) redirect();
        $atention=$this->input->post();
        $res=false;
        if(!empty($atention)){
            $data=array(
                    'familiar'=>$atention['familiar'],
                    'familiar_phone'=>$atention['familiarphone'],
                    'insurance'=>$atention['insurance'],
                    'payment_method'=>$atention['payment']
            );
            $res=$this->Patient->updateAtention($atention['atentionid'],$data);
        }
        echo json_encode(array('ok'=>$res));
    }
}

/* End of file hospital.php */
/* Location: ./application/controllers/welcome.php */