@extends('layouts.main')

@section('title')
Módulo
@endsection

@section('css')
<!-- Sweet Alert-->
<link href="{{asset('js/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />

<!-- choices css -->
<link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"
    type="text/css" />

<link href="{{asset('css/modulo_padre.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('contenido')
<!--<div class="loader" style="position: fixed;
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
    </div>-->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Listado Módulos</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">

                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="abrimodal(0)"
                        data-bs-toggle="modal" data-bs-target="#staticBackdrop"> <i class="btn-icon-prepend"
                            data-feather="plus"></i> crear</button>

                </div>

                <div class="card-body">

                    <!-- Static Backdrop modal Button -->
                    <i data-feather="star"></i>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Nombre</th>
                                <th width="15%">Icono</th>
                                <th width="15%">Modulo padre</th>
                                <th width="20%">Url</th>
                                <th width="10%">Orden</th>
                                <th width="20%">Acciones</th>
                            </tr>
                        </thead>


                        <tbody id="listadecolores">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Formulario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="valor" value="0" />
                <div class="form-group">
                    <label for="">Módulo <strong style="color:red">(*)</strong></label>
                    <input type="text" class="form-control obligatorio limpiar" placeholder="Nombre del módulo"
                        id="name" autocomplete="off">
                </div>
                <div class="form-group mt-8rem">
                    <label for="">Icono <strong style="color:red">(*)</strong></label>
                    <input type="text" class="form-control obligatorio limpiar" placeholder="Icono del módulo (ej: fas fa-home)"
                        id="icon" autocomplete="off">
                </div>
                <div class="form-group mt-8rem">
                    <label for="">Módulo padre <strong style="color:red">(*)</strong></label>
                    <select name="" id="idmodulo_padre" class="form-control limpiar">
                    </select>
                </div>
                <div class="form-group mt-8rem">
                    <label for="">Url<strong style="color:red">(*)</strong></label>
                    <input type="text" class="form-control obligatorio limpiar" placeholder="Orden en que será mostrado"
                        id="url" autocomplete="off">
                </div>
                <div class="form-group mt-8rem">
                    <label for="">Order<strong style="color:red">(*)</strong></label>
                    <input type="number" class="form-control obligatorio limpiar"
                        placeholder="Orden en que será mostrado" id="order" autocomplete="off">
                </div>
                <!--<div class="mb-3"><label for="choices-multiple-default"
                        class="form-label font-size-13 text-muted">Default</label>
                    <div class="basic-multi-select css-b62m3t-container">
                        <span id="react-select-4-live-region" class="css-7pg0cj-a11yText"></span>
                        <span aria-live="polite" aria-atomic="false" aria-relevant="additions text" role="log" class="css-7pg0cj-a11yText"></span>
                        <div class="select__control css-13cymwt-control">
                            <div
                                class="select__value-container select__value-container--is-multi select__value-container--has-value css-1dyz3mf">
                                <div class="select__multi-value css-jnxwpw-multiValue">
                                    <div class="select__multi-value__label css-1clrecx">Choice 1</div>
                                    <div role="button" class="select__multi-value__remove css-mb32m0"
                                        aria-label="Remove Choice 1"><svg height="14" width="14" viewBox="0 0 20 20"
                                            aria-hidden="true" focusable="false" class="css-8mmkcg">
                                            <path
                                                d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.030-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z">
                                            </path>
                                        </svg></div>
                                </div>
                                <div class="select__multi-value css-jnxwpw-multiValue">
                                    <div class="select__multi-value__label css-1clrecx">Choice 2</div>
                                    <div role="button" class="select__multi-value__remove css-mb32m0"
                                        aria-label="Remove Choice 2"><svg height="14" width="14" viewBox="0 0 20 20"
                                            aria-hidden="true" focusable="false" class="css-8mmkcg">
                                            <path
                                                d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.030-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z">
                                            </path>
                                        </svg></div>
                                </div>
                                <div class="select__multi-value css-jnxwpw-multiValue">
                                    <div class="select__multi-value__label css-1clrecx">Choice 3</div>
                                    <div role="button" class="select__multi-value__remove css-mb32m0"
                                        aria-label="Remove Choice 3"><svg height="14" width="14" viewBox="0 0 20 20"
                                            aria-hidden="true" focusable="false" class="css-8mmkcg">
                                            <path
                                                d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.030-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z">
                                            </path>
                                        </svg></div>
                                </div>
                                <div class="select__input-container css-19bb58m" data-value=""><input
                                        class="select__input" autocapitalize="none" autocomplete="off" autocorrect="off"
                                        id="react-select-4-input" spellcheck="false" tabindex="0" type="text"
                                        aria-autocomplete="list" aria-expanded="false" aria-haspopup="true"
                                        role="combobox" aria-activedescendant="" value=""
                                        style="color: inherit; background: 0px center; opacity: 1; width: 100%; grid-area: 1 / 2; font: inherit; min-width: 2px; border: 0px; margin: 0px; outline: 0px; padding: 0px;">
                                </div>
                            </div>
                            <div class="select__indicators css-1wy0on6">
                                <div class="select__indicator select__clear-indicator css-1xc3v61-indicatorContainer"
                                    aria-hidden="true"><svg height="20" width="20" viewBox="0 0 20 20"
                                        aria-hidden="true" focusable="false" class="css-8mmkcg">
                                        <path
                                            d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.030-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z">
                                        </path>
                                    </svg></div><span
                                    class="select__indicator-separator css-1u9des2-indicatorSeparator"></span>
                                <div class="select__indicator select__dropdown-indicator css-1xc3v61-indicatorContainer"
                                    aria-hidden="true"><svg height="20" width="20" viewBox="0 0 20 20"
                                        aria-hidden="true" focusable="false" class="css-8mmkcg">
                                        <path
                                            d="M4.516 7.548c0.436-0.446 1.043-0.481 1.576 0l3.908 3.747 3.908-3.747c0.533-0.481 1.141-0.446 1.574 0 0.436 0.445 0.408 1.197 0 1.615-0.406 0.418-4.695 4.502-4.695 4.502-0.217 0.223-0.502 0.335-0.787 0.335s-0.57-0.112-0.789-0.335c0 0-4.287-4.084-4.695-4.502s-0.436-1.17 0-1.615z">
                                        </path>
                                    </svg></div>
                            </div>
                        </div>
                    </div>
                </div>-->
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
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<script src="{{ asset('js/modulo.js') }}"></script>

@endsection