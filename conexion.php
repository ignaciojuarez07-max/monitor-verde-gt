<?php
// conexion.php

$host     = "db.dkvvrpepffixgqrihyrg.supabase.co"; 
$port     = "5432";                                
$dbname   = "postgres";                            
$user     = "postgres";                            
$password = "MonitorVerde2026";                    // <-- TU NUEVA CONTRASEÑA AQUÍ

$connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} sslmode=require";

$dbconn = pg_connect($connection_string);

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
?>