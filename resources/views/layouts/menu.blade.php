<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="{{url('home')}}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="tool"></i>
                        <span data-key="t-authentication">Mantenimientos</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('marcas.index') }}" data-key="t-login">Marca</a></li>
                        <li><a href="{{ route('categorias.index') }}" data-key="t-register">categoria</a></li>
                        <li><a href="{{ route('subcategorias.index') }}" data-key="t-register">Subcategoria</a></li>
                        <li><a href="{{ route('colores.index') }}" data-key="t-recover-password">Color</a>
                        </li>
                        <li><a href="{{ route('modelos.index') }}" data-key="t-register">Modelos</a></li>
                        <li><a href="{{ route('sectores.index') }}" data-key="t-register">Sectores</a></li>
                        <li><a href="{{ route('impuestos.index') }}" data-key="t-register">Impuestos</a></li>
                        <li><a href="{{ route('bancos.index')}}" data-key="t-register">Bancos</a></li>
                        <li><a href="{{ route('cuentasbancarias.index')}}" data-key="t-register">Cuentas Bancarias</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="shopping-bag"></i>
                        <span data-key="t-apps">Compras</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <input type="hidden" id="url_raiz_proyecto" value="{{ url('/') }}" />
                        <li>
                            <a href="{{ route('proveedores.index') }}">
                                <span data-key="t-calendar">Proveedores</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('compras.index') }}">
                                <span data-key="t-chat">Registrar Compra</span>
                            </a>
                        </li>

                        <!--<li>
                            <a href="#">
                                <span data-key="t-chat">Compras al crédito</span>
                            </a>
                        </li> -->

                    </ul>
                </li>


                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="shopping-cart"></i>
                        <span data-key="t-components">Ventas</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('lista-precios.index') }}" data-key="t-alerts">Lista Precios</a></li>
                        <li><a href="{{ route('precios.index') }}" data-key="t-alerts">Precios</a></li>
                        <li><a href="{{ route('clientes.index') }}" data-key="t-alerts">Clientes</a></li>
                        <li><a href="{{ route('ventas.index') }}" data-key="t-buttons">Punto de Venta</a></li>
                        <li><a href=" {{ route('vendedores.index') }}" data-key="t-buttons">Vendedores</a></li>
                        <!--<li><a href="#" data-key="t-cards">Ventas al crédito</a></li> -->
                    </ul>
                </li>


                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="credit-card"></i>
                        <span data-key="t-authentication">Creditos</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{url('candados-creditos')}}" data-key="t-buttons">Candado Credito</a></li>
                        <li><a href="{{url('creditos')}}" data-key="t-buttons">Generar Credito</a></li>
                        <li><a href="{{ route('amortizacion.index') }}" data-key="t-recover-password">Amortizaciones</a></li>
                        <li><a href="{{route('creditos-pendientes.index')}}" data-key="t-recover-password">Consulta Credito</a></li>
                        <li><a href="{{route('consulta-amortizaciones.index')}}" data-key="t-lock-screen">Consulta de Amortizaciones</a></li>
                        <li><a href="{{route('creditos-reprogramacion.index')}}" data-key="t-lock-screen">Reprogramar Credito</a></li>
                        <li><a href="{{route('anular-credito.index')}}" data-key="t-lock-screen">Anular Credito</a></li>
                        <li><a href="{{route('anular-amortizaciones.index')}}" data-key="t-lock-screen">Anular Amortizaciones</a></li>
                        <li><a href="{{route('impresion-planilla.index')}}" data-key="t-lock-screen">Impresión Planilla Descuento</a></li>

                    </ul>
                </li>



                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="box"></i>
                        <span data-key="t-authentication">Caja</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">

                        <li><a href="{{ route('caja.index') }}" data-key="t-register">Sesion Caja</a></li>
                        <li><a href="{{ route('movimientos.index') }}" data-key="t-recover-password">Gestión de Movimientos</a>
                        </li>
                        <li><a href="{{ route('conceptos.index') }}" data-key="t-lock-screen">Conceptos</a></li>
                        <li><a href="{{ route('historico-caja.index') }}" data-key="t-lock-screen">Historial de caja</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="archive"></i>
                        <span data-key="t-pages">Almacén</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('productos.index') }}" data-key="t-maintenance">Registro de Producto</a></li>
                        <li><a href="{{ route('almacenes.index') }}" data-key="t-starter-page">Registro de Almacén</a></li>
                        <li><a href="{{ route('stock-location.index') }}" data-key="t-starter-page">Ubicaciones</a></li>
                        <li><a href="{{ route('vehiculos.index')}}" data-key="t-register">Vehículos</a></li>
                        <li><a href="{{ route('conductor.index') }}" data-key="t-register">Conductor</a></li>
                        <li><a href="{{ route('cargainventario.index') }}" data-key="t-coming-soon">Carga de Inventario</a></li>
                        <li><a href="{{ route('kardex.index') }}" data-key="t-coming-soon">Kardex de Producto</a></li>
                        <li><a href="{{ route('guias.index') }}" data-key="t-coming-soon">Guias</a></li>
                        <li><a href="{{ route('traslados.index') }}" data-key="t-coming-soon">Traslados</a></li>
                        <li><a href="{{ route('recepcion-mercaderia.index') }}" data-key="t-coming-soon">Control de Traslados</a></li>
                        <li><a href="{{ route('transportistas.index') }}" data-key="t-coming-soon">Transportistas</a></li>

                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="bar-chart-2"></i>
                        <span data-key="t-ui-elements">Reportes</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('reporteventas.index') }}" data-key="t-lightbox">Reporte de Ventas</a></li>
                        <li><a href="{{ route('reportecompras.index') }}" data-key="t-lightbox">Reporte de Compras</a></li>
                        <li><a href="{{ route('reporteinventarios.index') }}" data-key="t-lightbox">Reporte de Inventario</a></li>
                        <li><a href="{{ route('reporteallventas.index') }}" data-key="t-notifications">Reporte All Ventas</a>
                        </li>
                        <li><a href="{{ route('reportecuotas.index') }}" data-key="t-notifications">Reporte Cuotas Vencidas</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="bar-chart-2"></i>
                        <span data-key="t-ui-elements">Seguridad</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('modulo.index') }}" data-key="t-lightbox">Modulo</a></li>
                        <li><a href="{{ route('funcion.index') }}" data-key="t-lightbox">Función</a></li>
                        <li><a href="{{ route('acciones.index') }}" data-key="t-lightbox">Acciones</a></li>
                        <li><a href="{{ route('configuracion-acciones.index') }}" data-key="t-lightbox">Configuracion Acciones</a></li>
                        <li><a href="{{ route('permisos.index') }}" data-key="t-lightbox">Permisos</a></li>
                    </ul>
                </li>
                @can('Modulo Usuarios')

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="users"></i>
                        <span data-key="t-ui-elements">Usuarios</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{url('roles')}}" data-key="t-lightbox">Roles</a></li>
                        <li><a href="{{url('users')}}" data-key="t-range-slider">Usuario</a></li>

                </li>
            </ul>
            </li>
            @endcan



            </ul>

        </div>
        <!-- Sidebar -->
    </div>
</div>