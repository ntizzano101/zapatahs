<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

    public function busca_proveedor() {
        $id=$this->input->post('id');
        if(is_numeric($id)){
        $this->load->model('facturas_model');
        $prov=$this->facturas_model->buscar_proveedor($id);
        $resp=json_decode(json_encode($prov), true);
        }else{
            $resp=json_decode(json_encode(array()), true);
        }
        $this->send($resp);
    }


    
    public function busca_tp_comprob() {
        $proveedor=$this->input->post('proveedor');
        $empresa=$this->input->post('empresa');
        $this->load->model('facturas_model');
        $tipos=$this->facturas_model->lista_comprobantes($empresa,$proveedor);
        $data = new stdClass();
        $combo=""; 
        foreach ($tipos as $tp) {
            
            $combo.='<option value="'.$tp->id.'">'.$tp->nombre.'</option>';
        }
        
        if ($combo==""){$combo='<option value="">Sin tipos de comprobante</option>';}
       
        $data->combo= $combo;   
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);
    }
    

    
    public function busca_combo_item() {
        $item=$this->input->post('item');
        $this->load->model('facturas_model');
        $items=$this->facturas_model->buscar_item($item);
        $combo=""; $codigo=""; $articulo=""; $precio1="";$id_art="";
        $primero=true;
        foreach ($items as $it) {
            if ($primero){
                $codigo=$it->codigo; 
                $articulo=$it->articulo; 
                $precio1=$it->precio1;
                $id_art=$it->id_art;
                $primero=false;
                }
            $combo.='<option value="'.$it->id_art.'">'.$it->articulo.'</option>';
        }
        
        if ($combo==""){$combo='<option value="">Sin items</option>';}
        
        $data = new stdClass();
        
        $data->combo= $combo;   
        $data->codigo= $codigo;  
        $data->articulo= $articulo; 
        $data->precio1= $precio1; 
        $data->id_art= $id_art; 
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);
    }
    
    public function busca_item() {
        $id=$this->input->post('id');
        $this->load->model('facturas_model');
        $data=$this->facturas_model->buscar_un_item($id);
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);
        
        
    }
    
    public function carga_item() {
        $itemCod=$this->input->post('itemCod');
        $itemDesc=$this->input->post('itemDesc');
        $itemCant=$this->input->post('itemCant');
        $itemPrcU=$this->input->post('itemPrcU');
        $itemIva=$this->input->post('itemIva');
        $textIva=$this->input->post('textIva');
        $itemidArt=$this->input->post('itemidArt');
        $itemTotal=$this->input->post('itemTotal');
        $itemTipo=$this->input->post('itemTipo');
        $items=$this->input->post('items');
        
        $mtzItems= json_decode($items,true);
        $fila=array();
        $fila["id"]=count($mtzItems)+1 ;
        $fila["cod"]=$itemCod;
        $fila["id_art"]=$itemidArt;
        $fila["desc"]=$itemDesc;
        $fila["cant"]=$itemCant;
        $fila["prcu"]=sprintf("%.2f",$itemPrcU);
        $fila["iva"]=$itemIva;
        $fila["txiva"]=$textIva;
        $fila["tipo"]=$itemTipo;
        $fila["total"]=sprintf("%.2f",$itemTotal);
        
        array_push($mtzItems, $fila);
        
        $data = new stdClass();
        $data->items= json_encode($mtzItems);
        
        $i=0; $cpFl=""; $intImpNeto=0.00; $intIva=0.00;$intImpExto=0.00;$intImpNoGra=0.00;
        foreach ($mtzItems as $fl) {
            ++$i;
            if($fl["iva"]=="E" or $fl["iva"]=="N" ){
                $fl["prcu"]=sprintf("%.2f",$fl["prcu"] );
                $fl["total"]=sprintf("%.2f",$fl["prcu"] * $fl["cant"]);
            }
            else 
                $fl["total"]=sprintf("%.2f",$fl["prcu"] * $fl["cant"]);
            $cpFl.="<tr>".
                    "<td>".$fl["cod"]."</td>".
                    "<td>".$fl["desc"]."</td>".
                    "<td>".$fl["cant"]."</td>".
                    "<td>".$fl["prcu"]."</td>".
                    "<td>".$fl["txiva"]."</td>".
                    "<td>".$fl["total"]."</td>".
                    "<td>".
                    ' <a class="btn-default fa fa-eraser" title="Borrar"'.
                    ' onclick="quitaItem('.$i.')">'.
                    "</td>".
                    "</tr>";
            if($fl["iva"]=="E"){
                $intImpExto+=$fl["cant"]*$fl["prcu"];
            }
            if($fl["iva"]=="N"){
                $intImpNoGra+=$fl["cant"]*$fl["prcu"];
            }
            if(is_numeric($fl["iva"])){     
                $intImpNeto+=$fl["cant"]*$fl["prcu"];
                $intIva+=round($fl["cant"]*$fl["iva"]*$fl["prcu"],2);
                }
        }
        
        $data->cpFl= $cpFl;        
        $data->intImpNeto= sprintf("%.2f",$intImpNeto);
        $data->intImpExto= sprintf("%.2f",$intImpExto);
        $data->intIva= sprintf("%.2f",$intIva);
        $data->intImpNoGra= sprintf("%.2f",$intImpNoGra);
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);
    }
    
    public function quita_item() {
        $id=$this->input->post('id');
        $items=$this->input->post('items');
        
        $mtzItems= json_decode($items,true);
        
        $i=0; $cpFl=""; $intImpNeto=0.00; $intIva=0.00; $aux=array();$intImpExto=0.00; $intImpNoGra=0.00;
        $intIva=0.00;
        foreach ($mtzItems as $fl){
            if($fl["id"] <> $id){
                ++$i;
                $cpFl.="<tr>".
                    "<td>".$fl["cod"]."</td>".
                    "<td>".$fl["desc"]."</td>".
                    "<td>".$fl["cant"]."</td>".
                    "<td>".$fl["prcu"]."</td>".
                    "<td>".$fl["txiva"]."</td>".
                    "<td>".$fl["total"]."</td>".
                    "<td>".
                    ' <a class="btn-default fa fa-eraser" title="Borrar"'.
                    ' onclick="quitaItem('.$i.')">'.
                    "</td>".
                    "</tr>";
                
                    if($fl["iva"]=="E"){
                        $intImpExto+=$fl["cant"]*$fl["prcu"];
                    }
                    if($fl["iva"]=="N"){
                        $intImpNoGra+=$fl["cant"]*$fl["prcu"];
                    }
                    if(is_numeric($fl["iva"])){     
                        $intImpNeto+=$fl["cant"]*$fl["prcu"];
                        $intIva+=round($fl["cant"]*$fl["iva"]*$fl["prcu"],2);
                        }
                        array_push($aux, $fl);    
                }
            }
            
                if($cpFl==""){$cpFl='<tr><td colspan="7" align="center" >Sin Items</td></tr>';}
               
                $data = new stdClass();
                $data->items= json_encode($aux);    
                $data->cpFl= $cpFl;        
                $data->intImpNeto= sprintf("%.2f",$intImpNeto);
                $data->intImpExto= sprintf("%.2f",$intImpExto);
                $data->intIva= sprintf("%.2f",$intIva);
                $data->intImpNoGra= sprintf("%.2f",$intImpNoGra);
                $resp=json_decode(json_encode($data), true);
                $this->send($resp);     
    }
    


    public function busca_cliente() {
        $id=$this->input->post('id');
        if(is_numeric($id)){
        $this->load->model('ventas_model');
        $prov=$this->ventas_model->buscar_cliente($id);
        $resp=json_decode(json_encode($prov), true);
        }else{
            $resp=json_decode(json_encode(array()), true);
        }
        $this->send($resp);
    }


    public function busca_tp_comprob_cl() {
        $cliente=$this->input->post('cliente');
        $empresa=$this->input->post('empresa');
        $this->load->model('ventas_model');
        $tipos=$this->ventas_model->lista_comprobantes($empresa,$cliente);
        $data = new stdClass();
        $combo=""; 
        foreach ($tipos as $tp) {
              //Solo Para  LA NICOLEÑA  no va interno
            if($tp->nombre=="Interno"){
                if($empresa==2){
                        $combo.='<option value="'.$tp->id.'">'.$tp->nombre.'</option>';
                    }
                }
            else  {
                $combo.='<option value="'.$tp->id.'">'.$tp->nombre.'</option>';
            }
        }
      
        if($combo==""){$combo='<option value="">Sin tipos de comprobante</option>';}       
        $data->combo= $combo;   
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);       
    }
    public function busca_comp_asoc(){
        $id=$this->input->post('id');
        $cliente=$this->input->post('cliente');    
        $this->load->model('ventas_model');               
        $rta=$this->ventas_model->busca_comp_asoc($cliente,$id);
        $combo='<option value="">NINGUNO</option>';
        foreach($rta as $r){
            $combo.='<option value="'.$r->id_factura.'">'.$r->mostrar.'</option>';
        }
        $data = new stdClass();
        $data->combo=$combo;   
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);     

    }
    public function busca_puertos() {
         $id=$this->input->post('id');
         $empresa=$this->input->post('empresa');         

         /*$rta=array("00001");
         if($id>0){
             if($empresa==1){
                //embotelladora
                  if($id<900)
                    $rta=array("00003","00004","00005","00006","00007");
                    
             }             
             if($empresa==3){
                if($id<900)   
                        $rta=array("00002");
             }

         }
        */        
        $this->load->model('ventas_model');       
        $rta=$this->ventas_model->puertos($empresa,$id);
        $data = new stdClass();
        $combo="";                
        foreach ($rta as $tp) {            
            $combo.='<option value="'.$tp->puerto.'">'.$tp->puerto.'</option>';
        }
        if($combo==""){$combo='<option value="">Sin Puertos</option>';}            
        $data->combo= $combo;     
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);        
         
    }
    public function borrar_comprobante() {
        $id=$this->input->post('id');          
       $this->load->model('ventas_model');           
       $rta=$this->ventas_model->borrar_comprobante($id);  
       $data = new stdClass();       
       $data->mensaje= $rta;     
       $resp=json_decode(json_encode($data), true);
       $this->send($resp);        
        
   }
   public function comprobantecambiar() {
        $id=$this->input->post('id');          
        $this->load->model('ventas_model');           
        $rta=$this->ventas_model->buscar_comprobante($id);  
        $data = new stdClass();          
        $data->numero= $rta[0]->numero;     
        $data->id_factura= $rta[0]->id_factura; 
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);            
    }
    public function validarnro() {
        $id=$this->input->post('id');          
        $numero=$this->input->post('numero');          
        $data = new stdClass();       
        if(!(is_numeric($numero) and $numero>0 and $numero < 99999999)){
            $data->mensaje="El Valor $numero no es correcto";
            $data->numero=0;
        }
        else{
            $this->load->model('ventas_model');                                   
            $a=$this->ventas_model->validar_comprobante($id,$numero);                                 
            $fact=$this->ventas_model->venta($id);                                                             
            $data->numero= $numero;
            $data->id_factura=$fact->id_factura;                      
            $data->renglon=$fact->nombre . " " .  str_pad($fact->puerto,5,"0",STR_PAD_LEFT)."-".  str_pad($fact->numero,8,"0",STR_PAD_LEFT) ;     
            $data->mensaje='';
        }                  
        $resp=json_decode(json_encode($data), true);
        $this->send($resp);            
    } 

   public function medio_pago() {
                $id=$this->input->post('id');          
                $this->load->model('ventas_model');           
                $rta=$this->ventas_model->medio_pago($id);  
                $data = new stdClass();       
                $data->nombre= $rta;     
                $resp=json_decode(json_encode($data), true);
                $this->send($resp);        
    
}
public function periva() {
    $periva=$this->input->post('periva');  
    $error="";
    $falla=false;  
    if(strpos($periva, "/")===false){
        $error="El separador debe ser /";$falla=true;
    }else{
        list($prM,$prA)= explode("/", $periva);
        if (!(is_numeric($prA))){
            $error="El separador debe ser /";$falla=true;
        }elseif ($prM < 1 || $prM > 12){ $error="Mes Incorrecto";$falla=true;}
        elseif ($prA < 2019 || $prA > 2050){ $error="Año Incorrecto";$falla=true;}
     }
    
    $data = new stdClass();       
    $data->rta="Guardado!!";
    $data->periva=$periva;
    if($falla===true){
        $data->rta=$error;        
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
   public function tablitaIva(){
    $items=$this->input->post('items');        
    $mtzItems= json_decode($items,true);   
       $tabla=' 
    <table class="table table-sm">
    <thead>
      <tr>
        <th scope="col">Alicuota</th>
        <th scope="col">Neto</th>
        <th scope="col">Iva</th>                
      </tr>
    </thead>
    <tbody>';
    $rows="";
    foreach($mtzItems as $it){
        if(is_numeric($it["iva"])){
            $neto=round($it["cant"]*$it["prcu"],2);
            $iva=round($it["cant"]*$it["prcu"]*$it["iva"],2);
            $texto=$it["txiva"];
        $rows.="<tr>                
                <td>".$texto."</td>
                <td>".$neto."</td>
                <td>".round($iva,2)."</td>
            </tr>";
        }
     }
   $tabla.=$rows.'
    </tbody>
  </table>';
  $data = new stdClass();       
  $data->tablita=$tabla;     
  $resp=json_decode(json_encode($data), true);
  $this->send($resp);     

   } 

  public function traerItems(){
     $idf=   $items=$this->input->post('id');     
     $idc=   $items=$this->input->post('cliente');     
     $this->load->model('ventas_model');                
     $rta=$this->ventas_model->traigo_items_mod($idf);  
     $m="";
     foreach($rta as $j){
         $m.='<input type="text" maxlenght="50"  name="item_valor[]"  class="form-control" value="'.$j->articulo.'" >
         <input type="hidden" maxlenght="50"  name="item_id[]" value="'.$j->id.'" >
         <br>';
     }     
     $data = new stdClass();
     $data->mensaje="";       
     $data->nombre=""; 
     $data->items=""; 
     if(empty($rta)){
        $data->mensaje="NO HAY ITEMS PARA MODIFICAR"; }
     else{
     $data->nombre= $rta[0]->cliente . " Comprobante Letra:" . $rta[0]->letra . " Cod:" . $rta[0]->cod_afip
     . " Puerto:" . $rta[0]->puerto . " Nro:".$rta[0]->numero  ;     
     $data->items=$m;
     }
     $resp=json_decode(json_encode($data), true);
     $this->send($resp);        

  }
  public function cambioItems(){
    $id=   $items=$this->input->post('id');     
    $items=   $items=$this->input->post('items');        
    $data = new stdClass();
    $data->mensaje="";       
    $data->nombre=""; 
    $data->items=array();  
    foreach($id as $k=>$v){     
        $update  = new stdClass();
        $update->id=$v;
        $update->articulo=$items[$k];
        $data->items[]=$update;
    }   
    $this->load->model('ventas_model');                
    $rta=$this->ventas_model->guardo_items_mod($data->items);         
    if(!empty($rta)){
       $data->mensaje="ERROR"; }    
    $resp=json_decode(json_encode($data), true);
    $this->send($resp);        

 }

}