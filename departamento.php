<?php
// departamento.php
include 'conexion.php';

// Verificamos si recibimos el nombre del departamento por la URL
if (!isset($_GET['nombre'])) {
    header("Location: index.php");
    exit();
}

$nombre_depto = $_GET['nombre'];

// --- CONSULTA PARA OBTENER EL RESUMEN DESDE LA BASE DE DATOS ---
$query_resumen = "SELECT resumen_ambiental FROM departamentos WHERE nombre = $1";
$result_resumen = pg_query_params($dbconn, $query_resumen, array($nombre_depto));

$texto_resumen = "Información ambiental detallada en proceso de investigación para este departamento.";

if ($result_resumen && pg_num_rows($result_resumen) > 0) {
    $row_resumen = pg_fetch_assoc($result_resumen);
    if (!empty($row_resumen['resumen_ambiental'])) {
        $texto_resumen = $row_resumen['resumen_ambiental'];
    }
}

// --- LÓGICA DE IMÁGENES DE FONDO DE CADA DEPARTAMENTO (Soporta PNG, JPG, JPEG) ---
$nombre_base = strtolower(str_replace(['á','é','í','ó','ú',' '], ['a','e','i','o','u','_'], $nombre_depto));
$ruta_imagen = "";

if (file_exists("img/" . $nombre_base . ".png")) {
    $ruta_imagen = "img/" . $nombre_base . ".png";
} else if (file_exists("img/" . $nombre_base . ".jpg")) {
    $ruta_imagen = "img/" . $nombre_base . ".jpg";
} else if (file_exists("img/" . $nombre_base . ".jpeg")) {
    $ruta_imagen = "img/" . $nombre_base . ".jpeg";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Verde GT | Perfil de <?php echo htmlspecialchars($nombre_depto); ?></title>
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

    <main class="contenedor-principal perfil-departamento">
        
        <section class="tarjeta-datos info-general">
            <h2>Contexto Ambiental</h2>
            
            <div class="contenedor-foto">
                <?php if ($ruta_imagen != ""): ?>
                    <img src="<?php echo $ruta_imagen; ?>" alt="Impacto ambiental en <?php echo htmlspecialchars($nombre_depto); ?>">
                <?php else: ?>
                    <div class="foto-placeholder">
                        <span>[Foto de <?php echo htmlspecialchars($nombre_depto); ?> en investigación]</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="texto-analisis">
                <p><?php echo htmlspecialchars($texto_resumen); ?></p>
            </div>

            <div class="tarjeta-consejo">
                <h3>💡 Reduce tu Huella Ecológica</h3>
                <p>
                    <?php 
                    if (in_array($nombre_depto, ['Escuintla', 'Guatemala', 'Quetzaltenango'])) {
                        echo "<strong>Alerta de Desechos:</strong> Este departamento genera altos volúmenes de basura. Empieza separando tus residuos orgánicos para compostaje y evita plásticos de un solo uso en tus compras diarias.";
                    } else if (in_array($nombre_depto, ['Izabal', 'Sacatepéquez', 'Sololá'])) {
                        echo "<strong>Protección Hídrica:</strong> La contaminación de ríos y lagos es crítica aquí. Utiliza detergentes biodegradables y asegúrate de no verter aceites de cocina por el desagüe.";
                    } else if ($nombre_depto == 'Petén' || $nombre_depto == 'Alta Verapaz') {
                        echo "<strong>Conservación Forestal:</strong> La deforestación afecta la recarga de agua. Apoya a productores locales que utilicen prácticas agrícolas sostenibles y reduce tu consumo de papel.";
                    } else {
                        echo "<strong>Acción Ciudadana:</strong> Revisa constantemente las llaves de agua en tu hogar para evitar fugas y opta por el transporte colectivo o bicicleta para mejorar la calidad del aire (AQI) en tu municipio.";
                    }
                    ?>
                </p>
            </div>
        </section>

        <section class="tarjeta-datos datos-municipios">
            <h2>Métricas Ambientales por Municipio</h2>
            <div class="contenedor-tabla">
                <table class="tabla-ranking">
                    <thead>
                        <tr>
                            <th>Municipio</th>
                            <th>Cont. Agua</th>
                            <th>Consumo (m³)</th>
                            <th>Aire (AQI)</th>
                            <th>Basura (Ton)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_municipios = "SELECT mun.nombre as municipio, 
                                                    m.porcentaje_contaminacion, 
                                                    m.consumo_m3,
                                                    m.calidad_aire_aqi,
                                                    m.toneladas_basura
                                             FROM metricas_ambientales m
                                             JOIN municipios mun ON m.id_municipio = mun.id_municipio
                                             JOIN departamentos d ON mun.id_departamento = d.id_departamento
                                             WHERE d.nombre = $1
                                             ORDER BY m.porcentaje_contaminacion DESC";
                        
                        $result_municipios = pg_query_params($dbconn, $query_municipios, array($nombre_depto));

                        // Arreglos PHP para almacenar los datos que usará la gráfica
                        $nombres_municipios = [];
                        $datos_agua = [];
                        $datos_aire = [];

                        if ($result_municipios && pg_num_rows($result_municipios) > 0) {
                            while ($row = pg_fetch_assoc($result_municipios)) {
                                // Guardamos las variables para Chart.js
                                $nombres_municipios[] = $row['municipio'];
                                $datos_agua[] = $row['porcentaje_contaminacion'];
                                $datos_aire[] = $row['calidad_aire_aqi'];

                                $claseAgua = ($row['porcentaje_contaminacion'] >= 60) ? 'nivel-critico' : '';
                                $claseAire = ($row['calidad_aire_aqi'] >= 100) ? 'nivel-critico' : '';
                                
                                echo "<tr>";
                                echo "<td><strong>{$row['municipio']}</strong></td>";
                                echo "<td class='{$claseAgua}'>{$row['porcentaje_contaminacion']}%</td>";
                                echo "<td>" . number_format($row['consumo_m3'], 2) . "</td>";
                                echo "<td class='{$claseAire}'>{$row['calidad_aire_aqi']}</td>";
                                echo "<td>" . number_format($row['toneladas_basura'], 2) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Sin datos registrados para este departamento.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 2px dashed #ccc;">
                <h3 style="text-align: center; margin-bottom: 20px; color: #1b4332;">Comparativa Visual de Impacto</h3>
                <canvas id="graficoAmbiental" height="110"></canvas>
            </div>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Monitor Verde GT. Monitoreo estricto de Impacto Hídrico, Calidad del Aire y Desechos Sólidos.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
         // ==========================================
            // 1. LÓGICA DE MODO OSCURO 100% AUTOMÁTICO
            // ==========================================
            const body = document.body;
            
            // Le preguntamos al sistema operativo su preferencia de color
            const preferenciaSistema = window.matchMedia('(prefers-color-scheme: dark)');

            // Función que activa o desactiva la clase según el sistema
            function aplicarTemaAutomatico(evento) {
                if (evento.matches) {
                    body.classList.add('dark-mode');
                } else {
                    body.classList.remove('dark-mode');
                }
            }

            // 1. Ejecutar inmediatamente al cargar la página
            aplicarTemaAutomatico(preferenciaSistema);

            // 2. Quedarse escuchando por si el usuario cambia el tema de su celular/PC mientras navega
            preferenciaSistema.addEventListener('change', aplicarTemaAutomatico);

            // 2. RENDEREADO DE LA GRÁFICA (CHART.JS)
            const etiquetas = <?php echo json_encode($nombres_municipios ?? []); ?>;
            const dataAgua = <?php echo json_encode($datos_agua ?? []); ?>;
            const dataAire = <?php echo json_encode($datos_aire ?? []); ?>;
            const ctx = document.getElementById('graficoAmbiental');
            
            if (ctx && etiquetas.length > 0) {
                new Chart(ctx, {
                    type: 'bar', 
                    data: {
                        labels: etiquetas,
                        datasets: [
                            {
                                label: 'Contaminación Hídrica (%)',
                                data: dataAgua,
                                backgroundColor: '#2d6a4f',
                                borderRadius: 4
                            },
                            {
                                label: 'Calidad de Aire (AQI)',
                                data: dataAire,
                                backgroundColor: '#52b788',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { 
                                position: 'top',
                                labels: { font: { family: 'Poppins' } }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // 3. LÓGICA INTERACTIVA DEL MODAL BUSCADOR
            const btnAbrirModal = document.getElementById('btn-abrir-buscador');
            const modalBuscador = document.getElementById('modal-buscador');
            const btnCerrarModal = document.getElementById('btn-cerrar-modal');
            const inputBusqueda = document.getElementById('input-busqueda');
            const btnBuscar = document.getElementById('btn-buscar-depto');
            const errorBusqueda = document.getElementById('error-busqueda');

            const diccionarioDepartamentos = {
                "alta verapaz": "Alta Verapaz", "baja verapaz": "Baja Verapaz",
                "chimaltenango": "Chimaltenango", "chiquimula": "Chiquimula",
                "el progreso": "El Progreso", "escuintla": "Escuintla",
                "guatemala": "Guatemala", "huehuetenango": "Huehuetenango",
                "izabal": "Izabal", "jalapa": "Jalapa", "jutiapa": "Jutiapa",
                "peten": "Petén", "quetzaltenango": "Quetzaltenango",
                "quiche": "Quiché", "retalhuleu": "Retalhuleu",
                "sacatepequez": "Sacatepéquez", "san marcos": "San Marcos",
                "santa rosa": "Santa Rosa", "solola": "Sololá",
                "suchitepequez": "Suchitepéquez", "totonicapan": "Totonicapán",
                "zacapa": "Zacapa"
            };

            if (btnAbrirModal) {
                btnAbrirModal.addEventListener('click', (e) => {
                    e.preventDefault();
                    modalBuscador.style.display = 'block';
                    inputBusqueda.value = "";
                    errorBusqueda.style.display = 'none';
                    setTimeout(() => inputBusqueda.focus(), 100);
                });
            }

            if (btnCerrarModal) {
                btnCerrarModal.addEventListener('click', () => modalBuscador.style.display = 'none');
            }
            
            window.addEventListener('click', (e) => {
                if (e.target === modalBuscador) modalBuscador.style.display = 'none';
            });

            function ejecutarBusqueda() {
                let textoLimpio = inputBusqueda.value.trim().normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                if (textoLimpio === "") return;

                if (diccionarioDepartamentos[textoLimpio]) {
                    let nombreOficial = diccionarioDepartamentos[textoLimpio];
                    window.location.href = `departamento.php?nombre=${encodeURIComponent(nombreOficial)}`;
                } else {
                    errorBusqueda.style.display = 'block';
                }
            }

            if (btnBuscar) btnBuscar.addEventListener('click', ejecutarBusqueda);
            if (inputBusqueda) {
                inputBusqueda.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') ejecutarBusqueda();
                });
            }
        });
    </script>
</body>
</html>