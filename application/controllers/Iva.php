<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Iva extends CI_Controller {

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
       $this->posicion();
     
    }
     
    public function compras()
    {
       
        $this->load->model('iva_model');
        $periodo=$this->input->post('periodo');
        $empresa=$this->input->post('empresa');
        if($periodo==''){$periodo=date('Ym');}
        if($empresa==''){$empresa=1;}
        $data["iva"]=$this->iva_model->compras($periodo,$empresa);
        $data["periodo"]=$periodo;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/iva_compras.php',$data);
     
    }
    public function ventas()
    {
       
        $this->load->model('iva_model');
        $periodo=$this->input->post('periodo');
        $empresa=$this->input->post('empresa');
        if($periodo==''){$periodo=date('Ym');}
        if($empresa==''){$empresa=1;}
        $data["iva"]=$this->iva_model->ventas($periodo,$empresa);
        $data["periodo"]=$periodo;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/iva_ventas.php',$data);
     

    }
    public function posicion()
    {
       
        $this->load->model('iva_model');
        $peri=$this->input->post('peri');
        if($peri==''){$peri=date('Ym');}
        $todo=$this->iva_model->posicion($peri);
        $data["debito1"]=$todo[0];
        $data["credito1"]=$todo[1];
        $data["credito2"]=$todo[2];
        $data["peri"]=$peri;
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/posicion_iva.php',$data);
     
    }
    
    public function plan_de_cuentas()
    {
       
        $this->load->model('iva_model');
        $data["iva"]=$this->iva_model->plan();        
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/plan.php',$data);
     
    }
    public function plan_de_cuentas_buscar()
    {
       
        $this->load->model('iva_model');
        $cuenta=$this->input->post('cuenta');
        $data["cuenta"]=$cuenta;
        $data["iva"]=$this->iva_model->plan_buscar($cuenta);
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/plan.php',$data);
     
    }

    public function plan_id()
    {      
        $id=$this->input->post('id');
        $this->load->model('iva_model');      
        if($id>0){                        
            $data=$this->iva_model->plan_id($id);
            $resp=json_decode(json_encode($data), true);
            $this->send($resp);    
        }    
    }

    public function plan_delete()
    {      
        $id=$this->input->post('id');
        $this->load->model('iva_model');      
        if($id>0){                        
            $this->iva_model->plan_delete($id);            
        }    
    }
    
    public function plan_editar($id)
    {      
        $this->load->model('iva_model');      
        if($id>0){            
            $data["cuenta"]=$this->iva_model->plan_id($id);
            $this->load->view('encabezado.php');
            $this->load->view('menu.php');
            $this->load->view('iva/plan_editar.php',$data);
           
        }    
    }
    public function plan_borrar($id)
    {
        
        $this->load->model('iva_model');
        $cuenta=$this->input->post('cuenta');
        $data["cuenta"]=$cuenta;
        $data["iva"]=$this->iva_model->plan_buscar($cuenta);
        $this->load->view('encabezado.php');
        $this->load->view('menu.php');
        $this->load->view('iva/plan.php',$data);
     
    }
    
    public function verificar_plan()
    {
        $data = new stdClass();     
        $data->error="";
        $data->mensaje="";
        $this->load->model('iva_model');
        $id=strtoupper($this->input->post('id'));
        $cuenta=strtoupper($this->input->post('cuenta'));
        $nombre=substr(strtoupper($this->input->post('nombre')),0,60);
        $imputable=strtoupper($this->input->post('imputable'));
        if(strlen(trim($cuenta))!=10){
            $data->mensaje="Longitud de la cuenta debe ser 10";
            $data->error="errCuenta";
        }
        elseif(!(is_numeric($cuenta))){
            $data->mensaje="La Cuenta Solo Debe Tener Numeros";
            $data->error="errCuenta";
        }        
        elseif($this->iva_model->plan_existe($cuenta,$id)){
            $data->mensaje="La Cuenta Ya Existe";
            $data->error="errCuenta";
        }
        elseif(!($imputable=="S" or $imputable=="N"))
        {
            $data->mensaje="Imputable S/N";
            $data->error="errImputable";
        }  
        elseif(trim($nombre)=="")
        {
            $data->mensaje="La Cuenta Debe Tener un Nombre";
            $data->error="errNombre";
        } 
        else{
            $plan = new stdClass();   
            $plan->id=$id;
            $plan->nombre=$nombre;
            $plan->cuenta=$cuenta;
            $plan->imputable=$imputable;
            if($id>0)
                $this->iva_model->plan_update($plan);     
            else   
                $this->iva_model->plan_insert($plan);     
        }       
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);    
     
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
    public function siap_compras($tipo,$piva){    
        $this->load->model('iva_model');
        $compro=$this->iva_model->siap_compras($piva);
        $c="";$x="";
        foreach($compro as $com){
            $com->cuit=str_replace("-","",$com->cuit);
            //loc Comp C entonces va exento no  nograba
            if(in_array($com->codigo_comp,array(11,12,13))){
                   $com->excento=0;
                   //$com->excento+$com->con_nograv;
                   $com->con_nograv=0; 
            }
            //Fecha Comp
            $c.=$com->f2;
            //tipo Comp
            $c.=str_pad($com->codigo_comp,3,"0",STR_PAD_LEFT);
            //puerto
            $c.=str_pad($com->puerto,5,"0",STR_PAD_LEFT);
            //numero
            $c.=str_pad($com->numero,20,"0",STR_PAD_LEFT);
            //despacho 
            $c.=str_repeat(" ",16);
            //tipo doc 80
            $c.="80";
            //nro doc 
            $c.=str_pad($com->cuit,20,"0",STR_PAD_LEFT);
            // Nombre
            $c.=str_pad(substr($com->proveedor,0,30),30," ",STR_PAD_RIGHT);
            //total
            $c.=str_pad($com->total*100,15,"0",STR_PAD_LEFT);
            //nogra
            $c.=str_pad($com->con_nograv*100,15,"0",STR_PAD_LEFT);
            //exento            
            $c.=str_pad($com->excento*100,15,"0",STR_PAD_LEFT);
            //percepcion iva
            $c.=str_pad($com->per_iva*100,15,"0",STR_PAD_LEFT);
            //percepcion ganancias
            $c.=str_pad($com->per_ganancia*100,15,"0",STR_PAD_LEFT);
            //ingresos brutos
            $c.=str_pad($com->per_ing_bto*100,15,"0",STR_PAD_LEFT);
            //municipales
            $c.=str_repeat("0",15);
            //impuestos internos
            $c.=str_repeat("0",15);
            //moneda
            $c.="PES";
            //cambio  4 ent 6 dec
            $c.="0001000000";
            //cant ivas
            $cantIVa=0;
            if($com->neto0<>0.00){$cantIVa++;}
            if($com->neto105<>0.00){$cantIVa++;}
            if($com->neto21<>0.00){$cantIVa++;}
            if($com->neto27<>0.00){$cantIVa++;}
            $c.=$cantIVa;
            //Cod OPeracion
            $c.=" ";
            //credito fiscal comptable 
            $c.=str_pad($com->iva*100,15,"0",STR_PAD_LEFT);
            //Otrostributos
            $c.=str_repeat("0",15);
            //cuit corredr
            $c.=str_repeat("0",11);
            //demonicacion corredor
            $c.=str_repeat(" ",30);
            //iva comision
            $c.=str_repeat("0",15);
            $c.=PHP_EOL;
            ///Alicuotas  
            if($com->iva0<>0.00) 
            {
                //tipo Comp
                $x.=str_pad($com->codigo_comp,3,"0",STR_PAD_LEFT);    
                //puerto
                $x.=str_pad($com->puerto,5,"0",STR_PAD_LEFT);
                //numero
                $x.=str_pad($com->numero,20,"0",STR_PAD_LEFT);
                //tipo doc 80    
                $x.="80";
                //nro doc 
                $x.=str_pad($com->cuit,20,"0",STR_PAD_LEFT);
                //neto
                $x.=str_pad($com->neto0*100,15,"0",STR_PAD_LEFT);
                //Copdigo
                $x.="0003";
                //iva liquidado
                $x.=str_repeat("0",15);
                $x.=PHP_EOL;
            }    
            if($com->iva105<>0.00) 
            {
                //tipo Comp
                $x.=str_pad($com->codigo_comp,3,"0",STR_PAD_LEFT);    
                //puerto
                $x.=str_pad($com->puerto,5,"0",STR_PAD_LEFT);
                //numero
                $x.=str_pad($com->numero,20,"0",STR_PAD_LEFT);
                //tipo doc 80    
                $x.="80";
                //nro doc 
                $x.=str_pad($com->cuit,20,"0",STR_PAD_LEFT);
                //neto
                $x.=str_pad($com->neto105*100,15,"0",STR_PAD_LEFT);
                //Copdigo
                $x.="0004";
                //iva liquidado
                $x.=str_pad($com->iva105*100,15,"0",STR_PAD_LEFT);
                $x.=PHP_EOL;
            }    
            if($com->iva21<>0.00) 
            {
                //tipo Comp
                $x.=str_pad($com->codigo_comp,3,"0",STR_PAD_LEFT);    
                //puerto
                $x.=str_pad($com->puerto,5,"0",STR_PAD_LEFT);
                //numero
                $x.=str_pad($com->numero,20,"0",STR_PAD_LEFT);
                //tipo doc 80    
                $x.="80";
                //nro doc 
                $x.=str_pad($com->cuit,20,"0",STR_PAD_LEFT);
                //neto
                $x.=str_pad($com->neto21*100,15,"0",STR_PAD_LEFT);
                //Copdigo
                $x.="0005";
                //iva liquidado
                $x.=str_pad($com->iva21*100,15,"0",STR_PAD_LEFT);
                $x.=PHP_EOL;
            }    
            if($com->iva27<>0.00) 
            {
                //tipo Comp
                $x.=str_pad($com->codigo_comp,3,"0",STR_PAD_LEFT);    
                //puerto
                $x.=str_pad($com->puerto,5,"0",STR_PAD_LEFT);
                //numero
                $x.=str_pad($com->numero,20,"0",STR_PAD_LEFT);
                //tipo doc 80    
                $x.="80";
                //nro doc 
                $x.=str_pad($com->cuit,20,"0",STR_PAD_LEFT);
                //neto
                $x.=str_pad($com->neto27*100,15,"0",STR_PAD_LEFT);
                //Copdigo
                $x.="0006";
                //iva liquidado
                $x.=str_pad($com->iva27*100,15,"0",STR_PAD_LEFT);
                $x.=PHP_EOL;
            }    


        }  
            ////FIN IVA COMPRAS 
            $rutaArchivo1 = "compras/Compras_".$piva.".txt";
            $rutaArchivo2 = "compras/Compras_Ali_".$piva.".txt";           
            file_put_contents($rutaArchivo1,$c);           
            file_put_contents($rutaArchivo2,$x);                       
            $defi=$rutaArchivo2;
            if($tipo==1){
                $defi=$rutaArchivo1;
            }
            ///
            $nombreArchivo = basename($defi);

            // Configurar las cabeceras para la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($defi));
            
            // Limpiar el búfer de salida
            ob_clean();
            flush();
            
            // Leer el archivo y enviarlo al navegador
            readfile($defi);
            exit;
            ///

    }

    
}  
?>