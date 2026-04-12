<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Sistema Comercial Tarrillo</title>

  <!-- Fonts -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- End fonts -->

  <!-- Favicons -->
  <link href="{{ asset('fontsedes/assets/img/favicon.png') }}" rel="icon">
  <link href="fontsedes/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="fontsedes/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="fontsedes/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="fontsedes/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="fontsedes/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="fontsedes/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Variables CSS Files. Uncomment your preferred color scheme -->
  <link href="fontsedes/assets/css/variables.css" rel="stylesheet">
  <!-- <link href="assets/css/variables-blue.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-green.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-orange.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-purple.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-red.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-pink.css" rel="stylesheet"> -->

  <!-- Template Main CSS File -->
  <link href="fontsedes/assets/css/main.css" rel="stylesheet">

     <!-- Sweet Alert-->
     <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

  <!-- =======================================================
  * Template Name: HeroBiz - v2.1.0
  * Template URL: https://bootstrapmade.com/herobiz-bootstrap-business-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top" data-scrollto-offset="0">
    <div class="container-fluid d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center scrollto me-auto me-lg-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <input type="hidden" id="url_raiz_proyecto" value="{{ url('/') }}" />

      </a>
      <nav id="navbar" class="navbar">

        <i class="bi bi-list mobile-nav-toggle d-none"></i>
      </nav><!-- .navbar -->



    </div>
  </header><!-- End Header -->

  <section id="hero-animated" class="hero-animated d-flex align-items-center">
    <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative" data-aos="zoom-out">
      <img src="fontsedes/assets/img/hero-carousel/hero-carousel-3.svg" class="img-fluid animated">
      <h2>Comercial <span>Tarrillo</span></h2>
      <p>Bienvenido al Sistema Comercial Tarrillo</p>

    </div>
  </section>

  <main id="main">

    <!-- ======= Featured Services Section ======= -->
    <section id="featured-services" class="featured-services">
      <div class="container">

      <h1 style="text-align: center;">Todas las Sedes</h1>
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <a href="{{ route('sedes.create') }}" type="buton" class="btn btn-primary"><i class="btn-icon-prepend" data-feather="plus"></i>AGREGAR</a>

        </div>

        <div class="row gy-4" id="list_sedes">

        @foreach( $sedes as $sede)

          <div class="col-xl-3 col-md-6 " >
            <div class="" style="background-color:white">
              <div class="icon"><i class="bi bi-broadcast icon" style="font-size: 30px;text-align: center;color:aquamarine"></i></div>

              <div class="flex-shrink-0" >
                    <div class="dropdown">
                        <button class="btn btn-link font-size-16 shadow-none text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-horizontal-rounded"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Editar</a></li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="correlativos({{ $sede->id }})">Correlativos</a></li>
                        </ul>
                    </div>
                </div>
              <h4><a href="#" class="stretched-link">{{ $sede->nombre }}</a></h4>
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Dirección:</label>
              <p> {{ $sede->direccion }}</p>
              <label class="tx-11 fw-bolder mb-0 text-uppercase">Telefono:</label>
              <p> {{ $sede->telefono }}</p>
              <div class="mt-3">
                        <div class="col">
                            <div class="d-inline-block me-1">Prueba</div>
                            <div class="form-check form-switch d-inline-block">
                                @if( $sede->tipo_envio == 0 )
                                <input type="checkbox" class="form-check-input tipo_envio" style="cursor: pointer;" value="{{ $sede->id }}" />
                                @else
                                <input type="checkbox" class="form-check-input tipo_envio" style="cursor: pointer;" value="{{ $sede->id }}" checked />
                                @endif
                                <label for="" class="form-check-label">Producción</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="col">
                            <div class="d-inline-block me-1">Inactivo</div>
                            <div class="form-check form-switch d-inline-block">
                                @if( $sede->estado == 2 )
                                <input type="checkbox" class="form-check-input estado" style="cursor: pointer;" value="{{ $sede->id }}" />
                                @else
                                <input type="checkbox" class="form-check-input estado" style="cursor: pointer;" value="{{ $sede->id }}" checked />
                                @endif
                                <label for="" class="form-check-label">Activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-grid">
                        <button type="button" class="btn btn-primary btn-block ingresar_sede" data-id="{{ $sede->id }}">INGRESAR</button>
                    </div>
            </div>
          </div><!-- End Service Item -->
          @endforeach


        </div>

      </div>
    </section><!-- End Featured Services Section -->


    <!-- ======= F.A.Q Section ======= -->


  </main><!-- End #main -->

  <!-- modal -->

<div class="modal fade bd-example-modal-lg" id="modal_correlativos" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title h4" id="myLargeModalLabel">Correlativos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form id="form_correlativos">
                <input type="hidden" name="idsede" id="idsede" value="0">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <select class="form-select" id="comprobante" name="comprobante">
                                    @foreach ($comprobantes as $comprobante)
                                    <option value="{{ $comprobante->id }}">{{ $comprobante->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="agregar_comprobante">Agregar</button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>COMPROBANTE</th>
                                <th>PRUEBA</th>
                                <th>PRODUCCION</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="bodyComprobantes">
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>


  <!-- ======= Footer ======= -->


  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="fontsedes/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="fontsedes/assets/vendor/aos/aos.js"></script>
  <script src="fontsedes/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="fontsedes/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="fontsedes/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="fontsedes/assets/vendor/php-email-form/validate.js"></script>



  <!-- Template Main JS File -->
  <script src="fontsedes/assets/js/main.js"></script>

  <!-- Sweet Alerts js -->
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->

  <script src="{{ asset('js/sedes.js') }}">
    </script>

</body>

</html>
