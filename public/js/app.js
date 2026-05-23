const apiUrl = 'http://localhost:8000';

document.addEventListener('DOMContentLoaded', () => {
    cargarSprints();
    cargarHistorias();

    document.getElementById('form-sprint').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            nombre: document.getElementById('sprint-nombre').value,
            fecha_inicio: document.getElementById('sprint-inicio').value,
            fecha_fin: document.getElementById('sprint-fin').value
        };
        await fetch(`${apiUrl}/sprints`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        e.target.reset();
        cargarSprints();
    });

    document.getElementById('form-historia').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            titulo: document.getElementById('historia-titulo').value,
            descripcion: document.getElementById('historia-descripcion').value,
            responsable: document.getElementById('historia-responsable').value,
            puntos: parseInt(document.getElementById('historia-puntos').value),
            estado: 'nueva',
            fecha_creacion: document.getElementById('historia-creacion').value,
            sprint_id: parseInt(document.getElementById('historia-sprint').value)
        };
        await fetch(`${apiUrl}/historias`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        e.target.reset();
        cargarHistorias();
    });

    document.getElementById('btn-informe').addEventListener('click', async () => {
        const res = await fetch(`${apiUrl}/informe`);
        const data = await res.json();
        const container = document.getElementById('informe-container');
        
        let html = `
            <div class="informe">
                <h3>Informe General</h3>
                <p><strong>Finalizadas:</strong> ${data.general.finalizadas}</p>
                <p><strong>Pendientes:</strong> ${data.general.pendientes}</p>
                <p><strong>Con impedimentos:</strong> ${data.general.impedimentos}</p>
            </div>
            <div class="informe">
                <h3>Informe por Responsable</h3>
        `;

        for (const [responsable, stats] of Object.entries(data.por_responsable)) {
            html += `
                <div class="responsable-stats">
                    <h4>${responsable}</h4>
                    <p>Finalizadas: ${stats.finalizadas} | Pendientes: ${stats.pendientes} | Impedimentos: ${stats.impedimentos}</p>
                </div>
            `;
        }
        
        html += `</div>`;
        container.innerHTML = html;
    });
});

async function cargarSprints() {
    const res = await fetch(`${apiUrl}/sprints`);
    const sprints = await res.json();
    const select = document.getElementById('historia-sprint');
    select.innerHTML = '<option value="" disabled selected>Selecciona un Sprint</option>';
    sprints.forEach(s => {
        select.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
    });
}

async function cargarHistorias() {
    const res = await fetch(`${apiUrl}/historias`);
    const historias = await res.json();
    const container = document.getElementById('historias-container');
    container.innerHTML = '<h2>Historias por Sprint</h2>';
    
    const agrupadas = {};
    historias.forEach(h => {
        const sprintName = h.sprint ? h.sprint.nombre : 'Sin Sprint Asignado';
        if (!agrupadas[sprintName]) agrupadas[sprintName] = [];
        agrupadas[sprintName].push(h);
    });

    for (const [sprint, lista] of Object.entries(agrupadas)) {
        let html = `<div class="sprint-group"><h3>📌 ${sprint}</h3>`;
        lista.forEach(h => {
            html += `
                <div class="tarjeta-historia">
                    <h4>${h.titulo} <span class="badge">${h.puntos} pts</span></h4>
                    <p>${h.descripcion}</p>
                    <p><strong>Responsable:</strong> ${h.responsable}</p>
                    <div class="acciones-historia">
                        <select onchange="cambiarEstado(${h.id}, this.value)">
                            <option value="nueva" ${h.estado === 'nueva' ? 'selected' : ''}>Nueva</option>
                            <option value="activa" ${h.estado === 'activa' ? 'selected' : ''}>Activa</option>
                            <option value="finalizada" ${h.estado === 'finalizada' ? 'selected' : ''}>Finalizada</option>
                            <option value="impedimento" ${h.estado === 'impedimento' ? 'selected' : ''}>Impedimento</option>
                        </select>
                        <button class="btn-editar" onclick="editarHistoria(${h.id}, '${h.titulo}', '${h.descripcion}', ${h.puntos})">Editar</button>
                        <button class="btn-eliminar" onclick="eliminarHistoria(${h.id})">Eliminar</button>
                    </div>
                </div>
            `;
        });
        html += `</div>`;
        container.innerHTML += html;
    }
}

async function cambiarEstado(id, nuevoEstado) {
    const data = { estado: nuevoEstado };
    if (nuevoEstado === 'finalizada') {
        data.fecha_finalizacion = new Date().toISOString().split('T')[0];
    }
    
    await fetch(`${apiUrl}/historias/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    cargarHistorias();
}

async function editarHistoria(id, tituloActual, descActual, puntosActuales) {
    const nuevoTitulo = prompt("Editar Título:", tituloActual);
    if (!nuevoTitulo) return;
    const nuevaDesc = prompt("Editar Descripción:", descActual);
    const nuevosPuntos = prompt("Editar Puntos:", puntosActuales);

    await fetch(`${apiUrl}/historias/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            titulo: nuevoTitulo,
            descripcion: nuevaDesc,
            puntos: parseInt(nuevosPuntos)
        })
    });
    cargarHistorias();
}

async function eliminarHistoria(id) {
    if(confirm("¿Seguro que deseas eliminar esta historia?")) {
        await fetch(`${apiUrl}/historias/${id}`, {
            method: 'DELETE'
        });
        cargarHistorias();
    }
}