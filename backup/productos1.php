<?php
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

if (!$con) {
  $errorInfo = $con->errorInfo();
  die("Error de conexión: " . $errorInfo[2]);
}

$sql = $con->prepare("SELECT id, nombre, descripcion, precio, imagen, categoria_id FROM productos WHERE disponibilidad=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = $con->prepare("SELECT id, nombre FROM categorias");
$sql->execute();
$categorias = $sql->fetchAll(PDO::FETCH_ASSOC);
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

  <!-- SECCION PRODUCTOS DESTACADOS -->

  <div class="container-fluid productos-destacados mt-3">

    <h2 class="container t-productos">Todos Los Productos</h2>


    <div class="container-fluid d-flex flex-column">
      <div class="row">

        <!-- Columna de categorías (fixed) -->
        <div class="col-md-3 col-12 d-none d-sm-block d-flex flex-column">
          <div class="card mt-3 categorias position-fixed col-2 ">
            <div class="card-header ">
              Categorías
            </div>

            <!--Menu Desktop -->

            <div class="card-body submenu-categorias">
              <div id="categories" class="d-flex flex-column justify-content-start align-items-start">
                <a href="#" data-filter="all" class="btn btn-primary btn-sm me-2">
                  <img class="icon-categorias" src="svg/All.svg" alt=""> Todos
                </a>
                <?php foreach ($categorias as $categoria) {
                  $svg_path = 'svg/' . strtolower($categoria['nombre']) . '.svg';
                  if (!file_exists($svg_path)) {
                    // Si no existe el archivo SVG para esta categoría, se carga un archivo genérico
                    $svg_path = 'svg/default.svg';
                  }
                ?>
                  <a href="#" data-filter="<?php echo $categoria['id']; ?>" class="btn btn-primary btn-sm me-2 flex-row">
                    <img class="icon-categorias" src="<?php echo $svg_path; ?>" alt="">
                    <?php echo ucfirst($categoria['nombre']); ?>
                  </a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <!--Categorias Menu Mobile-->
        <div class="col-sm-12  col-md-10 d-sm-none d-flex flex-column">
          <div class="card mt-3 categorias">
            <div class="card-header">
              Categorías
            </div>
            <div class="card-body submenu-categorias-m">
              <div id="categories" class="d-flex flex-row justify-content-start align-items-start">
                <a href="#" data-filter="all" class="btn btn-sm me-2">
                  <img class="icon-categorias-m" src="svg/All.svg" alt=""> Todos
                </a>
                <?php foreach ($categorias as $categoria) { ?>
                  <a href="#" data-filter="<?php echo $categoria['id']; ?>" class="btn btn-sm me-2 flex-row">
                    <img class="icon-categorias-m" src="svg/<?php echo strtolower($categoria['nombre']); ?>.svg" alt="">
                    <?php echo ucfirst($categoria['nombre']); ?>
                  </a>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>



        <!-- Columna de productos (responsive) -->
       <div class="col-md-8 mt-3">
          <div class="row" id="products">


            <?php
            // Obtener las ofertas
            $sql_oferta = $con->prepare("SELECT * FROM ofertas WHERE producto_id = ?");
            $sql_img = $con->prepare("SELECT imagen FROM productos WHERE id = ?");
            $sql = $con->prepare("SELECT nombre,categoria_id FROM productos WHERE disponibilidad=1");

            foreach ($resultado as $row) {


              // Verificar si el producto está en oferta
              $id_producto = $row['id'];
              $sql_oferta->execute([$id_producto]);
              $resultado_oferta = $sql_oferta->fetch(PDO::FETCH_ASSOC);
              $esta_en_oferta = !empty($resultado_oferta);

              // Obtener los datos binarios de la imagen desde la base de datos
              $id = $row['id'];
              $sql_img->execute([$id]);
              $resultado_img = $sql_img->fetch(PDO::FETCH_ASSOC);
              $imagen_data = $resultado_img['imagen'];

              // Si no se encontró la imagen en la base de datos, mostrar una imagen predeterminada
              if (empty($imagen_data)) {
                $imagen_data = file_get_contents("img/no_product.webp");
              }

              // Codificar los datos binarios en formato base64 para mostrar la imagen
              $imagen_base64 = base64_encode($imagen_data);
              $imagen_src = "data:image/jpg;base64,{$imagen_base64}";

              // Buscar e igualar nombres para la ruta de imagnes local 
              $categoria_id = $row['categoria_id'];
              $nombre_producto = $row['nombre'];

              $ruta_categoria = "img/productos/" . $categoria_id . "/";
              $ruta_producto = $ruta_categoria . $nombre_producto . "/";

            ?>

            

              <div class="col-sm-12 col-md-6 col-lg-3 product category-<?php echo $row['categoria_id']; ?>" data-category="<?php echo $row['categoria_id']; ?>">
                <div class="card mb-3 rounded-3" style="width: 240px; height: 300px;">
                  <div class="card-image">
                    <img src="<?php echo $imagen_src; ?>" class="card-img-top" alt="<?php echo $row['nombre']; ?>" style="height: 25vh;">
                  </div>
                  <div class="card-body">
                    <h3 class="card-title"><?php echo $row['nombre'] ?></h3>
                    <div class="card-overlay"> <!-- Overlay de las cards -->
                      <div class="row">
                        <?php if ($esta_en_oferta) { ?>
                          <div class="col">
                            <p class="card-text text-muted">Antes <del><?php echo number_format($row['precio'], 2, '.', ','); ?></del></p>
                          </div>
                        <?php } ?>
                        <div class="col">
                          <p class="card-text">Precio: $<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
                        </div>
                      </div>
                      <div class="row mt-3">
                        <div class="col boton-card">
                          <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#mimodal-<?php echo $id ?>">Ver producto</button>
                        </div>
                        <div class="col boton-card">
                          <a href="https://api.whatsapp.com/send?phone=3187149192" target="_blank">
                            <button class="btn btn-outline-success">
                              Asesor <i class="bi bi-whatsapp"></i>
                            </button>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
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

                          <p><?php echo $row['descripcion'] ?></p>
                          <div class="row mt-3">
                            <div class="col">
                              <p class="card-text">Precio: $<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
                            </div>
                          </div>
                          <div class="row mt-3">
                            <div class="col">
                              <h3 class="card-subtitle mb-2">Especificaciones:</h3>
                              <table class="table">
                                <tbody>
                                  <tr>
                                    <td>Altura:</td>
                                    <!--<td><?php echo $row['altura'] ?></td>-->
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
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>
          </div>
        </div>


      </div>
    </div>
  </div>





  <!--Footer-->

  <?php include 'includes/footer.php'; ?>

  <!--Scripth Bootstrap 5-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

  <script src="js/filtro-categoria.js"></script>

</body>

</html>