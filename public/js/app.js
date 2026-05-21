const API = "http://127.0.0.1:8000";

/* ==========================
   SPRINTS
========================== */

async function obtenerSprints() {

    const response =
        await fetch(`${API}/sprints`);

    const data =
        await response.json();

    const contenedor =
        document.getElementById(
            "listaSprints"
        );

    contenedor.innerHTML = "";

    data.forEach(sprint => {

        contenedor.innerHTML += `
            <div class="sprint-card">

                <h3>
                    ${sprint.nombre}
                </h3>

                <p>
                    <strong>Inicio:</strong>
                    ${sprint.fecha_inicio}
                </p>

                <p>
                    <strong>Fin:</strong>
                    ${sprint.fecha_fin}
                </p>

                <p>
                    <strong>ID:</strong>
                    ${sprint.id}
                </p>

            </div>
        `;
    });
}

async function crearSprint() {

    const nombre =
        document.getElementById(
            "nombreSprint"
        ).value;

    const fecha_inicio =
        document.getElementById(
            "fechaInicio"
        ).value;

    const fecha_fin =
        document.getElementById(
            "fechaFin"
        ).value;

    await fetch(`${API}/sprints`, {

        method: "POST",

        headers: {
            "Content-Type":
                "application/json"
        },

        body: JSON.stringify({
            nombre,
            fecha_inicio,
            fecha_fin
        })
    });

    document.getElementById(
        "nombreSprint"
    ).value = "";

    document.getElementById(
        "fechaInicio"
    ).value = "";

    document.getElementById(
        "fechaFin"
    ).value = "";

    obtenerSprints();
}

/* ==========================
   HISTORIAS
========================== */

async function obtenerHistorias() {

    const response =
        await fetch(`${API}/historias`);

    const data =
        await response.json();

    const contenedor =
        document.getElementById(
            "listaHistorias"
        );

    contenedor.innerHTML = "";

    data.forEach(historia => {

        contenedor.innerHTML += `
            <div class="historia-card">

                <h3>
                    ${historia.titulo}
                </h3>

                <p>
                    <strong>Descripción:</strong>
                    ${historia.descripcion}
                </p>

                <p>
                    <strong>Responsable:</strong>
                    ${historia.responsable}
                </p>

                <p>
                    <strong>Estado:</strong>
                    ${historia.estado}
                </p>

                <p>
                    <strong>Puntos:</strong>
                    ${historia.puntos}
                </p>

                <p>
                    <strong>Sprint:</strong>
                    ${historia.sprint_id}
                </p>

                <button onclick="
                    editarHistoria(
                        ${historia.id}
                    )
                ">
                    Editar
                </button>

                <button onclick="
                    eliminarHistoria(
                        ${historia.id}
                    )
                ">
                    Eliminar
                </button>

            </div>
        `;
    });
}

async function crearHistoria() {

    const titulo =
        document.getElementById(
            "titulo"
        ).value;

    const descripcion =
        document.getElementById(
            "descripcion"
        ).value;

    const responsable =
        document.getElementById(
            "responsable"
        ).value;

    const puntos =
        parseInt(
            document.getElementById(
                "puntos"
            ).value
        );

    const estado =
        document.getElementById(
            "estado"
        ).value;

    const sprint_id =
        parseInt(
            document.getElementById(
                "sprintId"
            ).value
        );

    await fetch(`${API}/historias`, {

        method: "POST",

        headers: {
            "Content-Type":
                "application/json"
        },

        body: JSON.stringify({

            titulo,
            descripcion,
            responsable,
            estado,
            puntos,

            fecha_creacion:
                new Date()
                    .toISOString()
                    .split("T")[0],

            fecha_finalizacion:
                null,

            sprint_id
        })
    });

    // limpiar formulario
    document.getElementById(
        "titulo"
    ).value = "";

    document.getElementById(
        "descripcion"
    ).value = "";

    document.getElementById(
        "responsable"
    ).value = "";

    document.getElementById(
        "puntos"
    ).value = "";

    document.getElementById(
        "sprintId"
    ).value = "";

    obtenerHistorias();
}

async function editarHistoria(id) {

    const nuevoTitulo =
        prompt("Nuevo título");

    if (!nuevoTitulo) return;

    await fetch(
        `${API}/historias/${id}`,
        {
            method: "PUT",

            headers: {
                "Content-Type":
                    "application/json"
            },

            body: JSON.stringify({
                titulo:
                    nuevoTitulo
            })
        }
    );

    obtenerHistorias();
}

async function eliminarHistoria(id) {

    await fetch(
        `${API}/historias/${id}`,
        {
            method:
                "DELETE"
        }
    );

    obtenerHistorias();
}

/* ==========================
   INIT
========================== */

obtenerSprints();
obtenerHistorias();