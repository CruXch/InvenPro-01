console.log("✅ JS cargado");
//-----------------1_02_registroAdministrador---------------
function registroAdmin() {
  const password1 = document.getElementById('contra-admin').value;
  const password2 = document.getElementById('confirmar-contra-admin').value;

  // Validación de estructura segura
  if (
    password1.length < 6 ||
    !/[A-Z]/.test(password1) ||        // al menos una mayúscula
    !/\d/.test(password1) ||          // al menos un número
    !/[!@#$%^&*(),.?":{}|<>]/.test(password1) // al menos un símbolo
  ) {
    alert("La contraseña debe tener al menos 6 caracteres, una mayúscula, un número y un símbolo.");
    return false;
  }

  // Validación de coincidencia
  if (password1 !== password2) {
    alert("Las contraseñas no coinciden.");
    return false;
  }

  // Confirmación final
  let respuesta = confirm("¿Desea registrar el siguiente usuario administrador?");
  return respuesta;
}


//-----------------1_02_registroAdministrador---------------
const boton = document.getElementById('acordeon1');
const submenu = document.getElementById('acordeon1opciones');
const botonInventario = document.getElementById('acordeon2');
const icono = document.getElementById('icono1');

// Solo ejecutar si todos los elementos existen en la página actual
if (boton && submenu && botonInventario && icono) {
  boton.addEventListener('click', () => {
    const desplegado = submenu.style.display === 'flex';

    // Alternar visibilidad del submenú
    submenu.style.display = desplegado ? 'none' : 'flex';

    // Ocultar o mostrar el otro botón según estado del submenú
    botonInventario.style.display = desplegado ? 'flex' : 'none';

    // Alternar clases del botón principal
    if (desplegado) {
      boton.classList.remove('abierto');
    } else {
      boton.classList.add('abierto');
    }

    // Cambiar ícono de flecha
    if (desplegado) {
      icono.classList.remove('fa-chevron-up');
      icono.classList.add('fa-chevron-down');
    } else {
      icono.classList.remove('fa-chevron-down');
      icono.classList.add('fa-chevron-up');
    }
  });
}





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


//-----------------3_02_02_subirCSV----------------
function pestañaListaPlanificacion(){
  window.location.href = "3_02_01_listaPlanificacion.php";
}


//-----------------3_02_02_subirCSV----------------
function pestañaAsignarContador() {
    window.location.href = "3_02_04_asignarContador.php";
}


//-----------------3_02_04_asignarContador----------------
function pestañaEnviarToma(){
  window.location.href = "3_02_05_enviarToma.php";
}


//-----------------5_06_eliminarInforme-------------------
function eliminarInforme(){
    alert ("Informe eliminado")
}

// Evita acceso con botón "atrás" después de cerrar sesión en todos los archivos
window.addEventListener('pageshow', function (event) {
  if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
    window.location.reload();
  }
});

