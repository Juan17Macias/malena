<?php
echo '<div class="row justify-content-center">';
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
$sql = "SELECT id, nombre FROM categorias";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoriaID = $row["id"];
        $categoriaNombre = $row["nombre"];
        echo '<div class="col-lg-2 col-md-4 col-sm-6 d-flex justify-content-center align-items-center">';
        echo '<a href="productos.php?categoria=' . $categoriaID . '" style="text-decoration: none;">';
        echo '<div class="circle">';
        echo '<img src="img/categorias/' . $categoriaID . '.jpg" alt="' . $categoriaNombre . '" width="150" height="150">';
        echo '<div class="overlay">';
        echo '<p class="text text-categoria text-center">' . $categoriaNombre . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
    }

    // Agregar el último circle fijo con el enlace deseado
    echo '<div class="col-lg-2 col-md-4 col-sm-6 d-flex justify-content-center align-items-center">';
    echo '<a href="productos.php?categoria=ofertas" style="text-decoration: none;">'; // Reemplaza "enlace_deseado.php" con tu enlace real
    echo '<div class="circle">';
    echo '<img src="img/oferta/5.jpg" alt="ofertas" width="150" height="150">'; // Reemplaza "ruta_a_imagen.jpg" y "Nombre de la categoría" con los valores correctos
    echo '<div class="overlay">';
    echo '<p class="text text-categoria text-center">Ofertas</p>'; // Cambia el texto según lo que desees mostrar
    echo '</div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
} else {
    echo "No hay categorías disponibles.";
}

$conn->close();
echo '</div>';
?>
