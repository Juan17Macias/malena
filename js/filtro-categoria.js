    // seleccionar todos los botones de categoría
    const categoryButtons = document.querySelectorAll('#categories [data-filter]');

    // agregar un evento de clic a cada botón de categoría
    categoryButtons.forEach(button => {
      button.addEventListener('click', () => {
        // obtener el valor del atributo "data-filter" del botón de categoría seleccionado
        const category = button.getAttribute('data-filter');

        // seleccionar todos los elementos de producto
        const products = document.querySelectorAll('#products .product');

        // mostrar los elementos de producto correspondientes a la categoría seleccionada y ocultar los demás
        products.forEach(product => {
          if (category === 'all' || product.getAttribute('data-category') === category) {
            product.style.display = 'block';
          } else {
            product.style.display = 'none';
          }
        });
      });
    });