<?php
// detalles.php
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Verde GT | Departamentos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header class="hero-header">
        <div class="barra-superior">
            <div class="logo-texto-izq"></div>
            
           <nav class="nav-transparente">
                <a href="index.php" class="activo">Mapa Nacional</a>
                <a href="detalles.php">Ranking de Municipios</a>
                <a href="#" id="btn-abrir-buscador">Detalle Departamental</a>
                <button id="btn-dark-mode" style="background:none; border:none; font-size:1.5rem; cursor:pointer; margin-left:15px;" title="Alternar Modo Oscuro">🌙</button>
            </nav>
        </div>

        <div class="hero-contenido">
            <h1 class="hero-titulo">MONITOR VERDE <span>GT</span></h1>
            <p class="hero-subtitulo">PLATAFORMA INTERACTIVA DE ANÁLISIS Y CONCIENTIZACIÓN AMBIENTAL<br>Monitoreo de Impacto Hídrico, Calidad del Aire y Desechos Sólidos</p>
        </div>
    </header>

    <div id="modal-buscador" class="modal-oculto">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="btn-cerrar-modal">&times;</span>
            <h2>Buscar Departamento</h2>
            <p>Escribe el nombre del departamento que deseas analizar.</p>
            
            <div class="caja-busqueda">
                <span class="icono-lupa">🔍</span>
                <input type="text" id="input-busqueda" placeholder="Ej: Escuintla, Petén, Guatemala...">
                <button id="btn-buscar-depto">Buscar</button>
            </div>
            
            <p id="error-busqueda" style="color: #ae2012; display: none; margin-top: 15px; font-size: 0.9rem; font-weight: bold;">
                No se encontró el departamento. Revisa la ortografía.
            </p>
        </div>
    </div>
    </header>

    <main class="contenedor-principal">
        <section class="tarjeta-datos">
            <h2>Selecciona un Departamento</h2>
            <p>Haz clic en el nombre de un departamento para ver su informe ambiental completo, problemáticas y las métricas de sus municipios.</p>

            <div class="grid-departamentos">
                <?php
                // Consultamos solo los nombres de los departamentos ordenados alfabéticamente
                $query = "SELECT nombre FROM departamentos ORDER BY nombre ASC";
                $result = pg_query($dbconn, $query);

                if ($result && pg_num_rows($result) > 0) {
                    while ($row = pg_fetch_assoc($result)) {
                        $depto = $row['nombre'];
                        // Creamos un botón que funciona como enlace directo a la página de perfil
                        echo "<a href='departamento.php?nombre=" . urlencode($depto) . "' class='btn-departamento'>";
                        echo "<span>{$depto}</span>";
                        echo "<span class='icono-flecha'>→</span>";
                        echo "</a>";
                    }
                } else {
                    echo "<p style='text-align:center;'>No hay departamentos registrados.</p>";
                }
                ?>
            </div>
        </section>
    </main>

  <footer class="pie-pagina">
        <p>&copy; 2026 Monitor Verde GT. Monitoreo estricto de Impacto Hídrico, Calidad del Aire y Desechos Sólidos.</p>
    </footer>
</body>

// ==========================================
            // LÓGICA DEL MODO OSCURO
            // ==========================================
            const btnDarkMode = document.getElementById('btn-dark-mode');
            const body = document.body;

            // Revisar si el usuario ya tenía el modo oscuro activado antes
            if (localStorage.getItem('modoOscuro') === 'activado') {
                body.classList.add('dark-mode');
                if(btnDarkMode) btnDarkMode.textContent = '☀️';
            }

            if(btnDarkMode) {
                btnDarkMode.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    
                    if (body.classList.contains('dark-mode')) {
                        localStorage.setItem('modoOscuro', 'activado');
                        btnDarkMode.textContent = '☀️'; // Cambia a sol
                    } else {
                        localStorage.setItem('modoOscuro', 'desactivado');
                        btnDarkMode.textContent = '🌙'; // Cambia a luna
                    }
                });
            }
</html>