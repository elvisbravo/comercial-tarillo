<!DOCTYPE html>
<html>
<head>
    <title>contrato</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

   <div class="container">
       <div class="row">
           <div class="col-lg-12">
               <h5 style="text-align: center;font-size:16px ;">CONTRATO DE COMPRAVENTA A PLAZOS</h5>
               <img src="img/logo2.jpeg" alt="" width="100px" style="margin-top: -30px;">
               <p style="text-align:justify ;font-size: 10px;">
               Conste por el presente documento un Contrato de COMPRAVENTA A PLAZOS que celebran de una parte COMERCIAL TARRILLO Con RUC: Nº. 10448761873, con domicilio en FRANCISCO BARDÁLES #922, ALFONSO UGARTE #518 - 520  de la ciudad de YURIMAGUAS quién en adelante se le denominará “EL VENDEDOR”, y de otra parte <strong>{{$credito->razon_social}}</strong>, identificado con DNI/RUC Nº <strong>{{$credito->documento}}</strong> domiciliado en <strong>{{$credito->dire_per}}</strong>, a quien en adelante se le denominará EL PRESTATARIO, en las siguientes condiciones y términos:
               </p>
               <p style="text-align:justify ;font-size: 10px;"><strong> PRIMERO.-</strong> Por el presente contrato el “VENDEDOR” entrega al “COMPRADOR” en la calidad de venta conforme a lo preceptuado en el Art. 1529 del Código Civil él/los siguientes (s) bien (es):</p>
               <p style="text-align: center;font-size: 10px;"><strong >DETALLE DE PRODUCTOS OTORGADOS</strong></p>
               <table class="table  dt-responsive  nowrap w-100">
                   <thead>

                       <td style="font-size: 10px;text-align:center;"><strong>Producto</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>Cantidad</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>Sub Total</strong></td>

                   </thead>
                   <tbody>
                       @foreach($detalle as $d)
                       <tr>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$d->nomb_pro}}</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$d->cantidad}}</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{ $d->precio*$d->cantidad}}</td>

                       </tr>
                       @endforeach

                   </tbody>
               </table>
               <p style="text-align: center;font-size: 10px;"><strong >INFORMACION DEL CREDITO</strong></p>
               <table class="table  dt-responsive  nowrap w-100">
                   <thead>

                       <td style="font-size: 10px;text-align:center;"><strong>Cod. Credito</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>Capital Prestado</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>Interes</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>N° Cuotas</strong></td>
                       <td style="font-size: 10px;text-align:center;"><strong>Total a Pagar</strong></td>

                   </thead>
                   <tbody>
                       <tr>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$credito->id}}</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$credito->impo_cre}}</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">0.00</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$credito->peri_cre}}</td>
                           <td  style="padding:1px;font-size: 10px;text-align:center;">{{$credito->impo_cre}}</td>

                       </tr>

                   </tbody>
               </table>
               <p style="text-align:justify;font-size: 10px;"><strong>SEGUNDO.-</strong> El plazo, incluye el número y periodicidad de las cuotas establecidas, se
                encuentran detallados en el Cronograma de Pagos que se entrega a EL PRESTATARIO a la firma
                del presente contrato y que se adjunta a la misma, EL PRESTATARIO deberá cumplir con el pago
                del crédito conforme a las fechas indicadas.
               </p>

               <p style="text-align:justify;font-size: 10px;"><strong>TERCERO.-</strong> El “COMPRADOR” se obliga a pagar puntualmente las cuotas pactadas en la cláusula 2da del presente contrato. En caso de incumplimiento del pago de dos (02) de las cuotas establecidas, ambas partes de mutuo acuerdo, convienen en dar por vencidos los plazos de las obligaciones por devengar, siendo exigible el pago integro del saldo deudor, mas los cargos por concepto de intereses, y demás gastos referidos a la cobranza, pudiendo de esta forma el mismo “VENDEDOR” resolver en forma unilateral el presente contrato.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>CUARTO.-</strong> Si el “VENDEDOR” opta por resolver el contrato y no acepta recibir el pago de la cuota o cuotas restantes , el “COMPRADOR” queda obligado a entregar al “VENDEDOR” el bien materia del presente contrato cuando este lo solicite, mediante requerimiento simple, a través de la Orden de Recojo de Mercadería considerándose como una compensación por el uso del bien, los pagos efectuados conforme lo prevé el artículo 1563 del Código Civil, independiente de que el “COMPRADOR” responda en el caso de que el bien no cubra el saldo adeudado por el uso, deterioro, desperfecto u otra situación a normal en el producto, imputable al “COMPRADOR” en la medida de que sea tasada por nuestro Departamento Técnico, con una indemnización calculada en no menos al 50% del valor real del bien objeto del presente contrato.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>QUINTO.-</strong> Queda establecido por parte del “VENDEDOR” la posibilidad de otorgar al “COMPRADOR” el plazo improrrogable de 30 días de recogido la mercadería para que éste regularice en forma indefectible sobre los meses que adeudara, caso contrario después de dicho termino caducará cualquier reclamo y/o solicitud de devolución del producto. </p>
               <p style="text-align:justify;font-size: 10px;"><strong>SEXTO.-</strong> Queda establecido que la garantía que brinda el “VENDEDOR” de los productos materia de venta se circunscriben a los límites de la marca de cada producto, constituyendo garantía explicita para los efectos del presente contrato, conforme lo previsto en el artículo 20 inciso b) de la Ley 29571- Código del Consumidor.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>SÈPTIMO.-</strong> EL “FIADOR SOLIDARIO” se responsabiliza solidariamente con el “COMPRADOR” por el fiel y correcto cumplimiento de este Contrato y acepta anticipadamente cualquier decisión futura que acordase el “COMPRADOR” y el “VENDEDOR”, respecto a la presente relación contractual, renunciando expresamente el “FIADOR SOLIDARIO” al beneficio de excusión.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>OCTAVO.-</strong> Las partes que suscriben el presente contrato así como el “FIADOR SOLIDARIO” renuncian al fuero de sus domicilios y se someten expresamente a la Jurisdicción de los Juzgados del Distrito Judicial donde se encuentra el domicilio de “EL VENDEDOR” para todos los efectos Judiciales respecto a este Contrato.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>NOVENO.-</strong> Queda establecido que, en caso de existir, los gastos que ocasionan la suscripción de este contrato y los de transferencia de una probable sustitución del deudor serán de cuenta del “COMPRADOR”.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>DÈCIMO.-</strong> Las partes acuerdan que en caso el COMPRADOR no cumpla con pagar sus cuotas periódicas en las fechas previstas en el cronograma de pagos, el COMPRADOR y el FIADOR SOLIDARIO, de ser el caso, incurrirán en mora automática, de conformidad con lo previsto por el numeral 1 del artículo 1333º del Código Civil sin necesidad de requerimiento o intimación previa por parte del “VENDEDOR” y por lo tanto, las sumas no pagadas devengaran los intereses moratorios a la tasa establecida en los anexos que forman parte del presente contrato.</p>
               <p style="text-align:justify;font-size: 10px;"><strong>DÉCIMO PRIMERO.-</strong> Las partes contratantes señalan expresamente que, a partir de la fecha las obligaciones materia del presente contrato generarán intereses compensatorios referidos a la máxima tasa activa del mercado en moneda nacional (TAMN), autorizada por el Banco Central de Reserva del Perú (BCRP).</p>
               <p style="text-align:justify;font-size: 10px;"><strong>DECIMO SEGUNDO.-</strong> EL COMPRADOR, y en su caso, también el FIADOR SOLIDARIO, declaran expresamente que con anterioridad a otorgamiento del crédito materia del presente contrato, EL VENDEDOR le ha proporcionado toda la información detallada sobre las tasa de interés compensatorio y moratorio así como el monto de las comisiones y gastos, inclusive las comisiones, gastos extrajudiciales y demás en los que pudiera incurrir EL VENDEDOR para la cobranza de la obligación en caso de incumplimiento. </p>
               <p style="text-align:justify;font-size: 9px;">Suscribo en la ciudad de ____________________ a los___________________ días del mes de ___________________ del 20_______</p>
                <br><br>
                <hr style="width: 150px;margin-left: -20;">
                <hr  style="width: 150px;margin-top: -18px;">
                <hr  style="width: 150px; margin-left: 400px;margin-top: -38px;">
               <p style="font-size: 12px;">COMPRADOR</p>
               <p style="margin-left: 250px; font-size: 12px;margin-top: -28px;">CONYUGE</p>
               <p style="margin-left: 450px;font-size: 12px;margin-top: -38px;">VENDEDOR</p>


           </div>

       </div>
   </div>

</body>
</html>
