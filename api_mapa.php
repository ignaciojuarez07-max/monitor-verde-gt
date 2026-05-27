<?php
// api_mapa.php
include 'conexion.php';
header('Content-Type: application/json');

if (isset($_GET['departamento'])) {
    $departamento = $_GET['departamento'];
    
    // Agregamos el AVG (promedio) para el aire y el SUM (suma) para la basura
    $query = "SELECT 
                AVG(m.porcentaje_contaminacion) as contaminacion, 
                SUM(m.consumo_m3) as consumo,
                AVG(m.calidad_aire_aqi) as aire,
                SUM(m.toneladas_basura) as basura
              FROM metricas_ambientales m
              JOIN municipios mun ON m.id_municipio = mun.id_municipio
              JOIN departamentos d ON mun.id_departamento = d.id_departamento
              WHERE d.nombre = $1";
    
    $result = pg_query_params($dbconn, $query, array($departamento));
    
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        
        // Si hay datos, los enviamos empaquetados en JSON
        if ($row['contaminacion'] !== null) {
            echo json_encode([
                'exito' => true,
                'contaminacion' => round($row['contaminacion'], 2),
                'consumo' => number_format($row['consumo'], 2),
                'aire' => round($row['aire'], 2),
                'basura' => number_format($row['basura'], 2)
            ]);
            exit;
        }
    }
    
    echo json_encode(['exito' => false, 'error' => 'Datos no disponibles para este departamento.']);
} else {
    echo json_encode(['exito' => false, 'error' => 'Falta el nombre del departamento.']);
}
?>