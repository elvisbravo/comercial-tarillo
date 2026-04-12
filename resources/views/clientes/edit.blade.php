@extends('layouts.main')

@section('title')
Editar Cliente
@endsection

@section('css')
<!-- glightbox css -->
<link rel="stylesheet" href="{{ asset('assets/libs/glightbox/css/glightbox.min.css') }}">
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')

<div class="loader" style="position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('{{asset('img/loader-meta.gif')}}') 50% 50% no-repeat rgb(249,249,249);
        opacity: .8;">

    <div class="col-md-12" id="myDIV">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
            <div class="panel-body loader-demo" style="margin-top:200px;">
                <h1 style="color: #186A3B;font-family: 'Jomhuria', cursive;text-align:center"></h1>
                <div class="ball-pulse">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Modificar Cliente</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{url('clientes')}}" class="btn btn-primary "> <i class="dripicons-reply-all"></i> Atras</a>
                </div>

                <form id="form_cliente_update" enctype="multipart/form-data">

                    <div class="card-body">
                        <!-- Static Backdrop modal Button -->
                        <i data-feather="star"></i>

                        <div class="row">
                            <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>
                            <div class="col-md-6 col-xs-12">

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Codigo Cliente <strong style="color:red;font-size: 10px;">(Solo utilizar para clientes antiguos que no se cuente con DNI/RUC)</strong></label>
                                    <input type="text" class="form-control" placeholder="Codigo Interno!" name="codigo" id="codigo" value="{{$clientes->codigo}}">
                                </div>

                                <input type="hidden" id="id_cliente" name="id" value="{{$clientes->id}}">

                                <label class="form-label" for="formrow-email-input">Tipo Doc. Ident </label>
                                <select class="form-select" name="tipo_doc" id="documento_identidad">
                                    @foreach ($tipo_documento as $documento)
                                    @if ($documento->id == $clientes->tipo_doc)

                                    <option value="{{ $documento->id }}" selected="true">{{ $documento->nombre }}</option>

                                    @else
                                    <option value="{{ $documento->id }}">{{ $documento->nombre }}</option>
                                    @endif
                                    @endforeach

                                </select>

                                <label class="form-label" for="formrow-email-input">N° de Documento de Identidad </label>
                                <div class="input-group mb-3">
                                    <input type="numer" class="form-control " id="numero_documento" placeholder="Número de documento aqui!" value="{{$clientes->documento}}" name="documento">
                                    <button class="btn btn-primary" type="button" id="btn_consultar"><i class="bx bx-search-alt align-middle"></i></button>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Razon Social <strong style="color:red">(*)</strong></label>
                                    <input type="text" class="form-control " placeholder="Razon Social!" name="razon_social" id="razon_social" value="{{$clientes->razon_social}}" required="true">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Nombre o Razón Social aqui!" name="nomb_per" id="nomb_per" value="{{$clientes->nomb_per}}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Apellido Paterno</label>
                                    <input type="text" class="form-control" placeholder="Apellido Paterno!" name="pate_per" id="pate_per" value="{{$clientes->pate_per}}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Apellido Materno</label>
                                    <input type="text" class="form-control" placeholder="Apellido Materno!" name="mate_per" id="mate_per" value="{{$clientes->mate_per}}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Dirección Fiscal <strong style="color:red">(*)</strong></label>
                                    <input type="text" class="form-control obligatorio" placeholder="Dirección Fiscal!" name="dire_per" id="dire_per" value="{{$clientes->dire_per}}" required="true">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="id_sector_uno">Sector <strong style="color:red">(*)</strong></label>
                                    <select name="id_sector" id="id_sector_uno" class="form-control">
                                        <option value="">--Seleccionar--</option>
                                        @foreach($sector as $s)

                                        @if($clientes->id_sector== $s->id)

                                        <option value="{{$s->id}}" selected>{{$s->nomb_sec}}</option>

                                        @else

                                        <option value="{{$s->id}}">{{$s->nomb_sec}}</option>

                                        @endif

                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            <div class="col-md-6 col-xs-12">

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-password-input">Ubigeo <strong style="color:red">(*)</strong></label>

                                    @if($clientes->ubigeo_id=="")

                                    <input type="hidden" id="ubigeo_idtem" value="0">

                                    @else

                                    <input type="hidden" id="ubigeo_idtem" value="{{$clientes->ubigeo_id}}">

                                    @endif

                                    <select class="form-select select_ubigeo_partida" id="ubigeo_id" name="ubigeo_id">
                                        <option>Seleccione</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Telefono <strong style="color:red">(*)</strong></label>
                                    <input type="number" class="form-control" placeholder="Telefono!" name="telefono" id="telefono" value="{{$clientes->telefono}}" required="true">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Email</label>
                                    <input type="text" class="form-control" placeholder="Email!" name="email" id="email" value="{{$clientes->email}}">
                                </div>

                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Sexo:</label>
                                    <select name="sexo_per" id="sexo_per" class="form-control">

                                        <option value="">--Seleccionar--</option>
                                        @if($clientes->sexo_per=="MASCULINO")
                                        <option value="MASCULINO" selected>MASCULINO</option>
                                        <option value="FEMENINO">FEMENINO</option>
                                        <option value="NINGUNO">NINGUNO</option>
                                        @elseif($clientes->sexo_per=="FEMENINO")
                                        <option value="MASCULINO">MASCULINO</option>
                                        <option value="FEMENINO" selected>FEMENINO</option>
                                        <option value="NINGUNO">NINGUNO</option>
                                        @elseif($clientes->sexo_per=="FEMENINO")
                                        <option value="MASCULINO">MASCULINO</option>
                                        <option value="FEMENINO">FEMENINO</option>
                                        <option value="NINGUNO" selected>NINGUNO</option>
                                        @else
                                        <option value="MASCULINO">MASCULINO</option>
                                        <option value="FEMENINO">FEMENINO</option>
                                        <option value="NINGUNO">NINGUNO</option>

                                        @endif
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Tipo Cliente:</label>
                                    <select name="tipo_cliente" id="tipo_cliente" class="form-control">
                                        <option value="">--Seleccionar--</option>
                                        @if($clientes->tipo_cliente==0)
                                        <option value="0" selected>Nuevo</option>
                                        <option value="1">Moroso</option>
                                        @else
                                        <option value="0">Nuevo</option>
                                        <option value="1" selected>Moroso</option>
                                        @endif
                                    </select>

                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Anexo Concar</label>
                                    <input type="text" class="form-control" placeholder="Anexo Concar!" name="anexo_concar" value="{{$clientes->anexo_concar}}" id="anexo_concar">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Conyuge</label>
                                    <input type="text" class="form-control" placeholder="Conyuge!" name="conyugue" value="{{$clientes->conyugue}}" id="conyugue">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Referencia</label>
                                    <input type="text" class="form-control" placeholder="Referencia!" name="referencia" value="{{$clientes->referencia}}" id="referencia">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="foto_referencia">Foto referencia</label>
                                    <input type="file" class="form-control" name="foto_referencia" id="foto_referencia">

                                    <input type="hidden" name="imagen_path" value="{{$clientes->path_image}}">

                                    <div id="view_preview_img">
                                    @if($clientes->path_image != null || $clientes->path_image == "")
                                    <a href="{{ asset($clientes->path_image) }}" class="image-popup-desc" data-title="foto referencia cliente" id="idImage">
                                        <img id="preview" class="mt-1" src="{{ asset($clientes->path_image) }}" alt="Vista previa" style="width: 200px;height: 200px;">
                                    </a>
                                    @endif
                                    </div>
                                </div>
                            </div>


                        </div>
                        <hr style="border: 0; height: 2px; text-align: center; background-image: linear-gradient(left, #fff, #000, #fff);">
                        <hr style="border: 0; height: 2px; text-align: center; background-image: linear-gradient(left, #fff, #000, #fff);">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <h6>Direcciones y Contactos</h6>
                                <button class="btn btn-primary" onclick="agregar();">Añadir</button> <br><br>
                            </div>

                            <div class="row" id="direccionespe">

                            </div>

                            <div class="col-md-3 col-xs-12">
                            </div>
                            <div class="col-md-3 col-xs-12">
                            </div>
                            <div class="col-md-3 col-xs-12">
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <a href="{{ route('clientes.index') }}" class="btn btn-danger">CANCELAR</a>
                                <button type="submit" class="btn btn-primary" id="guardardatos">Guardar</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>


<!-- Static Backdrop Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Direcciones / Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="form-registrar-direccion" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="{{$clientes->id}}">
                    <input type="hidden" name="id" id="valor" value="0" />
                    <div class="form-group">
                        <label for="">Nombre de Contacto <strong style="color:red">(*)</strong></label>
                        <input type="text" class="form-control limpiar obligatoriodos" placeholder="Nombre de Contacto" id="nombre_contacto" name="nombre_contacto">

                    </div>
                    <div class="form-group">
                        <label for="">Dirección Contacto <strong style="color:red">(*)</strong></label>
                        <input type="text" class="form-control limpiar obligatoriodos" placeholder="Dirección Contacto" id="direccion" name="direccion">

                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="formrow-email-input">Sector <strong style="color:red">(*)</strong></label>
                        <select name="id_sector" id="id_sector" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Telefono / Celular <strong style="color:red">(*)</strong></label>
                        <input type="text" class="form-control limpiar obligatoriodos" placeholder="Telefono / Celular" id="telefo" name="telefono">

                    </div>

                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" class="form-control limpiar" placeholder="Email" id="correo" name="correo">

                    </div>

                    <div class="form-group">
                        <label class="form-label" for="formrow-password-input">Ubigeo <strong style="color:red">(*)</strong></label>

                        <select class="form-select select_ubigeo_partidados" id="ubigeo_iddos" name="ubigeo_id">
                            <option>Seleccione</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Referencias</label>
                        <textarea name="referencia" id="referencia" cols="5" rows="5" class="form-control limpiar">

                                </textarea>

                    </div>

                    <div class="form-group">
                        <label for="">Subir Imagenes</label>
                        <input type="file" class="form-control limpiar" id="fileImages" name="fileImages[]" accept="image/*" multiple>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-danger" id="remover">Remover</button>
                    <button type="submit" class="btn btn-primary" id="actualizar">Actualizar</button>

                </div>
            </form>
        </div>
    </div>
</div>


<!-- Static Backdrop Modal -->
<div class="modal fade" id="modal_imagen_direccion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenDireccion"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row" id="contenedor-imagenes">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>


@endsection

@section('js')

<!-- Sweet Alerts js -->

<!-- Required datatable js -->
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<!-- glightbox js -->
<script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>

<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<script src="{{ asset('js/editarcliente.js') }}">
</script>

@endsection