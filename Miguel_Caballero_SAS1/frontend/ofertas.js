function mostrarDetalles(puesto, empresa, descripcion, beneficios, ubicacion) {
    document.getElementById("detallePuesto").innerText = puesto;
    document.getElementById("detalleEmpresa").innerText = empresa;
    document.getElementById("detalleDescripcion").innerText = descripcion;
    document.getElementById("detalleBeneficios").innerText = beneficios;
    document.getElementById("detalleUbicacion").innerText = ubicacion;
    const modal = new bootstrap.Modal(document.getElementById("modalDetalles"));
    modal.show();
}

function mostrarFormulario() {
    document.getElementById("formularioPostulacion").classList.remove("d-none");
    window.scrollTo(0, document.getElementById("formularioPostulacion").offsetTop);
}
