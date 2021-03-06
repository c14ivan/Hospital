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
        $this->load->model('tank_auth/Users');
        $this->config->load('hospital');
        $this->load->library('Pdf');
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
    function pharmacy(){
        $this->twig->display('hospital/patientstatus');
    }
    function diet(){
        $this->twig->display('hospital/patientstatus',array('nopayinfo'=>true,'nomodify'=>true,'justhospitalized'=>true));
    }
    function atentionreport(){
        $diagnosisid=$this->uri->segment(3);
        $diagnosis=$this->Patient->getDiagnosis($diagnosisid);
        $atention=$this->Patient->getAtention($diagnosis->atentionid);
        $patientinfo=$this->Patient->loadpatient($atention->patientid);
        $diagnosis->doctor=$this->Users->get_user_by_id($diagnosis->doctor,true);
        
        $pdf = new Pdf('L', 'mm', 'A5', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        $pdf->SetTitle(lang('diagnosisreporttitle'));
        $pdf->SetTopMargin(10);
        $pdf->SetAutoPageBreak(true);
        $pdf->SetAuthor($this->config->item('apptitle'));
        $pdf->SetDisplayMode('real', 'default');
        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);
        
        // set text shadow effect
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        // set font
        //$pdf->SetFont('times', 'BI', 20);
        $pdf->SetFont('dejavusans', '', 8, '', true);
        // add a page
        $pdf->AddPage('L', 'A5');
        // Set some content to print
        $html = $this->twig->getDisplay('hospital/atentionreport',array('patientinfo'=>$patientinfo,'atention'=>$atention,'diagnosis'=>$diagnosis));
        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('report.pdf', 'I');
    }
    function triageatention(){
        $units=$this->Building->getUnits();
        $unitsarr=array();
        if(count($units)>0){
            foreach ($units as $unit){
                $unitsarr[$unit['id']]=$unit['name'];
            }
        }
        $currentpatient=$this->Patient->currentAtended($this->session->userdata('user_id'));
        $this->twig->display('hospital/patienttriage',
                array(
                    'units'=>$unitsarr,
                    'roomtypes'=>$this->config->item('roomtypes', 'hospital'),
                    'currentpatient'=>($currentpatient)?$currentpatient->patientid:false
                )
            );
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
        $hosp=$this->input->post('hospitalized');
        $justhospitalized=(isset($hosp))?intval($hosp):0;
        $patients=$this->Patient->getpatientsinatention($justhospitalized);
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
        $data=array();
        if(!empty($atention)){
            $data=array(
                    'familiar'=>$atention['familiar'],
                    'familiar_phone'=>$atention['familiarphone'],
                    'insurance'=>$atention['insurance'],
                    'payment_method'=>$atention['payment']
            );
            $res=$this->Patient->updateAtention($atention['atentionid'],$data);
        }
        echo json_encode(array('ok'=>$res,'data'=>$data,'post'=>$atention));
    }
    function atendpatient(){
        if(!$this->input->is_ajax_request()) redirect();
        $patientid=$this->input->post('patient');
        $asign=$this->input->post('asign')=="false"?false:true;
        $patientinfo=$this->Patient->loadpatient($patientid);
        $currentatention=$this->Patient->currentAtention($patientid);
        $asigned=false;
        $othdoctor=false;
        if($asign){
            if($currentatention->doctor>0){
                $atend=false;
                $othdoctor=true;
            }else{
                $atend=$this->Patient->asignDoctor($currentatention->atentionid,$this->session->userdata('user_id'));
                $asigned=true;
            }
        }else{
            $atend=true;
        }
        
        echo json_encode(array('ok'=>$atend,'prev'=>$asigned,'asigned'=>$othdoctor,'basicinfo'=>$patientinfo,'atentioninfo'=>$currentatention));
    }
    function diagnosepatient(){
        if(!$this->input->is_ajax_request()) redirect();
        $diagnosis=$this->input->post();
        $atention=$this->Patient->getAtention($diagnosis['atentionid']);
        $adddiagnosis=true;
        $room=false;
        $url='';
        if(isset($diagnosis['tohospital'])){//Si necesita hospitalizar se reasigna
            //buscar habitación:
            $room=$this->Patient->getAvailableRoom($diagnosis['unit'],$diagnosis['roomtype']);
            if($room){
                $this->Patient->updateAtention($diagnosis['atentionid'],array('roomid'=>$room->roomid,'doctor'=>0,'priority'=>$diagnosis['priority']));
            }else{
                $adddiagnosis=false;
            }
        }else{//sino se cierra
            $this->Patient->updateAtention($diagnosis['atentionid'],array('status'=>1,'priority'=>$diagnosis['priority']));
        }
        $okdiagnosis=false;
        //agregar diagnostico
        if($adddiagnosis){
            $okdiagnosis=$this->Patient->diagnosis($diagnosis['atentionid'],$diagnosis['symptoms'],$diagnosis['treatment'],$this->session->userdata('user_id'),$atention->roomid);

            $url=anchor('patients/atentionreport/'.$okdiagnosis,lang('diagnosisreport'),array('target'=>"_blank"));
        }
        echo json_encode(array('ok'=>$adddiagnosis,'link'=>$url,'room'=>$room,'needroom'=>(isset($diagnosis['tohospital']))?$diagnosis['tohospital']:false,'diagnosisok'=>$okdiagnosis));
    }
}

/* End of file hospital.php */
/* Location: ./application/controllers/welcome.php */