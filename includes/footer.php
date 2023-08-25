<?php
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();

// Obtener los datos de la tabla "categorias"
$query = "SELECT * FROM categorias";
$result = $con->query($query);

// Verificar si hay resultados y almacenarlos en un arreglo asociativo
$categorias = array();
if ($result && $result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $categorias[] = $row;
    }
}
?>

<footer class="bg-dark footer-makarlu">
  <div class="container py-4">
    <div class="row">
      <div class="col-12 text-center">
        <img src="img/logo/malena.png" alt="Footer Logo">
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 col-sm-12">
        <h2>Productos</h2>
        <ul class="list-unstyled categorias">
        <?php foreach ($categorias as $categoria): ?>
          <li><a href="productos.php?categoria=<?php echo htmlspecialchars($categoria['id']); ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></a></li>
        <?php endforeach; ?>
        </ul>
      </div>
      <div class="col-md-4 col-sm-12">
        <h2>Dirección</h2>
        <ul class="list-unstyled">
          <li><a href="#">Av Caracas # 23-58 Bogota D.C</a></li>
          <li><a href="#">+57 312 456 8592 & +57 315 254 3691</a></li>
          <li><a href="mailto:malena.home@gmail.com">malena.home@gmail.com</a></li>
        </ul>
      </div>
      <div class="col-md-4 col-sm-12 iconos">
        <h2>Síguenos</h2>
        <ul class="list-inline">
          <li class="list-inline-item">
            <a target="_blank" href="https://www.facebook.com/Muebles.adecorarte.10/"><i class="bi bi-facebook"></i></a>
          </li>
          <li class="list-inline-item">
            <a target="_blank" href="https://twitter.com/mueblestugo?lang=es"><i class="bi bi-twitter"></i></a>
          </li>
          <li class="list-inline-item">
            <a target="_blank" href="https://www.instagram.com/el_mueble/?hl=es"><i class="bi bi-instagram"></i></a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>
