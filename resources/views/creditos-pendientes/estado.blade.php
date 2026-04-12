<html>
    <head>
        <style>
            @page {
                margin: 0cm 0cm;
                font-family: Arial;
            }

            body {
                margin: 3cm 2cm 2cm;
            }
            h1{
                font-size: 16px;
            }
            #nombres{


                font-size: 16px;
            }
            #comite{
                margin-left: 70px;
                margin-top: -45px;
                font-size: 12px;
            }

            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;
                background: #52CB85 ;
                color: black;
                text-align: center;
                line-height: 10px;
            }

            footer {
                position: fixed;
                bottom: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;
                background-color:#52CB85  ;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            .tabla3{
            margin-top: 15px;
        }
        .tabla3 .head{
        border-bottom: #000 1px solid;
        }


        .tabla3 .cancelado{
            border-left: 0;
            border-right: 0;
            border-bottom: 0;
            border-top: 1px dotted #000;
            width: 200px;
        }
        </style>
        <title>Estado Credito</title>



    </head>

    <body>

    <main style="margin-top: -80px;">



         <h4 style="text-align: center;margin-top: 0px;margin-top:-45;color:#145A32 "> Asociación de Productores Jardines de Palma</h4>
         <h4 style="text-align: center;margin-top:-15px"><strong>R.U.C : 20531262434</strong></h4>
         <p style="font-size:12px;margin: top -10px;margin-left:20px">CAR. TARAPOTO YURIMAGUAS KM. 60.5 SECTOR WICUNGO-CAYNARACHI-LAMAS-SAN MARTIN</p>
         <hr style="  border-style: dotted;">
         <h4 style="text-align: center;">ESTADO DE DEUDAS</h4>
         <img src="https://consulta-palmero.jarpal.com.pe/images/logo.png" alt="" width=20%  style="margin-top: -150px;">

         <?php

        $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        //echo $diassemana[date('w')].", ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;


    ?>
    <p style="text-align: center;font-size: 12px;margin-top:-40px">Estado de deudas al <?php echo $diassemana[date('w')].", ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;   ?> </p>
    <h4 style="padding: 1px;font-size: 12px;"><strong>Nombres: </strong>   <?php //echo  Auth::user()->name ?></h4>
     <h4 style="padding: 1px;font-size: 12px;"><strong>DNI/RUC:</strong>     <?php  //echo $persona->ruc_per?></h4>
     <hr>
     <h4 style="padding: 1px;font-size: 12px;">N° Credito: <?php //echo $datos[$i]["id_cre"] ?>    Concepto: <?php //echo $lineasactivas[$i]->nomb_con;   ?> </h4>

     <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                  <th style="padding: 1px;font-size: 12px;">N° Cuota</th>
                                  <th style="padding: 1px;font-size: 12px;">Monto Cuota</th>
                                  <th style="padding: 1px;font-size: 12px;">Vencimiento</th>
                                  <th style="padding: 1px;font-size: 12px;">Estado</th>
                                  <th style="padding: 1px;font-size: 12px;"> Amortizado</th>
                                  <th style="padding: 1px;font-size: 12px;">Saldo</th>
                                </tr>
                              </thead>

                      <tbody>

                      <?php

                          $sumageneral=0;
                          $sumamortizado=0;
                          $sumasaldo=0;
                          ?>



                        </tbody>
                        </table>

                        <hr style="border-style: dotted;">

                      <p style="font-size: 14px;margin-left:500px;margin-top:-50px"><?php //echo $sumamortizado  ?></p>
                      <p style="font-size: 14px;margin-left:590px;margin-top:-50px"> <?php //echo $sumasaldo  ?></p>
                      <p style="font-size: 14px;margin-left:120px;margin-top:-50px"> <?php //echo str_pad($sumageneral, 2, "0", STR_PAD_LEFT)  ?></p>







    </main>










    </body>
    </html>
