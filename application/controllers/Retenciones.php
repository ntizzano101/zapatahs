<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retenciones extends CI_Controller {

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
     * @see https://codeigniter.com/user_guide/general/urls.html
     * 
     * 
     * 
     */   
    public function __construct()
    {
        parent::__construct();
            if(!isset($this->session->usuario)){
                redirect('salir');
                exit;
            }		
    }
    
     ##CLIENTES
    public function index()
    {
        $this->load->model('retenciones_model');
        $tipo=$this->input->post('tipo');
        if($tipo==""){$tipo=3;}
        $fdesde=$this->input->post('fdesde');
        if($fdesde==""){$fdesde=date("Y-m-d");}
        $fhasta=$this->input->post('fhasta');
        if($fhasta==""){$fhasta=date("Y-m-d");}
        $data["retenciones"]=$this->retenciones_model->listado($tipo,$fdesde,$fhasta);
        $data["tipo"]=$tipo;
        $data["fdesde"]=$fdesde;
        $data["fhasta"]=$fhasta;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');          
        $this->load->view('retenciones/retenciones.php',$data);
        
    }
    
      
    
}    

?>