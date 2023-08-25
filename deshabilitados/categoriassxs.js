 // Filtrar productos por categoría
 $("#categories a").click(function(){
  var category = $(this).data("filter");
  if (category == "all") {
    $("#products .product").show();
  } else {
    $("#products .product").hide();
    $("#products ." + category).show();
  }
});