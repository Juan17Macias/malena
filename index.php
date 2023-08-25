<?php
require 'config/database.php';

// Crear la conexión a la base de datos
$db = new Database();
$con = $db->conectar();

// Consulta para obtener los productos destacados en oferta
$sql = $con->prepare("SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen,p.Alto, p.categoria_id 
FROM productos AS p
JOIN categorias AS c ON p.categoria_id = c.id
LEFT JOIN ofertas AS o ON p.id = o.producto_id
WHERE p.disponibilidad = 1 AND o.id IS NOT NULL AND o.fecha_fin >= NOW()
ORDER BY RAND() LIMIT 6");

$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener todas las categorías
$sql_categorias = $con->prepare("SELECT id, nombre FROM categorias");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

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

  <!--Slider Mobile-->

  <swiper-container class="slider-mobile" autoplay delay="5000" effect="slide">
    <!-- lazy="true" attribute will automatically render the prelaoder element -->
    <swiper-slide lazy="true">
      <img src="img/fondos/1.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>
    <swiper-slide lazy="true">
      <img src="img/fondos/2.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>
    <swiper-slide lazy="true">
      <img src="img/fondos/3.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>
    <swiper-slide lazy="true">
      <img src="img/fondos/4.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>
    <swiper-slide lazy="true">
      <img src="img/fondos/5.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>
    <swiper-slide lazy="true">
      <img src="img/fondos/6.jpg" class="d-block w-100 h-80" alt="Fondo Slider productos" loading="lazy" />
    </swiper-slide>

  </swiper-container>

  <!--Slider Desktop-->

  <section class="principal container-fluid col-sm-12">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
      <div class="carousel-inner">
        <div class="carousel-item img-carousel  active">
          <img src="img/fondos/1.jpg" class="d-block w-100" alt="Imagen 1">
        </div>
        <div class="carousel-item img-carousel">
          <img src="img/fondos/2.jpg" class="d-block w-100" alt="Imagen 2">
        </div>
        <div class="carousel-item img-carousel">
          <img src="img/fondos/3.jpg" class="d-block w-100" alt="Imagen 3">
        </div>
        <div class="carousel-item img-carousel">
          <img src="img/fondos/4.jpg" class="d-block w-100" alt="Imagen 4">
        </div>
        <div class="carousel-item img-carousel">
          <img src="img/fondos/5.jpg" class="d-block w-100" alt="Imagen 5">
        </div>
        <div class="carousel-item img-carousel">
          <img src="img/fondos/6.jpg" class="d-block w-100" alt="Imagen 6">
        </div>
      </div>
      <!--<p class="carousel-caption">Bienvenidos a MALENA-HOME</p>
      <h2 class="carousel-caption2">Brindamos la Mejor Calidad de Muebles para tu Hogar en Colombia</h2>-->
    </div>
  </section>

  <!-- SECCION PRODUCTOS DESTACADOS -->


  <div class="container-fluid productos-destacados">

    <h2 class="tittle-destacados container">Mejores Ofertas!</h2>

    <!--cards de productos Destacados -->
    <div class="container destacados">
      <div class="row ">

        <?php

        // Mezclar aleatoriamente el arreglo $resultado para seleccionar productos al azar
        shuffle($resultado);
        $contador = 0; // Inicializar un contador para llevar el registro de los productos mostrados


        foreach ($resultado as $row) {
          // Verificamos si el producto está en oferta
          $id_producto = $row['id'];
          $sql_oferta = $con->prepare("SELECT * FROM ofertas WHERE producto_id= ?");
          $sql = $con->prepare("SELECT nombre,categoria_id FROM productos WHERE disponibilidad=1");
          $sql_oferta->execute([$id_producto]);
          $resultado_oferta = $sql_oferta->fetch(PDO::FETCH_ASSOC);

          $esta_en_oferta = !empty($resultado_oferta);

          // Si el producto no está en oferta, saltar al siguiente
          if (!$esta_en_oferta) {
            continue;
          }

          // cantidad de productos en pantalla de Ofertas principal
          if ($contador >= 11) {
            break;
          }
          // Obtener la imagen del producto
          $id = $row['id'];
          $sql_img = $con->prepare("SELECT imagen FROM productos WHERE id = ?");
          $sql_img->execute([$id]);
          $resultado_img = $sql_img->fetch(PDO::FETCH_ASSOC);
          $imagen_data = $resultado_img['imagen'];

          // Si no se encontró la imagen en la base de datos, mostrar una imagen predeterminada
          if (empty($imagen_data)) {
            $imagen_data = file_get_contents("img/no_product.webp");
          }

          // Codificar los datos binarios de la imagen en formato base64 para mostrar la imagen
          $imagen_base64 = base64_encode($imagen_data);
          $imagen_src = "data:image/png;base64,{$imagen_base64}";

          // Buscar las imagenes de manera local para cada producto igualando categoria y nombre para la ruta
          $categoria_id = $row['categoria_id'];
          $nombre_producto = $row['nombre'];

          $ruta_categoria = "img/productos/" . $categoria_id . "/";
          $ruta_producto = $ruta_categoria . $nombre_producto . "/";

          $contador++; // Incrementar el contador de productos mostrados
        ?>
          <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card">

              <div class="card-image">

                <img src="<?php echo $imagen_src; ?>" class="card-img-top" alt="<?php echo $row['nombre']; ?>" >
              </div>
              <div>

                <h3 class="card-title"><?php echo $row['nombre'] ?></h3>
              </div>
              <div class="card-overlay">

                <div class="row">

                </div>
                <div class="row mt-3">
                  <div class="col boton-card">
                    <button class="btn btn-outline-dark btn-card" data-bs-toggle="modal" data-bs-target="#mimodal-<?php echo $id ?>">Detalles<i class="bi bi-check2-circle"></i></button>
                  </div>
                  <div class="col boton-card">
                    <a href="https://api.whatsapp.com/send?phone=3187149192" target="_blank">
                      <button class="btn btn-outline-success btn-card">
                        Asesor <i class="bi bi-whatsapp"></i>
                      </button>
                    </a>
                  </div>
                </div>
              </div>
              <?php if ($esta_en_oferta) { ?>
                <!--  <div class="col">
                      <p class="card-text text-muted">Antes <del><?php echo number_format($row['precio'], 2, '.', ','); ?></del></p>
                    </div> --> <!--DIV DESCUENTO -->
              <?php } ?>
              <div class="col">
                <p class="card-text">Precio: $<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
              </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="mimodal-<?php echo $id ?>" tabindex="-1" aria-labelledby="mimodalLabel-<?php echo $id ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="mimodalLabel-<?php echo $id ?>"><?php echo $row['nombre'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12 col-lg-6">
                        <div id="carousel-<?php echo $id; ?>" class="carousel slide" data-bs-ride="carousel">
                          <div class="carousel-inner">
                            <?php
                            // Obtén las cuatro imágenes del producto
                            for ($i = 1; $i <= 4; $i++) {
                              $imagen_src = $ruta_producto . $i . '.jpg';
                            ?>
                              <div class="carousel-item <?php if ($i == 1) echo 'active'; ?>">
                                <img src="<?php echo $imagen_src; ?>" class="img-fluid" alt="Imagen <?php echo $i; ?>">
                              </div>
                            <?php } ?>
                          </div>
                          <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $id; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                          </button>
                          <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $id; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                          </button>
                        </div>
                        <div class="row mt-3" style="width: 100%; height: 15vh;">
                          <?php for ($i = 1; $i <= 4; $i++) { ?>
                            <div class="col-3">
                              <img src="<?php echo $ruta_producto . $i . '.jpg'; ?>" class="img-fluid" alt="Mini imagen <?php echo $i; ?>">
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-6">
                        <p class="descripcion"><?php echo $row['descripcion'] ?></p>
                        <div class="row mt-3">
                          <div class="col">
                            <p class="card-text precio">Precio: $<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col">
                            <h3 class="card-subtitle mb-2">Especificaciones:</h3>
                            <table class="table">
                              <tbody>
                                <tr>
                                  <td>Altura:</td>
                                  <td><?php echo $row['Alto'] ?></td>
                                </tr>
                                <tr>
                                  <td>Ancho:</td>
                                  <!-- <td><?php echo $row['ancho'] ?></td>-->
                                </tr>
                                <tr>
                                  <td>Peso:</td>
                                  <!-- <td><?php echo $row['peso'] ?></td>-->
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="row mt-6">
                          <div class="col boton-card">
                            <a href="https://api.whatsapp.com/send?phone=3187149192" target="_blank">
                              <button class="btn btn-outline-success">
                                Asesor <i class="bi bi-whatsapp"></i>
                              </button>
                            </a>
                          </div>
                        </div>
                        <div class="row mt-6 nota">

                          <p>Queridos clientes,

                            Para brindarles una experiencia única y personalizada, no ofrecemos opciones de pago directo en nuestro sitio web. Preferimos interactuar directamente a través de WhatsApp para guiarlos en la personalización de sus productos.

                            <a href="nota.php">Conoce mas</a>
                          </p>

                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        <?php } ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card oferta ">
            <img src="img/oferta/oferta.avif" class="card-img-top" alt="Imagen Estática">
            <div class="card-footer" style="text-align: center; padding:4px;">
              <a href="productos.php?categoria=ofertas" class="btn btn-outline-secondary">Ver más Ofertas</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  </div>
  <!--Banner de Promociones -->

  <div class="container banner-productos">
    <?php include 'circles_categorias.php'; ?>
  </div>





  <!--Enlasando Instagram -->
  <div class="container widget-instagram">
    <div class="row">
      <div class="col-md-12">
        <h3>Siguenos en:</h3>
        <!-- Reemplaza "NOMBRE_DE_USUARIO" con tu nombre de usuario de Instagram -->
        <div id="instagram-feed"></div>

        <script>
          // Get the Instagram feed
          var feed = new Instafeed({
            accessToken: '<YOUR INSTAGRAM ACCESS TOKEN>',
            get: 'user',
            userId: '<YOUR INSTAGRAM USER ID>',
            limit: 12,
            template: '<div class="instagram-post"><img src="{{image}}" alt="{{caption}}"></div>'
          });

          // Render the feed to the DOM
          feed.render('#instagram-feed');
        </script>

      </div>
    </div>
  </div>
  </div>

  <!--Footer-->
  <?php include 'includes/footer.php'; ?>


  <!--Scripth Bootstrap 5-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script async src="//www.instagram.com/embed.js"></script>


</body>

</html>