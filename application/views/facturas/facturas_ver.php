
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" /> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
   

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">Factura - Ver</div>
                <div class="panel-body">
                    <form role="search" method="POST" action="<?php echo base_url(); ?>facturas">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="empresa">Empresa</label> 
                                <input type="text" name="empresa" id="empresa" class="form-control" 
                                   value="<?=$factura->empresa?>" disabled /> 
                                
                            <br>
                                <label for="prov_nombre">Proveedor</label> 
                                <input type="text" name="prov_nombre" id="prov_nombre" class="form-control" 
                                   value="<?=$factura->prov_nombre?>" disabled /> 
                            <br>
                            
                            <label for="prov_dir">Dirección</label>
                            <input type="text" name="prov_dir" id="prov_dir" class="form-control" 
                                   value="<?=$factura->prov_dir?>" disabled /> 
                            <br>
                            <label for="prov_iva">Código IVA</label>
                            <input type="text" name="prov_iva" id="prov_iva" class="form-control" 
                                   value="<?=$factura->prov_iva?>" disabled /> 
                            <br>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Nro Factura</label>
                                    </div>
                                    <br>
                                    <div class="col-md-3 ">
                                        <input type="text" name="factnro1" id="factnro1" class="form-control"
                                            value="<?=$factura->puerto?>" disabled />
                                    </div>
                                    <div class="col-md-1">
                                        <label>-</label>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="factnro2" id="factnro2" class="form-control"
                                            value="<?=$factura->numero?>" disabled  />
                                    </div>
                                </div>
                                
                                <br>
                                
                                <label for="fecha">Fecha</label>
                                <input type="text" name="fecha" id="fecha" class="form-control" 
                                       value="<?=$factura->fc_format?>" disabled/> 
                                
                                <br>    
                                    
                                <label for="per_iva"><a href="#" title="Cambiar"  id="CambiaPerIva" > Período de IVA 
                                </a>
                                </label>            

                                <input type="text" name="per_iva" id="periva" class="form-control"
                                       value="<?=$factura->pi_mes?>/<?=$factura->pi_anio?>" 
                                       placeholder="mm/yyyy"  disabled/>   
                                  
                                <br>      
                                
                                <label for="cod_afip">Tipo de comprobante</label>
                                <input type="text" name="cod_afip" id="cod_afip" class="form-control"
                                       value="<?=$factura->tp_comprob?>" disabled/>  
                            </div>    
                                
                        </div>
                        
                        <br>
                        <hr>  
                        
                        <div id="tbFactura">
                           
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>código</th>
                                        <th>descripción</th>
                                        <th>cantidad</th>
                                        <th>precio u</th>
                                        <th>iva</th>
                                        <th>total</th>
                                    </tr>
                                </thead>
                                
                                <tbody id="cpFl">
                                    <?php
                                    $items_arr=array();
                                    $iva0=0;$iva21=0;$iva27=0;$iva105=0;
                                    $n0=0;$n21=0;$n27=0;$n105=0;
                                    foreach($items as $it){
                                        array_push($items_arr,get_object_vars($it));
                                    ?>
                                    <tr>
                                        <td><?=$it->codigo?></td>
                                        <td><?=$it->articulo?></td>
                                        <td><?=$it->cantidad?></td>
                                        <td><?=$it->precio?></td>
                                        <td><?php 
                                                    $px=$it->cantidad*$it->precio;                                                    
                                                    if($it->tipo=="I")
                                                        {echo "IVA(".$it->iva."%)";
                                                            if($it->iva==0){$n0+=$px;} 
                                                            if($it->iva==21){$n21+=$px;$iva21+=$px*0.21;} 
                                                            if($it->iva==27){$n27+=$px;$iva27+=$px*0.27;} 
                                                            if($it->iva==10.5){$n105+=$px;$iva105+=$px*0.105;} 
                                                        } 
                                            elseif($it->tipo=="E"){echo "Exento";}
                                            elseif($it->tipo=="N"){echo "No Grav";}
                                        ?></td>
                                        <td><?=$it->precio*$it->cantidad?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    $n0=round($n0,2);
                                    $n21=round($n21,2);
                                    $n105=round($n105,2);
                                    $n27=round($n27,2);

                                    $iva105=round($iva105,2);
                                    $iva21=round($iva21,2);
                                    $iva27=round($iva27,2);
                                    
                                    
                                    if(empty($items)) {?>
                                    <tr>
                                        <td colspan="7" align="center" >Sin Items</td>
                                    </tr>
                                    <?php } ?>    
                                    
                                </tbody>
                                
                            </table>
                            
                        </div>
                        
                        <br>
                        <hr>  
                        
                        <?php $frmPg="";
                        if ($factura->ctacte=="0"){ $frmPg="Contado";}
                        if ($factura->ctacte=="1"){ $frmPg="Cuenta corriente";}
                        ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="formaPago">Forma de pago</label>
                                     <input type="text" name="cod_afip" id="cod_afip" class="form-control"
                                       value="<?=$frmPg?>" disabled/> 
                                </div>
                                <input type="hidden" name="items" id="items" value="<?php echo trim(json_encode($items_arr,true));?>">
                                <div id="tablitaIva">                                
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th scope="col">Alicuota</th>
                                            <th scope="col">Neto</th>
                                            <th scope="col">Iva</th>                
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>                
                                                <td>IVA 0%</td>
                                                <td><?=$n0?></td>
                                                <td>0</td>
                                            </tr>
                                            <tr>                
                                                <td>IVA 10.5%</td>
                                                <td><?=$n105?></td>
                                                <td><?=$iva105?></td>
                                            </tr>
                                            <tr>                
                                                <td>IVA 21%</td>
                                                <td><?=$n21?></td>
                                                <td><?=$iva21?></td>
                                            </tr>
                                            <tr>                
                                                <td>IVA 27%</td>
                                                <td><?=$n27?></td>
                                                <td><?=$iva27?></td>
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                            <label for="intImpNeto">Importe Neto</label>
                           <input type="text" name="intImpNeto" id="intImpNeto" class="form-control"
                                value="<?=$factura->neto?>" disabled/>  
                            <br> 
                            
                            <label for="intIva">IVA</label>
                            <input type="text" name="intIva" id="intIva" class="form-control"
                                value="<?=$factura->iva?>" disabled/>  
                            <br>
                            
                            <label for="intPerIngB">Percepción Ing. Brutos</label>
                            <input type="text" name="intPerIngB" id="intPerIngB" class="form-control"
                                value="<?=$factura->per_ing_bto?>" disabled/>  
                            <br>
                            
                            <label for="intPerIva">Percepción IVA</label>
                            <input type="text" name="intPerIva" id="intPerIva" class="form-control"
                                value="<?=$factura->per_iva?>" disabled/>  
                            <br>
                            
                            <label for="intPerGnc">Percepción Ganancias</label>
                            <input type="text" name="intPerGnc" id="intPerGnc" class="form-control"
                                value="<?=$factura->per_ganancia?>" disabled/>  
                            <br>
                            
                            <label for="intPerStaFe">Percepción Santa Fé</label>
                            <input type="text" name="intPerStaFe" id="intPerStaFe" class="form-control"
                                value="<?=$factura->per_stafe?>" disabled/>
                            <br>
                            
                            <label for="intImpExto">Importe Exento</label>
                            <input type="text" name="intImpExto" id="intImpExto" class="form-control"
                                value="<?=$factura->excento?>" disabled/>
                            <br>
                            
                            <label for="intConNoGrv">Conc. no Gravados</label>
                            <input type="text" name="intConNoGrv" id="intConNoGrv" class="form-control"
                                value="<?=$factura->con_nograv?>" disabled/>  
                            <br>
                            
                            <label for="intTotal">Total</label>
                            <input type="text" name="intTotal" id="intTotal" class="form-control" 
                                value="<?=$factura->total?>" disabled/>  
                            <br>
                            
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label for="obs">Observación</label><br>
                                <?=nl2br($factura->observacion)?>
                                </div>
                            
                        </div>
                        
                        <br><br>
                        
                        
                        <button type="submit" class="btn btn-primary">Volver</button>
                    
                    </form>  
                </div>
                    
            </div>
        </div>
    </div>
    
</div>
<script>
var CFG = {
        url: '<?php echo $this->config->item('base_url');?>',
        token: '<?php echo $this->security->get_csrf_hash();?>'
    };    
    
$(document).ready(function(){
    
    $.ajaxSetup({data: {token: CFG.token}});
    $(document).ajaxSuccess(function(e,x) {
        var result = $.parseJSON(x.responseText);
        $('input:hidden[name="token"]').val(result.token);
        $.ajaxSetup({data: {token: result.token}});  
    
       

        })
    });
    
    ///tablita de ivas
    $("#CambiaPerIva").click(function(){
        $("#periva").removeAttr('disabled');  
    });         
    $("#periva").change(function(){
        $("#periva").attr('disabled','disabled');
        $.post(CFG.url + 'Ajax/periva/',        
            {periva:$("#periva").val(),
            id:<?=$factura->id_factura?>},
            function(data){ 
                alert(data.rta);            
            });   
    });             

$.post(CFG.url + 'Ajax/tablitaIva/',
            {items:$("#items").val()},
            function(data){               
           
                $("#tablitaIva").html(data.tablita);       
                
            });     
           
</script>    