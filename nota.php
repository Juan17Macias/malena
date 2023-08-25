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

    <div class="container mt-5 mb-5">
    <div class="row justify-content-center ">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">¡Importante! Proceso de Personalización y Pago</h5>
            <p class="card-text">
              Queridos clientes,<br><br>
              Para brindarles una experiencia única y personalizada, no ofrecemos opciones de pago directo en nuestro sitio web. Preferimos interactuar directamente a través de WhatsApp para guiarlos en la personalización de sus productos. Esto nos permite asegurar que cada detalle se ajuste a sus preferencias y garantizar un servicio excepcional.<br><br>
              Pasos simples:<br>
              1. Explore nuestro catálogo en línea.<br>
              2. Contáctenos por WhatsApp para personalizar su producto.<br>
              3. Reciba asesoramiento experto y detalles.<br>
              4. Complete su compra de manera segura según nuestras instrucciones.<br><br>
              Apreciamos su comprensión y esperamos ayudarles a crear artículos perfectos para su estilo.<br><br>
              Atentamente,<br>
              El Equipo de Malena Home 
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>


    <?php include 'includes/footer.php'; ?>

    <!--Scripth Bootstrap 5-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="js/filtro-categoria.js"></script>

</body>

</html>