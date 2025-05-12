// Vista previa de la foto de perfil
function previewFoto() {
    const input = document.getElementById('foto');
    const fotoPerfil = document.getElementById('fotoPerfil');

    const reader = new FileReader();
    reader.onload = function (e) {
        fotoPerfil.src = e.target.result;
    };

    if (input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

// Agregar más campos de experiencia laboral
function agregarExperiencia() {
    const experienciaDiv = document.getElementById('experiencia');
    const nuevaExperiencia = document.createElement('div');
    nuevaExperiencia.className = 'd-flex mb-2';
    nuevaExperiencia.innerHTML = `
        <input type="text" class="form-control me-2" placeholder="Puesto" required>
        <input type="text" class="form-control me-2" placeholder="Empresa" required>
        <input type="text" class="form-control me-2" placeholder="Duración (e.g., 1 año)">
    `;
    experienciaDiv.appendChild(nuevaExperiencia);
}
