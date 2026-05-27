<?php
// Decirle al navegador que vamos a devolver datos en formato JSON
header('Content-Type: application/json');
include 'conexion.php'; 

// Revisar si el mapa nos mandó el nombre del departamento
if (isset($_GET['departamento'])) {
    $nombre_depto = $_GET['departamento'];

    // Esta consulta mágica busca el departamento, encuentra sus municipios 
    // y saca el PROMEDIO de contaminación y la SUMA del consumo de agua.
    $query = "SELECT ROUND(AVG(m.porcentaje_contaminacion), 2) as contaminacion_promedio, " .
             "SUM(m.consumo_m3) as consumo_total " .
             "FROM metricas_ambientales m " .
             "JOIN municipios mun ON m.id_municipio = mun.id_municipio " .
             "JOIN departamentos d ON mun.id_departamento = d.id_departamento " .
             "WHERE d.nombre = $1";

    // Ejecutamos la consulta protegiéndola contra hackeos (SQL Injection)
    $result = pg_query_params($dbconn, $query, array($nombre_depto));
    
    if ($result) {
        $row = pg_fetch_assoc($result);
        
        // Si hay datos, los guardamos. Si no hay (porque no los hemos simulado aún), ponemos 0.
        $contaminacion = $row['contaminacion_promedio'] ? $row['contaminacion_promedio'] : 0;
        $consumo = $row['consumo_total'] ? $row['consumo_total'] : 0;

        // Devolvemos la respuesta al mapa
        echo json_encode([
            "exito" => true,
            "contaminacion" => $contaminacion,
            "consumo" => number_format($consumo, 2)
        ]);
    } else {
        echo json_encode(["exito" => false, "error" => "No se pudo consultar PostgreSQL"]);
    }
} else {
    echo json_encode(["exito" => false, "error" => "No se recibió ningún departamento"]);
}
?>