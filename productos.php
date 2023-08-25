<?php
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();

if (!$con) {
  $errorInfo = $con->errorInfo();
  die("Error de conexión: " . $errorInfo[2]);
}

// Function to sanitize and validate input parameters
function sanitizeInput($input)
{
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to get all categories from the database
function getAllCategories($con)
{
  $sql = $con->prepare("SELECT id, nombre FROM categorias");
  $sql->execute();
  return $sql->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get products based on the provided category and page number
function getProductsByCategoryAndPage($con, $categoria, $paginaActual, $productosPorPagina)
{
  $fechaActual = date('Y-m-d');

  // Query to get products with or without offers based on the category
  $sql = "SELECT p.id, p.nombre, p.precio, p.descripcion, p.imagen,p.Alto, p.categoria_id
          FROM productos p
          LEFT JOIN ofertas o ON p.id = o.producto_id
          WHERE (o.fecha_fin >= :fechaActual OR o.fecha_fin IS NULL)";

  // Check if a specific category is selected
  if ($categoria != "todos") {
    // If the category is "ofertas", show only products with offers
    if ($categoria == "ofertas") {
      $sql = "SELECT p.id, p.nombre, p.precio, p.descripcion, p.imagen,p.Alto, p.categoria_id
              FROM productos p
              INNER JOIN ofertas o ON p.id = o.producto_id
              WHERE o.fecha_fin >= :fechaActual";
    } else {
      $sql .= " AND p.categoria_id = :categoria";
    }
  }

  // Calculate the total number of products for the selected category or offers
  $sqlTotal = "SELECT COUNT(*) as total FROM productos p";
  if ($categoria != "todos") {
    if ($categoria == "ofertas") {
      $sqlTotal .= " INNER JOIN ofertas o ON p.id = o.producto_id WHERE o.fecha_fin >= :fechaActual";
    } else {
      $sqlTotal .= " WHERE p.categoria_id = :categoria";
    }
  }

  $stmtTotal = $con->prepare($sqlTotal);
  if ($categoria == "ofertas") {
    $stmtTotal->bindParam(':fechaActual', $fechaActual, PDO::PARAM_STR);
  } elseif ($categoria != "todos") {
    $stmtTotal->bindParam(':categoria', $categoria, PDO::PARAM_INT);
  }
  $stmtTotal->execute();
  $totalProductos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

  // Calculate the offset for pagination
  $offset = ($paginaActual - 1) * $productosPorPagina;
  $sql .= " LIMIT :offset, :productosPorPagina";

  $stmt = $con->prepare($sql);
  $stmt->bindParam(':fechaActual', $fechaActual, PDO::PARAM_STR);
  if ($categoria != "todos" && $categoria != "ofertas") { // Add this condition to exclude "ofertas" from binding
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
  }
  $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
  $stmt->bindParam(':productosPorPagina', $productosPorPagina, PDO::PARAM_INT);
  $stmt->execute();
  return array(
    'totalProductos' => $totalProductos,
    'productos' => $stmt->fetchAll(PDO::FETCH_ASSOC),
  );
}


// Get the category from the URL parameter, sanitize it, and assign it to a variable
$categoria = "todos"; // Default category is "todos"
if (isset($_GET['categoria'])) {
  $categoria = sanitizeInput($_GET['categoria']);
}

// Get the current page from the URL parameter, sanitize it, and assign it to a variable
$paginaActual = 1; // Default page is 1
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
  $paginaActual = intval($_GET['page']);
}

// Number of products to display per page
$productosPorPagina = 12;

// Get all categories
$categorias = getAllCategories($con);

// Get products based on category and page number
$productosData = getProductsByCategoryAndPage($con, $categoria, $paginaActual, $productosPorPagina);
$productos = $productosData['productos'];
$totalProductos = $productosData['totalProductos'];
$totalPaginas = ceil($totalProductos / $productosPorPagina);
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'includes/head.php'; ?>

<body>
  <?php include 'includes/navbar.php'; ?>

  <section class="catalogo">
    <h2 class="container t-productos">Todos Los Productos</h2>
    <div class="filtro-categorias container">
      <div class="row">
        <div class="col">
          <a class="categorias-p <?php echo $categoria === 'todos' ? 'active' : ''; ?>" href="productos.php">
            <img src="svg/todos.svg" alt="Todos" class="svg-icon d-sm-none img-fluid">
            <span class="d-none d-sm-inline">Todos</span>
          </a>
        </div>
        <?php foreach ($categorias as $categoriaData) : ?>
          <div class="col">
            <a class="categorias-p <?php echo $categoria == $categoriaData['id'] ? 'active' : ''; ?>" href="productos.php?categoria=<?php echo $categoriaData['id']; ?>">
              <img src="svg/<?php echo $categoriaData['nombre']; ?>.svg" alt="<?php echo $categoriaData['nombre']; ?>" class="svg-icon d-sm-none img-fluid">
              <span class="d-none d-sm-inline"><?php echo $categoriaData['nombre']; ?></span>
            </a>
          </div>
        <?php endforeach; ?>
        <div class="col">
          <a class="categorias-p <?php echo $categoria === 'ofertas' ? 'active' : ''; ?>" href="productos.php?categoria=ofertas">
            <img src="svg/ofertas.svg" alt="Ofertas" class="svg-icon d-sm-none img-fluid">
            <span class="d-none d-sm-inline">Ofertas</span>
          </a>
        </div>
      </div>
    </div>

    <!-- SECCION PRODUCTOS DESTACADOS -->

    <div class="productos container">
      <div class="row">
        <?php if (!empty($productos)) : ?>
          <?php foreach ($productos as $producto) : ?>
            <?php
            // Get the category ID and name of the product
            $categoria_id = $producto['categoria_id'];
            $nombre_categoria = "";

            // Get the name of the category based on the category ID
            foreach ($categorias as $categoriaData) {
              if ($categoriaData['id'] == $categoria_id) {
                $nombre_categoria = $categoriaData['nombre'];
                break;
              }
            }

            // Define $ruta_producto based on the category of the product
            $ruta_producto = "img/productos/" . $categoria_id . "/" . $producto['nombre'] . "/";
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-5">
              <div class="card">
                <div class="card-image">
                  <?php
                  $imagen_base64 = "";
                  $imagen_data = $producto['imagen'];
                  if (empty($imagen_data)) {
                    $imagen_data = file_get_contents("img/no_product.webp");
                  }
                  $imagen_base64 = base64_encode($imagen_data);
                  $imagen_src = "data:image/png;base64," . $imagen_base64;
                  ?>
                  <img src="<?php echo $imagen_src; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>">
                  <div class="card-overlay">

                    <div class="row mt-3">
                      <div class="col boton-card">
                        <button class="btn btn-outline-dark btn-card" data-bs-toggle="modal" data-bs-target="#mimodal-<?php echo $producto['id']; ?>">Detalles<i class="bi bi-check2-circle"></i></button>
                      </div>
                      <div class="col boton-card">
                        <a href="https://api.whatsapp.com/send?phone=3187149192" target="_blank">
                          <button class="btn btn-outline-success btn-card">Asesor <i class="bi bi-whatsapp"></i></button>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <h3 class="card-title"><?php echo $producto['nombre']; ?></h3>
                </div>
                <div class="col">
                  <p class="card-text">COP: $<?php echo number_format($producto['precio'], 2, '.', ','); ?></p>
                </div>
              </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="mimodal-<?php echo $producto['id'] ?>" tabindex="-1" aria-labelledby="mimodalLabel-<?php echo $producto['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="mimodalLabel-<?php echo $producto['id'] ?>"><?php echo $producto['nombre'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12 col-lg-6 lado-izquierdo">
                        <div id="carousel-<?php echo $producto['id']; ?>" class="carousel slide" data-bs-ride="carousel">
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
                          <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $producto['id']; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                          </button>
                          <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $producto['id']; ?>" data-bs-slide="next">
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
                        <p class="descripcion"><?php echo $producto['descripcion'] ?></p>
                        <div class="row mt-3">
                          <div class="col">
                            <p class="card-textv precio">Precio: $<?php echo number_format($producto['precio'], 2, '.', ','); ?></p>
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col">
                            <h3 class="card-subtitle mb-2">Especificaciones:</h3>
                            <table class="table">
                              <tbody>
                                <tr>
                                  <td>Altura:</td>
                                  <td><?php echo $producto['Alto'] ?></td>
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
                            <a href="https://api.whatsapp.com/send?phone=3187149192&text=<?php echo urlencode('¡Hola! Estoy interesado en el producto: ' . $producto['nombre']); ?>" target="_blank">
                              <button class="btn btn-outline-success">
                                Asesor <i class="bi bi-whatsapp"></i>
                            </a>
                          </div>
                        </div>
                        <div class="row mt-6 nota">

                          <p>Queridos clientes,

                            Para brindarles una experiencia única y personalizada, no ofrecemos opciones de pago directo en nuestro sitio web. Preferimos interactuar directamente a través de WhatsApp para guiarlos en la personalización de sus productos. Esto nos permite asegurar que cada detalle se ajuste a sus preferencias y garantizar un servicio excepcional.

                           <a href="nota.php">Conoce mas</a></p>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
        <?php else : ?>
          <p>No hay productos disponibles.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- PAGINACION DE PRODUCTOS -->
    <div class="row">
      <div class="col">
        <?php
        $totalPaginas = ceil($totalProductos / $productosPorPagina);

        if ($totalProductos > 0) {
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
        } else {
          echo "</ul>";
        }
        ?>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <!--Scripth Bootstrap 5-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script src="js/filtro-categoria.js"></script>

</body>

</html>