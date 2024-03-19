<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

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
       
        $this->load->model('ventas_model');
        $d=date("Y-m-d");
        $data["facturas"]=$this->ventas_model->listado("",$d,$d);
        $data["buscar"]="";
        $data["fdesde"]=$d;
        $data["fhasta"]=$d;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('ventas/facturas.php',$data);

    }
    
    public function buscar()
    {
        $this->load->library('session');
        $session=$this->session;
        $buscar=$this->input->post('buscar');
        $fdesde=$this->input->post('fdesde'); 
        if($session->has_userdata('fdesde')){$session->fdesde;}else{$fdesde=date("Y-m-d");}               
        $fhasta=$this->input->post('fhasta');        
        if($session->has_userdata('fhasta')){$session->fhasta;}else{$fhasta=date("Y-m-d");}       
        $this->load->model('ventas_model');
        $data["facturas"]=$this->ventas_model->listado($buscar,$fdesde,$fhasta);
        $data["fdesde"]=$fdesde;
        $data["fhasta"]=$fhasta;
        $newdata = [
            'buscar'  => $buscar,
            'fdesde'     => $fdesde,
            'fhasta' => $fhasta
        ];        
        $session->set_userdata($newdata);
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('ventas/facturas.php',$data);

    }
    
    public function ingresar()
    {
        $this->load->model('ventas_model');
        $this->load->model('facturas_model');
        $obj = new stdClass();
        $obj->empresa="";
        $obj->cliente="";
        $obj->factnro1="";
        $obj->factnro2="";
        $obj->fecha=date('Y-m-d');
        $obj->periva="";
        $obj->cod_afip="";
        $obj->formaPago="";
        $obj->intImpNeto="";
        $obj->intImpExto="";
        $obj->intIva="";
        $obj->intPerIngB="";
        $obj->intPerIva="";
        $obj->intPerGnc="";
        $obj->intConNoGrv="";
        $obj->obs="";
        $obj->cuit="";
        $obj->items="[]";       
        
        $data["factura"]=$obj;
        $data["lista_clientes"]=$this->ventas_model->lista_clientes();
        $data["lista_empresas"]=$this->ventas_model->lista_empresas();
        $bancos=$this->facturas_model->cmb_cbus();
        $data["cbu"]=0;        
        $comps_asoc=$this->facturas_model->cmb_comps_asoc(0,0);
        $data["comps_asoc"]=$comps_asoc;
        $data["bancos"]=$bancos;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('ventas/facturas_grabar.php',$data);
    }
    
    public function grabar()
    {
        $this->load->model('ventas_model');        
        $obj = new stdClass();
        $obj->empresa=$this->input->post('empresa');
        $obj->cliente=trim($this->input->post('cliente'));
        $obj->factnro1=trim($this->input->post('factnro1'));
        $obj->factnro2=trim($this->input->post('factnro2'));
        $obj->fecha=$this->input->post('fecha');
        $obj->periva=substr($obj->fecha,0,4) . substr($obj->fecha,5,2);
        $obj->cod_afip=trim($this->input->post('cod_afip'));
        $obj->formaPago=trim($this->input->post('formaPago'));
        $obj->intImpNeto=trim($this->input->post('intImpNeto'));
        $obj->intIva=trim($this->input->post('intIva'));
        $obj->intPerIngB=trim($this->input->post('intPerIngB'));
        $obj->intPerIva=trim($this->input->post('intPerIva'));
        $obj->intPerGnc=trim($this->input->post('intPerGnc'));
        $obj->intPerStaFe=trim($this->input->post('intPerStaFe'));
        $obj->intImpExto=trim($this->input->post('intImpExto'));
        $obj->intConNoGrv=trim($this->input->post('intConNoGrv'));
        $obj->intTotal=trim($this->input->post('intTotal'));
        $obj->obs=trim($this->input->post('obs'));
        $obj->cuit=trim($this->input->post('cuit'));
        $obj->cbu=trim($this->input->post('cbu'));
        $obj->sdesde=trim($this->input->post('sdesde'));
        $obj->shasta=trim($this->input->post('shasta'));      
        $obj->id_comp_asoc=trim($this->input->post('id_comp_asoc'));
        $obj->items=trim($this->input->post('items'));       
        $obj->vence=trim($this->input->post('vfecha'));       
        $resultado=$this->ventas_model->guardar($obj);
        $data["mensaje"]='<div class="alert alert-success alert-dismissible" role="alert">'.
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
        '<span aria-hidden="true">&times;</span></button>'.
        'La factura se ha ingresado con éxito'.
        '</div>';
        $data["numero2"]=$resultado;
        $data["error"]="";
        $resp=json_decode(json_encode($data), true);
        $this->send($resp); 
        return 0;
        exit;            
        
    } 
    
    public function ver($id)
    {
        $this->load->model('ventas_model');
        $data["factura"]=$this->facturas_model->buscar($id);
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('ventas/facturas_ver.php',$data);
    }
    
    public function borrar($id)
    {
        
        
        $this->load->model('ventas_model');
        
        
        if ($this->ventas_model->borrar($id)){
            $data["mensaje"]='<div class="alert alert-success alert-dismissible" role="alert">'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
                '<span aria-hidden="true">&times;</span></button>'.
                'La factura '.$id.' se ha borrado con éxito'.
                '</div>';
        }else{
            $data["mensaje"]='<div class="alert alert-warning alert-dismissible" role="alert">'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
                '<span aria-hidden="true">&times;</span></button>'.
                'La factura  no se pudo borrar. Consulte con al administrador del Sistema'.
                '</div>';
        }
        $this->load->library('session');
        $session=$this->session;
        if($session->has_userdata('fdesde')){$fdesde=$session->fdesde;}else{$fdesde=date('Y-m-d');}
        if($session->has_userdata('fhasta')){$fhasta=$session->fhasta;}else{$fhasta=date('Y-m-d');}
        if($session->has_userdata('buscar')){$buscar=$session->buscar;}else{$buscar="";}
        $data["facturas"]=$this->ventas_model->listado($buscar,$fdesde,$fhasta);
        $data["buscar"]=$buscar;
        $data["fdesde"]=$fdesde;
        $data["fhasta"]=$fhasta;        
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('ventas/facturas.php',$data);
    }
    

    public function listar()
    {
       
        $this->load->model('ventas_model');
        $this->load->library('session');
        $session=$this->session;

        $data["facturas"]=$this->ventas_model->listado("",date("Y-m-d"),date("Y-m-d"));
        $data["fdesde"]=date("Y-m-d");
        $data["fhasta"]=date("Y-m-d");
        $newdata = [
            'buscar'  => "",
            'fdesde'     => date("Y-m-d"),
            'fhasta' => date("Y-m-d")
        ];        
        $session->set_userdata($newdata);
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');      
        $this->load->view('ventas/facturas.php',$data);
    }
     
    private function send($array) {

        if (!is_array($array)) return false;

        $send = array('token' => $this->security->get_csrf_hash()) + $array;

        if (!headers_sent()) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: ' . date('r'));
            header('Content-type: application/json');
        }

        exit(json_encode($send, JSON_FORCE_OBJECT));

    }

    public function comprobante($id){
        if(!($id>0)){ return false;}
        $this->load->model('ventas_model');
        $data["venta"]=$this->ventas_model->venta($id);
        $data["empresa"]=$this->ventas_model->empresa($id);
        $data["cliente"]=$this->ventas_model->cliente($id);
        $data["items"]=$this->ventas_model->items($id);   
       
        $this->load->view('ventas/comprobante.php',$data);

    }
    
}  
?>