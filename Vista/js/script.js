//-----------------1_02_registroAdministrador---------------
function registroAdmin(){
    let respuesta = confirm("¿Desea registrar el siguiente usuario administrador?")
    return respuesta
}


//-----------------1_02_registroAdministrador---------------
const boton = document.getElementById('acordeon1');
const submenu = document.getElementById('acordeon1opciones');
const botonInventario = document.getElementById('acordeon2'); // <- este es el que desaparecerá
const icono = document.getElementById('icono1');

boton.addEventListener('click', () => {
  const desplegado = submenu.style.display === 'flex';
  
  // Alternar visibilidad del submenú
  submenu.style.display = desplegado ? 'none' : 'flex';

  // Ocultar o mostrar el otro botón según estado del submenú
  botonInventario.style.display = desplegado ? 'flex' : 'none';

  //Con esto se despliega el nuevo modo de ver el boton con las opciones juntas
  if (desplegado) {
    boton.classList.remove('abierto');
  } else {
    boton.classList.add('abierto');
  }

  // Cambia la clase del ícono al estar abierto o cerrado el boton con las opciones de acordeon
  if (desplegado) {
    boton.classList.remove('abierto');
    icono.classList.remove('fa-chevron-up');
    icono.classList.add('fa-chevron-down');
  } else {
    boton.classList.add('abierto');
    icono.classList.remove('fa-chevron-down');
    icono.classList.add('fa-chevron-up');
  }
});



//-----------------3_01_01_registrarUsuarios----------------
function registroUsuario(){
    let respuesta = confirm("¿Desea registrar el siguiente usuario?")
    return respuesta
}


//-----------------3_01_06_edicionUsuario-------------------
function editarUsuario(){
    let respuesta = confirm("¿Desea aplicar los cambios a este usuario?")
    return respuesta
}


//-----------------3_01_07_eliminarUsuario-------------------
function eliminarUsuario(){
    alert ("Usuario eliminado")
}


//-----------------3_01_09_edicionAdmin-----------------------
function editarAdmin(){
    let respuesta = confirm("¿Desea aplicar los cambios a este usuario?")
    return respuesta
}


//-----------------3_02_03_asignarProducto----------------
function pestañaAsignarContador() {
    window.location.href = "3_02_04_asignarContador.html";
}


//-----------------3_02_04_asignarContador----------------
function pestañaEnviarToma(){
  window.location.href = "3_02_05_enviarToma.html";
}


//-----------------5_06_eliminarInforme-------------------
function eliminarInforme(){
    alert ("Informe eliminado")
}
