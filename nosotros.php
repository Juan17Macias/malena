<?php
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, descripcion, precio, imagen, categoria_id FROM productos WHERE disponibilidad=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = $con->prepare("SELECT id, nombre FROM categorias");
$sql->execute();
$categorias = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre Nosotros - Malena Home </title>
  <link rel="stylesheet" href="styles.css">
</head>
<?php include 'includes/head.php'; ?>
<!--  BREAKPOINT
        .row filas 
        .col- columna 
              xs (muy pequeño)         <576px
        .col-sm- (small - pequeño)     >=576px
        .col-md- (tamaño medio)        >=768px
        .col-lg- (tamaño Grande)       >=992px
        .col-xl- (tamaño muy grande)   >=1200px
             xxl (tamaño estra grande) >=1400px
      -->

<body>
  <?php include 'includes/navbar.php'; ?>

  
  <section class="container cont-empresa">
    <div class="col-md-12 col-sm-10 quienes">
      <h2>Malena Home</h2>
      <p>En nuestra empresa de ventas de muebles, nos apasiona ayudar a nuestros clientes a crear espacios hermosos y funcionales en sus hogares. Con una amplia selección de muebles de alta calidad y diseños únicos, nos esforzamos por ofrecer opciones que se adapten a todos los estilos y necesidades. <br />

        En nuestro negocio, nos enorgullece ofrecer una experiencia de compra excepcional. Nuestro equipo de expertos en muebles está siempre dispuesto a asesorar y brindar asistencia personalizada para ayudar a nuestros clientes a tomar decisiones informadas. Valoramos las relaciones a largo plazo con nuestros clientes y nos esforzamos por superar sus expectativas en cada interacción.<br>

        Trabajamos directamente con fabricantes y proveedores confiables, lo que nos permite garantizar la calidad y durabilidad de nuestros muebles. Además, nos mantenemos al tanto de las últimas tendencias y estilos en el mundo del diseño de interiores para asegurarnos de ofrecer productos modernos y de vanguardia.

        La satisfacción del cliente es nuestra máxima prioridad. Nos aseguramos de que cada entrega se realice de manera puntual y cuidadosa, y nos esforzamos por resolver cualquier problema o inquietud de manera rápida y efectiva.<br>

        Ya sea que estés buscando amueblar una nueva casa, renovar una habitación o simplemente añadir un toque de estilo a tu hogar, estamos aquí para ayudarte. En nuestra empresa de ventas de muebles, te brindamos la calidad, el servicio y la atención personalizada que te mereces para convertir tu hogar en un espacio único y acogedor.</p>
    </div>
    <div class="row">

      <div class="container">
        <div class="row">
          <div class="col-md-6 col-sm-12 vision">
            <img src="img/nosotros/Mision.jpg" class="img-fluid" alt="">
          </div>

          <div class="col-md-6 col-sm-12 mision">
            <h3>Misión</h3>
            <p>Ofrecer a nuestros clientes una amplia selección de muebles de calidad, funcionales y elegantes, que les permita crear espacios acogedores y personalizados en sus hogares. Nos esforzamos por brindar un servicio excepcional, asesoramiento experto y soluciones de diseño innovadoras para satisfacer las necesidades y superar las expectativas de nuestros clientes.</p>
          </div>



          <div class="col-md-6 col-sm-12 mision">
            <img src="img/nosotros/vision.jpg" class="img-fluid" alt="">
          </div>

          <div class="col-md-6 col-sm-12 vision">
            <h3>Visión</h3>
            <p>Ser la tienda de muebles de referencia en nuestra comunidad, reconocida por nuestra excelencia en calidad, variedad y servicio. Buscamos ser un destino inspirador para aquellos que desean transformar sus hogares en espacios únicos y confortables. Nos esforzamos por mantenernos a la vanguardia de las tendencias y las últimas innovaciones en el diseño de interiores, y ser reconocidos como líderes en la industria del mobiliario, tanto por nuestra oferta de productos como por la experiencia de compra que brindamos a nuestros clientes.</p>
          </div>
        </div>
      </div>

  </section>

  <!-- Seccion de contacto y direccion -->
  <div class="container contacto-f">
    <div class="row">
      <div class="col-lg-6 col-sm-10 form-c">
        <h2>SUSCRÍBETE</h2>

        <p>
          Se el primero en enterarte de nuestras ofertas y novedades.
        </p>

        <form>
          <div class="mb-2">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" required>
          </div>
          <div class="mb-2">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" required>
          </div>
          <div class="mb-2">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="correo" required>
          </div>
          <div class="mb-2">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono">
          </div>
          <div class="mb-2">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje"></textarea>
          </div>
          <button type="submit" class="btn btn-dark enviar-f">Enviar</button>
        </form>
      </div>
    </div>
    <div class="col-lg-6 col-sm-12">
      <h2>Nuestra Ubicación</h2>
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1988.2843123703867!2d-74.07434071445728!3d4.670724811554075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9afe974f2c09%3A0xb2efc089b87b08f9!2sAlcobas%20Sofas!5e0!3m2!1ses!2sco!4v1685332785268!5m2!1ses!2sco" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
  </div>
  </div>
  </div>



  <!--Footer-->

  <?php include 'includes/footer.php'; ?>

  <!--Scripth Bootstrap 5-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>


</body>

</html>