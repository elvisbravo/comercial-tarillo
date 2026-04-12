<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Crear Sede</title>

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
  <link href="{{ asset('fontsedes/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('fontsedes/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('fontsedes/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('fontsedes/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('fontsedes/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Variables CSS Files. Uncomment your preferred color scheme -->
  <link href="{{ asset('fontsedes/assets/css/variables.css') }}" rel="stylesheet">
  <!-- <link href="assets/css/variables-blue.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-green.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-orange.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-purple.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-red.css" rel="stylesheet"> -->
  <!-- <link href="assets/css/variables-pink.css" rel="stylesheet"> -->

  <!-- Template Main CSS File -->
  <link href="{{ asset('fontsedes/assets/css/main.css') }}" rel="stylesheet">

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

      <h2>Comercial <span>Tarrillo</span></h2>
      <p>Crear Sede</p>

    </div>
  </section>

  <main id="main">

    <!-- ======= Featured Services Section ======= -->
    <section id="featured-services" class="featured-services">
      <div class="container">

        <div class="row gy-4" id="list_sedes">
        @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif

            <!--<form>-->
            {!! Form::open(array('route' => 'sedes.store','method'=>'POST','autocomplete'=>'off')) !!}
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            {!! Form::text('nombre', null, array('placeholder' => 'Escribe un nombre para la sede','class' => 'form-control','id'=>'nombre')) !!}
                                            <!--<input type="text" class="form-control" placeholder="Escribe un nombre para la sede" name="nombre" id="nombre">-->
                                        </div>
                                    </div><!-- Col -->
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>
                                            {!! Form::text('direccion', null, array('placeholder' => 'Escribe la dirección de la sede','class' => 'form-control','id'=>'direccion')) !!}
                                            <!--<input type="text" class="form-control" placeholder="Escribe la dirección de la sede" name="direccion" id="direccion">-->
                                        </div>
                                    </div><!-- Col -->
                                </div><!-- Row -->
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            {!! Form::text('telefono', null, array('placeholder' => 'Escribe teléfono de la sede','class' => 'form-control','id'=>'telefono')) !!}
                                            <!--<input type="text" class="form-control" placeholder="Escribe teléfono de la sede" name="telefono" id="telefono">-->
                                        </div>
                                    </div><!-- Col -->
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label">Anexo</label>
                                            {!! Form::text('anexo', null, array('placeholder' => 'Escribe el anexo de la sede','class' => 'form-control','id'=>'anexo')) !!}
                                            <!--<input type="text" class="form-control" placeholder="Escribe el anexo de la sede" name="anexo" id="anexo">-->
                                        </div>
                                    </div><!-- Col -->

                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label for="exampleFormControlSelect1" class="form-label">Sede Principal</label>
                                            <select class="form-select" id="principal" name="principal">
                                                <option value="0" selected>No</option>
                                                <option value="1">Si</option>
                                            </select>
                                        </div>
                                    </div><!-- Col -->

                                </div><!-- Row -->

                                <button type="submit" class="btn btn-primary submit">CREAR</button>
                                <a href="{{ route('sedes.index') }}" class="btn btn-danger">CANCELAR</a>

                            {!! Form::close() !!}
                            <!--</form>-->



        </div>

      </div>
    </section><!-- End Featured Services Section -->


    <!-- ======= F.A.Q Section ======= -->


  </main><!-- End #main -->

  <!-- modal -->


  <!-- ======= Footer ======= -->


  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('fontsedes/assets/vendor/php-email-form/validate.js') }}"></script>



  <!-- Template Main JS File -->
  <script src="{{ asset('fontsedes/assets/js/main.js') }}"></script>

  <!-- Sweet Alerts js -->
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Required datatable js -->

  <script src="{{ asset('js/sedes.js') }}">
    </script>

</body>

</html>
