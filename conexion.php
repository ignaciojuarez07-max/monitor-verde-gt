<?php
// conexion.php

// 1. CREDENCIALES DE SUPABASE (Corregidas para conexión directa a Postgres)
$host     = "db.dkvvrpepffixgqrihyrg.supabase.co"; // Tu host real de base de datos
$port     = "6543";                                // Puerto estándar de PostgreSQL
$dbname   = "postgres";                            // Siempre es postgres en Supabase
$user     = "postgres";                            // El usuario del motor siempre es postgres
$password = "Monitor?Verde123";                    // La contraseña que creaste al inicio del proyecto

// 2. CADENA DE CONEXIÓN (Obligatorio incluir sslmode=require para bases de datos en la nube)
$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} sslmode=require";

// 3. EJECUTAR LA CONEXIÓN
$dbconn = pg_connect($connection_string);

// 4. VERIFICACIÓN DE SEGURIDAD
if (!$dbconn) {
    echo "<div style='padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>";
    echo "<h3 style='margin-top:0;'>⚠️ Error de Conexión</h3>";
    echo "No se pudo establecer la comunicación con el servidor de Supabase.<br><br>";
    echo "<strong>Por favor verifica:</strong>";
    echo "<ul style='margin-bottom:0;'>";
    echo "<li>Que el Host de Supabase sea el correcto en <code>conexion.php</code>.</li>";
    echo "<li>Que la contraseña de la base de datos no tenga caracteres extraños mal escapados.</li>";
    echo "<li>Que cuentes con una conexión activa a internet.</li>";
    echo "</ul>";
    echo "</div>";
    exit;
}

// Si llega aquí, la conexión fue un éxito y la variable $dbconn ya está disponible para tus consultas PHP.
?>
