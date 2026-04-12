@extends('layouts.main')

@section('title')
Crear Cliente
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- DataTables -->

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
                <h4 class="mb-sm-0 font-size-18">Crear Clientes</h4>

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

                <div class="card-body">

                    <!-- Static Backdrop modal Button -->
                    <i data-feather="star"></i>

                    <form id="form_create_cliente" enctype="multipart/form-data">
                        <div class="row">
                            <p style="color:red">Todos los campos marcados con (*) son obligatorios</p>
                            <div class="col-md-6 col-xs-12">
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Codigo Cliente <strong style="color:red;font-size: 10px;">(Solo utilizar para clientes antiguos que no se cuente con DNI/RUC)</strong></label>
                                    <input type="text" class="form-control" placeholder="Codigo Interno!" name="codigo" id="codigo">
                                </div>

                                <label class="form-label" for="formrow-email-input">Tipo Doc. Ident </label>
                                <select class="form-select mb-3" name="tipo_doc" id="documento_identidad" required>
                                    <option value="">--Seleccionar--</option>
                                    @foreach ($tipo_documento as $documento)
                                    <option value="{{ $documento->id }}">{{ $documento->nombre }}</option>
                                    @endforeach
                                </select>

                                <label class="form-label" for="formrow-email-input">N° de Documento de Identidad </label>
                                <div class="input-group mb-3">
                                    <input type="numer" class="form-control" id="numero_documento" placeholder="Número de documento aqui!" name="documento" required>
                                    <button class="btn btn-primary" type="button" id="btn_consultar"><i class="bx bx-search-alt align-middle"></i></button>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Razon Social <strong style="color:red">(*)</strong></label>
                                    <input type="text" class="form-control obligatorio" placeholder="Razon Social!" name="razon_social" id="razon_social" required="true">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Nombre o Razón Social aqui!" name="nomb_per" id="nomb_per">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Apellido Paterno</label>
                                    <input type="text" class="form-control" placeholder="Apellido Paterno!" name="pate_per" id="pate_per">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Apellido Materno</label>
                                    <input type="text" class="form-control" placeholder="Apellido Materno!" name="mate_per" id="mate_per">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Dirección Fiscal <strong style="color:red">(*)</strong></label>
                                    <input type="text" class="form-control obligatorio" placeholder="Dirección Fiscal!" name="dire_per" id="dire_per" required="true">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Sector <strong style="color:red">(*)</strong></label>
                                    <select name="id_sector" id="id_sector" class="form-control" required>
                                        <option value="">--Seleccionar--</option>
                                        @foreach($sector as $s)
                                        <option value="{{$s->id}}">{{$s->nomb_sec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-12">
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-password-input">Ubigeo <strong style="color:red">(*)</strong></label>
                                    <select class="form-select select_ubigeo_partida" id="ubigeo_id" name="ubigeo_id" required>
                                        <option>Seleccione</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Telefono <strong style="color:red">(*)</strong></label>
                                    <input type="number" class="form-control" placeholder="Telefono!" name="telefono" id="telefono" required="true">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Email</label>
                                    <input type="text" class="form-control" placeholder="Email!" name="email" id="email">
                                </div>

                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Sexo:</label>
                                    <select name="sexo_per" id="sexo_per" class="form-control">

                                        <option value="">--Seleccionar--</option>
                                        <option value="MASCULINO">MASCULINO</option>
                                        <option value="FEMENINO">FEMENINO</option>
                                        <option value="NINGUNO">NINGUNO</option>
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Tipo Cliente:</label>
                                    <select name="tipo_cliente" id="tipo_cliente" class="form-control">
                                        <option value="">--Seleccionar--</option>
                                        <option value="0" selected>Nuevo</option>
                                        <option value="1">Moroso</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Anexo Concar</label>
                                    <input type="text" class="form-control" placeholder="Anexo Concar!" name="anexo_concar" id="anexo_concar">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Conyuge</label>
                                    <input type="text" class="form-control" placeholder="Conyuge!" name="conyugue" id="conyugue">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Referencia</label>
                                    <input type="text" class="form-control" placeholder="Referencia!" name="referencia" id="referencia">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formrow-email-input">Foto de referencia</label>
                                    <input type="file" class="form-control" name="foto_referencia" id="foto_referencia">

                                    <div id="vista_previa">
                                       
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 m-auto text-center">
                                <a href="{{ route('clientes.index') }}" class="btn btn-danger">CANCELAR</a>
                                <button type="submit" class="btn btn-primary" id="guardardatos">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Static Backdrop Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Formulario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="name" id="valor" value="0" />
                <div class="form-group">
                    <label for="">Nombre de la Catogoria</label>
                    <input type="text" class="form-control" placeholder="Nombre de la Catogoria" id="categoria">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
                <button type="button" class="btn btn-primary" id="actualizar">Actualizar</button>
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
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('js/clientes-crear.js') }}">
</script>

@endsection