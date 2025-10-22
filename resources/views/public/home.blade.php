@extends('layouts.app')
@section('title', 'Inicio - CleanWash')

@section('content')

<!-- Hero -->
<section id="inicio" class="pt-0">
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="https://cdn.pixabay.com/photo/2014/12/14/16/05/laundry-saloon-567951_1280.jpg" class="d-block w-100" height="550" style="object-fit: cover;">
        <div class="carousel-caption">
          <h3 class="fw-bold text-white">Tu ropa limpia y fresca, siempre a tiempo</h3>
          <p>Servicio profesional de lavandería y planchado.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="https://img.freepik.com/vector-gratis/interior-lavanderia-dibujos-animados_52683-84120.jpg?semt=ais_hybrid&w=740&q=80" class="d-block w-100" height="550" style="object-fit: cover;">
        <div class="carousel-caption">
          <h3 class="fw-bold text-white">Recogemos y entregamos en tu hogar</h3>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Años de experiencia -->
<section class="text-center bg-light" data-aos>
  <div class="container">
    <h2 class="section-title">Más de <span class="text-primary">10 años</span> de experiencia</h2>
    <p>Confianza, calidad y puntualidad en cada servicio.</p>
  </div>
</section>

<!-- Servicios -->
<section id="servicios" data-aos>
  <div class="container text-center">
    <h2 class="section-title">Nuestros Servicios</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-4">
          <i class="bi bi-droplet-half fs-1 text-primary"></i>
          <h5 class="mt-3 fw-bold">Lavado y Planchado</h5>
          <p>Ropa limpia, sin arrugas y lista para usar.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <i class="bi bi-basket2 fs-1 text-primary"></i>
          <h5 class="mt-3 fw-bold">Lavado en seco</h5>
          <p>Cuidado especial para prendas delicadas o finas.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4">
          <i class="bi bi-house fs-1 text-primary"></i>
          <h5 class="mt-3 fw-bold">Recojo a domicilio</h5>
          <p>Vamos a tu casa y devolvemos tu ropa lista.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Equipos -->
<section id="equipos" class="bg-light" data-aos>
  <div class="container text-center">
    <h2 class="section-title">Nuestros Equipos</h2>
    <div class="row g-4">
      <div class="col-md-4"><img src="https://images.unsplash.com/photo-1581579188871-2c3a37c963ec" class="img-fluid rounded shadow"></div>
      <div class="col-md-4"><img src="https://images.unsplash.com/photo-1581579202884-5c5c992b016d" class="img-fluid rounded shadow"></div>
      <div class="col-md-4"><img src="https://images.unsplash.com/photo-1579077208479-69b98230c86c" class="img-fluid rounded shadow"></div>
    </div>
  </div>
</section>

<!-- Opiniones -->
<section id="comentarios" data-aos>
  <div class="container text-center">
    <h2 class="section-title">Opiniones de nuestros clientes</h2>
    <div class="row g-4">
      <div class="col-md-4"><div class="card p-3"><p>"Excelente servicio, muy puntuales."</p><h6 class="text-primary fw-bold">— María López</h6></div></div>
      <div class="col-md-4"><div class="card p-3"><p>"Ropa impecable, atención 10/10."</p><h6 class="text-primary fw-bold">— Carlos Gómez</h6></div></div>
      <div class="col-md-4"><div class="card p-3"><p>"La mejor lavandería de la ciudad."</p><h6 class="text-primary fw-bold">— Ana Torres</h6></div></div>
    </div>
  </div>
</section>

<!-- Contacto -->
<section id="contacto" class="bg-light" data-aos>
  <div class="container text-center">
    <h2 class="section-title">Contáctanos</h2>
    <form class="row g-3 justify-content-center">
      <div class="col-md-5"><input type="text" class="form-control" placeholder="Tu nombre"></div>
      <div class="col-md-5"><input type="email" class="form-control" placeholder="Tu correo"></div>
      <div class="col-md-10"><textarea class="form-control" rows="4" placeholder="Tu mensaje"></textarea></div>
      <div class="col-md-10"><button class="btn btn-primary px-5">Enviar mensaje</button></div>
    </form>
  </div>
</section>

@endsection