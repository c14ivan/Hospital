<?php
$config['permission']['db_table_prefix']='';

if (!defined('CONTEXT_HOME')) {
    define('CONTEXT_HOME',0);
}
if (!defined('CONTEXT_SYSTEM')) {
    define('CONTEXT_SYSTEM',10);
}
if (!defined('CONTEXT_USER')) {
    define('CONTEXT_USER',20);
}
if (!defined('CONTEXT_MODULE')) {
    define('CONTEXT_MODULE',30);
}
if (!defined('CONTEXT_SUBMODULE')) {
    define('CONTEXT_SUBMODULE',40);
}

// used to defined the mode of installation.
$config['permission']['permissions_mode']='role';//role,weight

//default role for everybody in home
$config['default-role']='visitor';
//positions for menus
$config['permission']['menu_positions']=array('left-bar','top','bottom','mini-top','status');

//default roles to be created, could be created in the application
$config['permission']['roles']=array(
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Pharmacy','weight'=>30,'shortname'=>'pharmacy','description'=>''),
        array('name'=>'Doctor','weight'=>30,'shortname'=>'doctor','description'=>''),
        array('name'=>'Assistant','weight'=>30,'shortname'=>'assistant','description'=>''),
        array('name'=>'Diet','weight'=>10,'shortname'=>'diet','description'=>''),
        );
// list of capabilities for the application. If a permission is not defined, the application
// allows permission.
$config['permission']['capabilities']=array(
        'auth/view' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin','icon'=>'iconh-user'),
        'auth/add' => array('weight'=>30, 'visible'=>false,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'permission/admin_roles' => array('weight'=>30, 'visible'=>false,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'permission/create_role' => array('weight'=>30, 'visible'=>false,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'permission/update' => array('weight'=>30, 'visible'=>false,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'permission/enrolments' => array('weight'=>30, 'visible'=>false,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'patients/register' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'assistant,admin,doctor','icon'=>'iconh-register'),
        'patients/patientstatus' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'assistant,admin,doctor','icon'=>'iconh-searchpatient'),
        'patients/pharmacy' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'pharmacy','icon'=>'iconh-inventory'),
        'patients/diet' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'admin,diet','icon'=>'iconh-diet'),
        'patients/triageatention'=>array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'admin,doctor','icon'=>'iconh-triage'),
        'hospital/units' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'admin','icon'=>'iconh-building'),
        'hospital/rooms' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'admin','icon'=>'iconh-rooms'),
        'hospital/roomreport' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'admin','icon'=>'iconh-maletin'),
        );