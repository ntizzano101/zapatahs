<?php
class Facturas_model extends CI_Model {
    
    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
    }
    
    //LISTADOS VARIOS
    
    public function lista_proveedores()
        {
            $sql="SELECT id, proveedor FROM proveedores a order by a.proveedor ";
            $retorno=$this->db->query($sql)->result();
            return $retorno;
        } 
        
    public function lista_empresas()
        {
            $sql="SELECT id_empresa, razon_soc FROM empresas";
            $retorno=$this->db->query($sql)->result();
            return $retorno;
        }     
        
    public function lista_comprobantes($id_empresa,$id_proveedor)
        {
            $sql="SELECT DISTINCT id, cod_afip, cod_afip_t,nombre FROM cod_afip".
            " WHERE id_iva=(SELECT iva FROM proveedores WHERE id=?)".
            " AND id_iva_compra=(SELECT cond_iva FROM empresas WHERE id_empresa=?)".
            " and cod_afip < 700 ORDER BY cod_afip";
            $retorno=$this->db->query($sql, array($id_proveedor,$id_empresa))->result();           
            return $retorno;
            //SACO INTERNO REMITO PRRESUPUESTO
        }    
        
    public function buscar_proveedor($id)
        {
            $sql="SELECT a.*, b.cond_iva".
                " FROM proveedores a".
                " INNER JOIN cdiva b ON a.iva=b.codigo". 
                " WHERE a.id=? ";
            $retorno=$this->db->query($sql, array($id))->row();
            return $retorno;
        }        
    
    public function buscar_item($item)
        {   
            $item="%".trim(strtoupper($item))."%";
            $sql="SELECT *".
                " FROM articulos".
                " WHERE UPPER(codigo) LIKE ? OR  UPPER(articulo) LIKE ?";
            
            $retorno=$this->db->query($sql, array($item, $item))->result();
            return $retorno;
        }       
        
    public function buscar_un_item($id)
        {   
            $sql="SELECT *".
                " FROM articulos".
                " WHERE id_art = ?";
            
            $retorno=$this->db->query($sql, array($id))->row();
            return $retorno;
        }    
        
        
        
    //FACTURAS
    public function listado($b,$c,$d)
        {
            $sql="SELECT a.id_factura AS id".
                ", DATE_FORMAT(a.fecha, '%d/%m/%Y') AS fecha".
                ", b.proveedor,a.codigo_comp,a.puerto,a.numero,a.total".
                " FROM facturas a".
                " INNER JOIN proveedores b ON a.id_proveedor=b.id".
                " WHERE TRUE ";
            
            
           /* if(trim($b) !=""){
                $esFch=false;
                if (substr_count($b,"/")==2){
                    list($dia,$mes,$anio)= explode("/",$b);
                    if(is_numeric($dia) && is_numeric($mes) && is_numeric($anio) ){
                        if(checkdate($mes, $dia, $anio)){
                            $esFch=true;
                            $b=$anio."-".$mes."-".$dia;
                        }
                    }
                }
                
                if($esFch){
                    $sql.=" AND a.fecha=?";
                }else{
            */        
                    $b="%".trim(strtoupper($b))."%";
                    $sql.=" AND UPPER(b.proveedor) LIKE ?";
            /*        
                }
            **/    
            $sql.= " and a.fecha between ? and ? ";          
            /*}else{
           */     
            $sql.=" ORDER BY a.fecha DESC, b.proveedor ";
            //}
         
            $sql.= " limit 100 ";
            
            $retorno=$this->db->query($sql, array($b,$c,$d))->result();
            if((is_array($retorno))){
                return $retorno;
            }
            else
            {
                return array();
            }
             
        }
        
    public function buscar($id)
        {
        $sql="SELECT a.*, DATE_FORMAT(fecha, '%d/%m/%Y') AS fc_format,".
            "SUBSTRING(a.periodo_iva, 1, 4) AS pi_anio, SUBSTRING(a.periodo_iva, 5,2) AS pi_mes,".    
            " b.razon_soc AS empresa,".
            " c.proveedor AS prov_nombre, c.domicilio AS prov_dir, d.cond_iva AS prov_iva,".
            " e.cod_afip_t AS tp_comprob".    
            " FROM facturas a".
            " INNER JOIN empresas b ON a.id_empresa=b.id_empresa".
            " INNER JOIN proveedores c ON a.id_proveedor=c.id".  
            " INNER JOIN cdiva d ON c.iva=d.codigo". 
            " INNER JOIN cod_afip e ON a.id_tipo_comp=e.id".    
            " WHERE a.id_factura=?";
        $retorno=$this->db->query($sql, array($id))->row();
        return $retorno;
        }       
    public function control_numeracion($obj){
        $sql="select letra from facturas where id_empresa=? and puerto=?  and numero=? and id_tipo_comp=? and id_proveedor=? limit 1";
        $retorno=$this->db->query($sql, array($obj->empresa,$obj->factnro1,$obj->factnro2,$obj->cod_afip,$obj->proveedor))->row();
        
        return $retorno;
    }
    public function guardar($obj)
        {       
              
                $usuario=$_SESSION["id"];
                if(!(is_numeric($obj->intImpNeto))){$obj->intImpNeto="0.00";}
                if(!(is_numeric($obj->intIva))){$obj->intIva="0.00";}
                if(!(is_numeric($obj->intPerIngB))){$obj->intPerIngB="0.00";}
                if(!(is_numeric($obj->intPerIva))){$obj->intPerIva="0.00";}
                if(!(is_numeric($obj->intPerGnc))){$obj->intPerGnc="0.00";}
                if(!(is_numeric($obj->intPerStaFe))){$obj->intPerStaFe="0.00";}
                if(!(is_numeric($obj->intImpExto))){$obj->intImpExto="0.00";}
                if(!(is_numeric($obj->intConNoGrv))){$obj->intConNoGrv="0.00";}
                if(!(is_numeric($obj->intTotal))){$obj->intTotal="0.00";}        
                list($ano,$mes,$dia)= explode("-", $obj->fecha);                                
                $items=json_decode($obj->items);          
                //guardo encabezado          
                $sql="INSERT INTO facturas(
                    id_proveedor, 
                    fecha,
                    puerto,
                    numero,
                    cod_afip,                        
                    cond_iva,
                    periodo_iva,
                    observacion,
                    ctacte,
                    id_empresa, 
                    letra,
                    tipo_comp,
                    codigo_comp,
                    id_tipo_comp,
                    dni,
                    cliente,
                    usuario,
                    excento,
                    total,
                    neto,
                    iva,
                    serv_desde,
                    serv_hasta,
                    cbu,
                    id_comp_asoc,
                    vence,
                     per_ing_bto,
                     per_iva,
                     per_ganancia,
                     per_stafe)VALUES(";        
                $sql.="    ?,";  // id_proveedor 1
                $sql.="    ?,";  //fecha 2
                $sql.="    ?,";  //puerto 3 
                $sql.="    ?,";  //nuero 4 
                $sql.="    (SELECT cod_afip from cod_afip where id=? ),";  // 5 
                $sql.="    (SELECT iva from proveedores where id=? ),";   // 6 
                $sql.="    ?,"; // 7 
                $sql.="    ?,"; //8 
                $sql.="    ?,"; // 9
                $sql.="    ?,"; // 10 
                $sql.="    (SELECT letra from cod_afip where id=? ),";  // 11 
                $sql.="    (SELECT id_tipo_comp from cod_afip where id=? ),"; //12
                $sql.="    (SELECT cod_afip_t from cod_afip where id=?),";    //13      
                $sql.="    ?,"; // 14
                $sql.="    ?,"; //15
                $sql.="    ?"; //16
                $sql.="    ,?"; //17 
                $sql.="    ,?"; //18 
                $sql.="    ,?";//19 
                $sql.="    ,?"; //20
                $sql.="    ,?"; //21
                $sql.="    ,?"; //22
                $sql.="    ,?"; //23
                $sql.="    ,?";  // 24
                $sql.="    ,?"; //25      
                $sql.="    ,?"; //26      
                $sql.="    ,?"; //27      
                $sql.="    ,?"; //28      
                $sql.="    ,?"; //29      
                $sql.="    ,?)"; //30               
                $mtz=array();
                $mtz[]=$obj->proveedor;
                $mtz[]=$obj->fecha;
                $mtz[]=$obj->factnro1;
                $mtz[]=$obj->factnro2;
                $mtz[]=$obj->cod_afip;
                $mtz[]=$obj->proveedor;
                $mtz[]=$obj->periva;
                $mtz[]=$obj->obs;
                $mtz[]=$obj->formaPago;
                $mtz[]=$obj->empresa;
                $mtz[]=$obj->cod_afip;
                $mtz[]=$obj->cod_afip;
                $mtz[]=$obj->cod_afip;
                $mtz[]=$obj->cod_afip;
                $mtz[]=0;
                $mtz[]=0;        
                $mtz[]=$_SESSION["id"];
                $mtz[]=$obj->intImpExto;
                $mtz[]=$obj->intTotal;
                $mtz[]=$obj->intImpNeto;
                $mtz[]=$obj->intIva;   
                $mtz[]='';
                $mtz[]='';
                $mtz[]='';
                $mtz[]='';
                $mtz[]='';
                $mtz[]=$obj->intPerIngB;
                $mtz[]=$obj->intPerIva;
                $mtz[]=$obj->intPerGnc;
                $mtz[]=$obj->intPerStaFe;
                $this->db->query($sql, $mtz);        
                $last_id=$this->db->insert_id();        
                //Ahora los items 
                $neto21=0;
                $neto105=0;
                $neto0=0;
                $neto27=0;
                $iva21=0;
                $iva27=0;                
                $iva105=0;                
                $exento=0;
                $total=0;
                $neto=0;
                $iva=0;
                $nogra=0;                           
                foreach($items as $x){
                     if(is_numeric($x->iva)){$x->iva=$x->iva*100;$x->tipo="I";}
                     else{$x->tipo=$x->iva;$x->iva=0;}

                     if($x->iva==21){$neto21=$neto21+ $x->prcu * $x->cant;
                                    $iva21=$iva21+($x->prcu * $x->cant)*0.21; }
                     if($x->iva==10.5){$neto105=$neto105+$x->prcu * $x->cant; 
                                    $iva105=$iva105+($x->prcu * $x->cant)*0.105;}
                    if($x->iva==27){$neto27=$neto27+$x->prcu * $x->cant; 
                                        $iva27=$iva27+($x->prcu * $x->cant)*0.27;}
                    if($x->iva==0 && $x->tipo=="I"){$neto0=$neto0+$x->prcu * $x->cant; }
                     
                     if($x->tipo=="E"){$exento=$exento+$x->prcu * $x->cant;} 

                     if($x->tipo=="N"){$nogra=$nogra+$x->prcu * $x->cant;} 

                     if($x->id_art==""){$x->id_art=0;}
                     $sql="select costo from articulos where id_art=".$x->id_art;            
                     $rta=$this->db->query($sql);             
                     $a=$rta->result_array();             
                     $costo=0;
                     if(count($a)>0)             
                        $costo=$a[0]["costo"];
                     $sql="INSERT INTO factura_items(id_factura,id_art,articulo,costo,precio,iva,cantidad,dto,tipo) values(?,?,?,?,?,?,?,?,?)";
                     $mtz=array(
                        $last_id,
                        $x->id_art,
                        $x->desc,
                        $costo,
                        $x->prcu,
                        $x->iva,
                        $x->cant,
                        0,
                        $x->tipo
                     );      
                 
                     $this->db->query($sql, $mtz);
                }
                $iva=$iva21+$iva105+$iva27;
                $neto=$neto21+$neto105+$neto0+$neto27;
                $total=$neto+$iva+$nogra+$exento+$obj->intPerIngB+$obj->intPerIva+$obj->intPerGnc+$obj->intPerStaFe;
                $sql="UPDATE facturas set 
                total=?,
                excento=?,
                iva21=?,
                iva105=?,
                iva27=?,
                neto21=?,
                neto105=?,
                neto27=?,
                neto0=?,
                neto=?,
                iva=?,
                con_nograv=?      
                where id_factura=?";
                $mtz=array(
                    $total,
                    $exento,
                    $iva21,
                    $iva105,
                    $iva27,
                    $neto21,
                    $neto105,
                    $neto27,
                    $neto0,
                    $neto,
                    $iva,
                    $nogra,
                    $last_id);
                //   echo $sql;
                //   print_r($mtz);die;    
                $this->db->query($sql,$mtz);
            //Si es CONTADO GENERA OP AUTOMNATICAMANTE
            if($obj->formaPago==0){
            $opago=new stdClass();
            $opago->cab=new stdClass();
            $opago->factura=new stdClass();
            $opago->pago=new stdClass();
            $opago->cab->id=Null;
            $opago->cab->id_cliente=Null;
            $opago->cab->fecha=$obj->fecha;
            $opago->cab->total=$total;
            $opago->cab->id_proveedor=$obj->proveedor;
            $opago->factura->id=Null;
            $opago->factura->id_op=Null;
            $opago->factura->id_factura=$last_id;
            $opago->factura->monto=$total;
            $opago->pago->id_pago=Null;
            $opago->pago->id=Null;
            $opago->pago->monto=$total;
            $opago->pago->id_medio_pago=1;
            $opago->pago->id_cheque=0;
            $opago->pago->observaciones="Automatica";
            $opago->pago->nro_comprobante="";
            $this->db->insert('opago', $opago->cab);
            $opago->cab->id=$this->db->insert_id();
            $opago->factura->id_op=$opago->cab->id;
            $opago->pago->id_pago=$opago->cab->id;
            $this->db->insert('opago_facturas', $opago->factura);
            $this->db->insert('opago_pago', $opago->pago);
        }

    } 
    
    public function factura_en_opago_existe($id)
        {
            $sql="SELECT * FROM opago_facturas WHERE id_factura=?";
            $datos=$this->db->query($sql, array($id))->result();
            $paso1=empty($datos);            
            $id_pago=0;
            if(!$paso1){
                $id_pago=$datos[0]->id_op;
            }
            //Pero si es de contado no impota que tenga 
            $sql="SELECT ctacte FROM facturas WHERE id_factura=?";
            $datos=$this->db->query($sql, array($id))->result();
            $paso2=$datos[0]->ctacte==0;
            $rta=true;
            //si no tiene pagos dejo borrar 
            if($paso1===true){$rta=false;}            
            //tiene pago pero es de contado, osea tomo como que no existe 
            //asi permito borrar
            elseif($paso1===false and $paso2===true){
                    //borramos opa  de contado
                    $sql1="DELETE FROM opago WHERE id=?";
                    $sql2="DELETE FROM opago_pago WHERE id_pago=?";
                    $sql3="DELETE FROM opago_facturas WHERE id_op=?";
                    $this->db->query($sql1, array($id_pago));
                    $this->db->query($sql2, array($id_pago));
                    $this->db->query($sql3, array($id_pago));
                    $rta=false;
                }
            else{$rta=true;}
            
            return $rta;

            
        }
    
    
    public function borrar($id)
        {
        $retorno="";
        
        $sql="SELECT * FROM opago_facturas WHERE id_factura = ?";
        $datos=$this->db->query($sql, array($id))->result();
        
        if (count($datos)==0){//seteamos baja
            $sql="DELETE FROM facturas WHERE id_factura=?";
            $this->db->query($sql, array($id));
            $sql="DELETE FROM factura_items WHERE id_factura=?";
            $this->db->query($sql, array($id));
            $retorno ="El artículos se ha eliminado con éxito";
        }
        return $retorno;           
        }  
    public function cmb_cbus(){
        $sql="select id,banco from bancos";        
        $r =$this->db->query($sql)->result();
        $a=array();
        foreach($r as $v){
                $a[$v->id]=$v->banco;    
        }
        if(count($a)==0){$a=array("0"=>"NINGUNO");}
        return $a;
    }          
    public function cmb_comps_asoc($id_cliente,$tipo){
        $sql="select concat(DATE_FORMAT(fecha, '%d/%m/%Y'),' ',letra,' ', LPAD(puerto,5,'0') , '-' ,LPAD(numero,8,'0')) as Fac,
        id_factura from facturas where id_cliente=? and 
        (codigo_comp in(1,2) and 3=?  
                or 
         codigo_comp in(201,202) and 203=?) 
         and id_comp_asoc=0";        
        $r =$this->db->query($sql,array($id_cliente,$tipo,$tipo))->result();
        $a=array();
        foreach($r as $v){
                $a[$v->id_factura]=$v->Fac;    
        }
        if(count($a)==0){$a=array("0"=>"NINGUNO");}
        return $a;
    }
    
    public function buscar_items($id)
        {
        $sql="SELECT a.*, ifnull(r.codigo,'') as codigo".
            " FROM factura_items a LEFT JOIN articulos r on a.id_art=r.id_art".                        
            " WHERE a.id_factura=?";
        $retorno=$this->db->query($sql, array($id))->result();
        return $retorno;
        } 
    public function periva($id,$periva)
        {
        $sql="update facturas set periodo_iva=? where id_factura=?";
        $retorno=$this->db->query($sql, array($periva,$id))->result();      
        return $retorno;
           
    }         
}
?>
