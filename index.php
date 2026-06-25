
<?php
// --- LÓGICA DEL BACKEND (PHP) ---
$host = '172.16.90.178'; 
$dbname = 'blog_db';
$usuario = 'rociojaluf';
$contrasena = 'rocio2026';

$conn = @new mysqli($host, $usuario, $contrasena, $dbname);

$mostrar_toast = false;

if ($conn->connect_error == null) {

    $sql = "CREATE TABLE IF NOT EXISTS datos_personales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contenido TEXT NOT NULL
    )";
    $conn->query($sql);

    // (método POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nuevo_dato'])) {
        $nuevo_dato = $conn->real_escape_string($_POST['nuevo_dato']);
        $insert = "INSERT INTO datos_personales (contenido) VALUES ('$nuevo_dato')";
        
        if ($conn->query($insert)) {
            $mostrar_toast = true; 
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Personal - Rocío</title>
    <style>
       
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            background-color: #fcfbf5; 
            color: #333; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 30px 20px; 
            line-height: 1.6; 
        }

        .tarjeta { 
            background-color: #ffffff; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.03); 
            margin-bottom: 25px; 
            border: 1px solid #b07d8b;
        }

        .cabecera { 
            display: flex; 
            align-items: center; 
            gap: 30px; 
        }
        .cabecera img { 
            width: 150px; 
            height: 150px; 
            object-fit: cover; 
            object-position: top center;
            border-radius: 8px; 
            border: 3px solid #e8e3d3;
        }
        .cabecera h1 { 
            margin: 0; 
            color: #4a4a4a; 
            font-size: 28px; 
        }

        hr { 
            border: 0; 
            height: 1px; 
            background-color: #b07d8b; 
            margin: 25px 0; 
        }

        .datos-personales p { margin: 8px 0; font-size: 16px; }
        .datos-personales strong { color: #1b1818; }
        
        #lista-datos { 
            margin: 10px 0 0 0; 
            padding-left: 20px; 
            color: #332528; 
            font-weight: 500;
        }
        #lista-datos li { margin-bottom: 8px; }

        h2 { margin-top: 0; color: #4a4a4a; font-size: 20px; }
        .grupo-form { display: flex; gap: 10px; margin-top: 15px; }
        input[type="text"] { 
            flex: 1; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 15px; 
            outline: none;
        }
        input[type="text"]:focus { border-color: #b07d8b; }
        button { 
            padding: 12px 24px; 
            background-color: #b07d8b; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: background 0.2s;
        }
        button:hover { background-color: #966774; }

        .btn-pdf { 
            display: inline-block; 
            background-color: #b07d8b; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: bold; 
            margin-top: 10px; 
            transition: background 0.2s;
        }
        .btn-pdf:hover { background-color: #5a6268; }

        .toast {
            position: fixed;
            top: 20px;
            right: -300px; 
            background-color: #4CAF50; 
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-weight: bold;
            transition: right 0.4s ease-in-out; 
            z-index: 1000;
        }
        .toast.mostrar { right: 20px; } 
    </style>
</head>
<body>

    <div id="notificacion" class="toast">✅ Dato agregado correctamente</div>

    <div class="tarjeta">
        <div class="cabecera">
            <img src="https://github.com/rociojota/TPF-Virtualizacion-JalufRocio/blob/main/foto.png?raw=true" alt="Foto de Rocío">
            <h1>Blog personal Rocío Jaluf</h1>
        </div>
        
        <hr>

        <div class="datos-personales">
            <ul id="lista-datos">
                <li><strong>DNI:</strong> 43003592</li>
                <li><strong>Carrera:</strong> Ingeniería en Sistemas de Información</li>
                <li><strong>Institución:</strong> UTN - FRT</li>
                <li><strong>Materia:</strong> Virtualización y consolidación de servidores</li>

                <?php
                if ($conn->connect_error == null) {
                    $resultado = $conn->query("SELECT contenido FROM datos_personales");
                    if ($resultado && $resultado->num_rows > 0) {
                        while($fila = $resultado->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($fila['contenido']) . "</li>";
                        }
                    }
                }
                ?>    

            </ul>
        </div>
    </div>

    <div class="tarjeta">
        <h2>Publicar nuevos datos al blog</h2>
        <form id="formulario-blog" method="POST">
            <div class="grupo-form">
                <input type="text" name="nuevo_dato" id="nuevo_dato" placeholder="Escribe aquí tu actualización..." required>
                <button type="submit">Agregar dato</button>
            </div>
        </form>
    </div>
   
    <div class="tarjeta">
        <h2>Documentación trabajo práctico final </h2>
        <a href="informe_tpf.pdf" class="btn-pdf" target="_blank">📄 Descargar el informe en PDF</a>
    </div>

   
    <script>
        const estadoGuardado = "<?php echo $mostrar_toast ? 'exito' : 'espera'; ?>";

        if (estadoGuardado === 'exito') {
            const toast = document.getElementById('notificacion');
            toast.classList.add('mostrar');

            // 3 segundos
            setTimeout(() => {
                toast.classList.remove('mostrar');
            }, 3000);
        }
    </script>

</body>
</html>