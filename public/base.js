function validarModal() {
 let min_price = document.getElementById("group_modal_min").value;
 let max_price = document.getElementById("group_modal_max").value;

 if (!/^([0-9999])*$/.test(min_price)) {
     alert("el numero de minimo precio no es un numero");
     return false;
 } else if (!/^([0-9999])*$/.test(max_price)) {
     alert("el numero de maximo precio no es un numero");
     return false;

 }

}