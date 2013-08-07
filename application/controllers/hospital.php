<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hospital extends MY_Controller {

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
    }
    function units(){
        $unidades=$this->Building->getUnits();
        
        $headers= array(
                'name'=>array('priority'=>1,'valor'=>lang('name')),
                'description'=>array('priority'=>2,'valor'=>lang('description')),
        );
        $data=array(
                'headers'=>$headers,
                'data'=>$unidades,
                'hasactions'=>true,
                'canedit'=>$this->Permissions->has_permission('hospital/units'),
                'canadd'=>$this->Permissions->has_permission('hospital/units'),
                'candel'=>$this->Permissions->has_permission('hospital/units'),
                'urldel'=>site_url("hospital/delunit"),
                'urladd'=>site_url("hospital/editunit"),
                'urledit'=>site_url("hospital/editunit"),
                'delajax'=>true,
                'icon'=>'user',
                'title'=>lang('units'),
        );
         
        $this->twig->display('general/table',$data);
    }
    function rooms(){
        $rooms=$this->Building->getRooms();
        
        $headers= array(
                'unit'=>array('priority'=>1,'valor'=>lang('unit')),
                'name'=>array('priority'=>2,'valor'=>lang('name')),
                'description'=>array('priority'=>3,'valor'=>lang('description')),
        );
        $data=array(
                'headers'=>$headers,
                'data'=>$rooms,
                'hasactions'=>true,
                'canedit'=>$this->Permissions->has_permission('hospital/rooms'),
                'canadd'=>$this->Permissions->has_permission('hospital/rooms'),
                'candel'=>$this->Permissions->has_permission('hospital/rooms'),
                'urldel'=>site_url("hospital/delroom"),
                'urladd'=>site_url("hospital/editroom"),
                'urledit'=>site_url("hospital/editroom"),
                'delajax'=>true,
                'icon'=>'book',
                'title'=>lang('rooms'),
        );
         
        $this->twig->display('general/table',$data);
    }
    function diet(){
        
    }
    function inventory(){
        
    }
    function consult(){
        
    }
    function register(){
        
    }
    function editunit($unitid=0){
        $post=$this->input->post();
        if(!empty($post)){
            if($post['id']>0){
                $this->Building->updateUnit($post['id'],$post['name'],$post['description']);
            }else{
                $this->Building->addUnit($post['name'],$post['description']);
            }
            redirect('hospital/units');
        }
        $name='';
        $desc='';
        if($unitid>0){
            $unit=$this->Building->getUnit($unitid);
            $name=$unit->name;
            $desc=$unit->description;
        }
        
        $data=array(
                'icon'=>'th',
                'title'=> lang('units'),
                'campos'=>array(
                        'id'=>array('tipo'=>'input','type'=>'hidden','name'=>'id','value'=>$unitid),
                        'name'=>array('tipo'=>'input','type'=>'text','name'=>'name','label'=>lang('unit'),'value'=>$name),
                        'description'=>array('tipo'=>'textarea','type'=>'textarea','name'=>'description','label'=>lang('description'),'value'=>$desc),
                ),
                'reglas'=>array(
                        'name'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('required','')),
                        ),
                        'description'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('required')),
                        ),
                )
        );
        if(isset($errors) && !empty($errors)){
            $data['errors']=$errors;
        }
        $this->twig->display('general/edit',$data);
    }
    function delunit(){
        if(!$this->input->is_ajax_request()) redirect();
        $unitid=$this->input->post('id');
        $val=$this->Building->deleteUnit($unitid);
        $msj=($val)?lang('deleteok'):lang('operationfails');
        $response=array('msj'=>lang('deleteok'),'id'=>$unitid);
        
        echo json_encode($response);
    }
    
    function editroom($roomid=0){
        $post=$this->input->post();
        if(!empty($post)){
            if($post['id']>0){
                $this->Building->updateRoom($post['id'],$post['name'],$post['description'],$post['unit']);
            }else{
                $this->Building->addRoom($post['name'],$post['description'],$post['unit']);
            }
            redirect('hospital/rooms');
        }
        $name='';
        $desc='';
        if($roomid>0){
            $room=$this->Building->getRoom($roomid);
            $name=$room->name;
            $desc=$room->description;
            $unit=$room->unitid;
        }
    
        $units=$this->Building->getUnits();
        $unitsarr=array();
        if(count($units)>0){
            foreach ($units as $unit){
                $unitsarr[$unit['id']]=$unit['name'];
            }
        }
        $data=array(
                'icon'=>'th',
                'title'=> lang('units'),
                'campos'=>array(
                        'id'=>array('tipo'=>'input','type'=>'hidden','name'=>'id','value'=>$roomid),
                        'name'=>array('tipo'=>'input','type'=>'text','name'=>'name','label'=>lang('roomid'),'value'=>$name),
                        'description'=>array('tipo'=>'textarea','type'=>'textarea','name'=>'description','label'=>lang('description'),'value'=>$desc),
                        'unit'=>array('tipo'=>'select','label'=>lang('unit'),'name'=>'unit','opciones'=>$unitsarr)
                ),
                'reglas'=>array(
                        'name'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('required','')),
                        ),
                        'description'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('required'),''),
                        ),
                        'unit'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('required'),''),
                        ),
                )
        );
        if(isset($errors) && !empty($errors)){
            $data['errors']=$errors;
        }
        $this->twig->display('general/edit',$data);
    }
    function delroom(){
        if(!$this->input->is_ajax_request()) redirect();
        $unitid=$this->input->post('id');
        $val=$this->Building->deleteRoom($unitid);
        $msj=($val)?lang('deleteok'):lang('operationfails');
        $response=array('msj'=>lang('deleteok'),'id'=>$unitid);
    
        echo json_encode($response);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */