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

  <!-- SECCION FILTROS CATALOGO-->
  <section class="catalogo">
    <h2 class="container t-productos" style="
    margin-top: 0.5em;">Todos Los Productos</h2>
    <div class="filtro-categorias container">
      <div class="row">
        <div class="col">
          <a class="categorias-p" href="productos.php">Todos</a>
        </div>
        <?php
        // Conexión a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "makarlu-prueba2";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
          die("Conexión fallida: " . $conn->connect_error);
        }


        // Consulta para obtener las categorías
        $categoriasSql = "SELECT id, nombre FROM categorias";
        $categoriasResult = $conn->query($categoriasSql);

        if ($categoriasResult->num_rows > 0) {
          while ($row = $categoriasResult->fetch_assoc()) {
            $categoriaId = $row["id"];
            $categoriaNombre = $row["nombre"];
        ?>
            <div class="col">
              <a class="categorias-p" href="productos.php?categoria=<?php echo $categoriaId; ?>"><?php echo $categoriaNombre; ?></a>
            </div>

        <?php

          }
        } else {
          echo "<p>No hay categorías disponibles.</p>";
        }
        ?>
        <div class="col">
          <!-- Enlace para mostrar productos en oferta -->
          <a class="categorias-p" href="productos.php?categoria=ofertas">Ofertas</a>
        </div>
      </div>
    </div>

    <!-- SECCION PRODUCTOS DESTACADOS -->

    <div class="productos container">
      <div class="row">
        <?php
        // Obtener la fecha actual en formato SQL (YYYY-MM-DD)
        $fechaActual = date('Y-m-d');

        // Construir la consulta para obtener los productos
        $sql = "SELECT p.id, p.nombre, p.precio, p.descripcion, p.imagen, p.categoria_id
        FROM productos p
        LEFT JOIN ofertas o ON p.id = o.producto_id
        WHERE (o.fecha_fin >= '$fechaActual' OR o.fecha_fin IS NULL)";

        // Verificar si se ha proporcionado el parámetro de categoría
        if (isset($_GET['categoria'])) {
          $categoria = $_GET['categoria'];

          // Si se selecciona la categoría "Ofertas", ajustar la consulta para mostrar solo productos en oferta
          if ($categoria == "ofertas") {
            $sql = "SELECT p.id, p.nombre, p.precio, p.descripcion, p.imagen, p.categoria_id
            FROM productos p
            INNER JOIN ofertas o ON p.id = o.producto_id
            WHERE o.fecha_fin >= '$fechaActual'";
          } elseif ($categoria != "todos") {
            // Agregar la cláusula WHERE para filtrar por categoría, si no es "todos"
            $sql .= " AND p.categoria_id = '$categoria'";
          }
        }

        // PAGINACION

        $resultTotal = $conn->query($sql); // Consulta sin límite para obtener el número total de productos
        $totalProductos = $resultTotal->num_rows;

        // Calcular el número de página actual a partir del parámetro 'page' en la URL
        $productosPorPagina = 12;
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
          $paginaActual = intval($_GET['page']);
        } else {
          $paginaActual = 1; // Por defecto, mostrar la primera página si no se proporciona o es inválido el parámetro 'page'
        }

        // Calcular el desplazamiento para la cláusula LIMIT
        $offset = ($paginaActual - 1) * $productosPorPagina;

        // Actualizar la consulta SQL para usar la cláusula LIMIT
        $sql .= " LIMIT $offset, $productosPorPagina";


        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $nombre = $row["nombre"];
            $precio = $row["precio"];
            $descripcion = $row["descripcion"];

            // Obtener los datos binarios de la imagen
            $imagen_data = $row['imagen'];

            // Si no se encontró la imagen en la base de datos, mostrar una imagen predeterminada
            if (empty($imagen_data)) {
              $imagen_data = file_get_contents("img/no_product.webp");
            }

            // Codificar los datos binarios de la imagen en formato base64 para mostrar la imagen
            $imagen_base64 = base64_encode($imagen_data);
            $imagen_src = "data:image/png;base64,{$imagen_base64}";
            $id = $row['id']; //

            // Buscar las imagenes de manera local para cada producto igualando categoria y nombre para la ruta
            $categoria_id = $row['categoria_id'];
            $nombre_producto = $row['nombre'];

            $ruta_categoria = "img/productos/" . $categoria_id . "/";
            $ruta_producto = $ruta_categoria . $nombre_producto . "/";

        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-5">
              <div class="card">
                <div class="card-image">
                  <img src="<?php echo $imagen_src; ?>" class="card-img-top" alt="<?php echo $nombre; ?>">
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
                </div>
                <div>
                  <h3 class="card-title"><?php echo $nombre; ?></h3>
                </div>
                <div class="col">
                  <p class="card-text">COP: $<?php echo number_format($precio, 2, '.', ','); ?></p>
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
                      <div class="col-md-12 col-lg-6 lado-izquierdo">
                        <div id="carousel-<?php echo $id; ?>" class="carousel slide" data-bs-ride="carousel">
                          <div class="carousel-inner">
                            <?php
                            // Obtén las cuatro imágenes del producto
                            for ($i = 1; $i <= 4; $i++) {
                              $imagen_src = $ruta_producto . $i . '.jpg';
                            ?>
                              <div class="carousel-item <?php if ($i == 1) echo 'active'; ?>">
                                <img src="<?php echo $imagen_src; ?>" class="img-fluid carousel-image" alt="Imagen <?php echo $i; ?>">
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
                        <div class="row mt-3">
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

        <?php
          }
        } else {
          echo "<p>No hay productos disponibles.</p>";
        }
        $conn->close();
        ?>
      </div>
    </div>

    <!--PAGINACION DE PRODUCTOS-->

    <div class="row">
      <div class="col">
        <?php
        $totalPaginas = ceil($totalProductos / $productosPorPagina);

        if ($totalPaginas > 1) {
          echo '<ul class="pagination">';
          for ($i = 1; $i <= $totalPaginas; $i++) {
            // Verificar si el parámetro 'categoria' está presente en la URL
            $categoriaParam = isset($_GET['categoria']) ? '&categoria=' . $_GET['categoria'] : '';

            if ($i == $paginaActual) {
              echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
              echo '<li class="page-item"><a class="page-link" href="productos.php?page=' . $i . $categoriaParam . '">' . $i . '</a></li>';
            }
          }
          echo '</ul>';
        }
        ?>
      </div>
    </div>
  </section>

  <!--Footer-->

  <?php include 'includes/footer.php'; ?>

  <!--Scripth Bootstrap 5-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

  <script src="js/filtro-categoria.js"></script>

</body>

</html>