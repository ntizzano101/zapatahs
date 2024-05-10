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
    
    public function exportar($sep,$fd,$fh,$itpo){
        //f3 formato de archivo
        $this->load->library('funciones');
        $this->load->model('retenciones_model');        
        $data=$this->retenciones_model->listado($itpo,$fd,$fh);
        $c="";
        foreach($data as $d){
            if($sep==1){
                $c.=$d->rete_fecha . ","  . $d->nro_comprobante . ","  . $d->monto . ","  . $d->cliente . ","  . $d->cuit . PHP_EOL ; 
            }   
            if($sep==2){         
                $c.=$d->rete_fecha . ";"  . $d->nro_comprobante . ";"  . str_replace(".",",",$d->monto) . ";"  . $d->cliente . ";"  . $d->cuit . PHP_EOL ; 
            }
        }

        $a1="exportar/retenciones_". $tipo."_".$fd.".csv";
        file_put_contents($a1,$c);
        $this->funciones->exportar_excel($a1);
    }   
    
}    

?>