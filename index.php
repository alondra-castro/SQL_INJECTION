<?php
// Base de datos: Conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sql_injection_demo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear tablas de ejemplo (ejecútalo solo una vez)
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255)
);";
$conn->query($sqlCreateTable);

// Insertar datos de prueba
$sqlInsertData = "INSERT IGNORE INTO users (username, password) VALUES
    ('admin', 'password123'),
    ('user', 'userpass');";
$conn->query($sqlInsertData);

// Página principal
if (!isset($_GET['attack'])) {
    echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Demostración de SQL Injection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            color: #333;
        }
        header {
            background: #2c3e50;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .container ul {
            list-style: none;
            padding: 0;
        }
        .container ul li {
            margin: 10px 0;
        }
        .container ul li a {
            display: block;
            text-decoration: none;
            color: #3498db;
            font-size: 1.2rem;
            font-weight: bold;
            background: #ecf0f1;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .container ul li a:hover {
            background: #3498db;
            color: #fff;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Demostración de SQL Injection</h1>
    </header>
    <div class='container'>
        <ul>
            <li><a href='?attack=basic'>SQL Injection Básico</a></li>
            <li><a href='?attack=union'>SQL Injection con UNION</a></li>
            <li><a href='?attack=boolean'>SQL Injection basado en booleanos</a></li>
            <li><a href='?attack=blind'>blind SQL Injection</a></li>
            <li><a href='?attack=second'>Second Order SQL Injection</a></li>
            <li><a href='?attack=error'>Error Based SQL Injection</a></li>
            <li><a href='?attack=oob'>OOB SQL Injection</a></li>
            <li><a href='?attack=stored'>Stored Procedure SQL Injection</a></li>
            <li><a href='?attack=load'>LoadFile SQL Injection</a></li>
            <li><a href='?attack=out'>Outfile SQL Injection</a></li>
            <li><a href='?attack=time'>Time Based SQL Injection</a></li>
            <li><a href='?attack=fulltext'>FullTextSearch SQL Injection</a></li>
            <li><a href='?attack=syntactic'>Syntactic SQL Injection</a></li>
        </ul>
    </div>
    <footer>
        <p>&copy; " . date('Y') . "Demostración de Seguridad Informática</p>
    </footer>
</body>
</html>";
    exit;
}

// Casos de ataque
switch ($_GET['attack']) {
    case 'basic':
        basicSQLInjection($conn);
        break;
    case 'union':
        unionSQLInjection($conn);
        break;
    case 'boolean':
        booleanSQLInjection($conn);
        break;
    case 'blind':
        blindSQLInjection($conn);
        break;
    case 'second':
        secondOrderSQLInjection($conn);
        break;
    case 'error':
        errorBasedSQLInjection($conn);
        break;
    case 'oob':
        oobSQLInjection($conn);
        break;
    case 'stored':
        storedProcedureSQLInjection($conn);
        break;


    case 'load':
        loadFileSQLInjection($conn);
        break;

    case 'out':
        outfileSQLInjection($conn);
        break;
    case 'time':
        timeBasedSQLInjection($conn);
        break;
    case 'fulltext':
        fullTextSearchSQLInjection($conn);
        break;
    case 'syntactic':
        syntacticSQLInjection($conn);
        break;
        
                 

    default:
        echo "Ataque no reconocido.";
}


// Ejemplo 1: SQL Injection Básico
function basicSQLInjection($conn)
{
    // Verificar si el formulario ha sido enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener datos del formulario y escapar las entradas para evitar errores de sintaxis
        $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, trim($_POST['username'])) : '';
        $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, trim($_POST['password'])) : '';

        // Ejecutar consulta vulnerable
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password';";
        $result = $conn->query($query);
    }

    // Formulario de prueba
    echo "<h2>Prueba de SQL Injection Básico</h2>";
    echo "<p>Intenta ingresar como nombre de usuario: <code>' OR '1'='1</code> o cualquier otro valor que rompa la lógica.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <label>Contraseña: <input type='text' name='password' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    // Mostrar Mitigación
    echo "<h3>Mitigación: Prevenir Inyección SQL</h3>";
    echo "<p>Para evitar ataques de inyección SQL, se deben usar consultas preparadas que separan el código SQL de los datos del usuario.</p>";
    echo "<pre>
\$stmt = \$conn->prepare(\"SELECT * FROM users WHERE username = ? AND password = ?\");
\$stmt->bind_param(\"ss\", \$username, \$password);  // 'ss' indica que ambos parámetros son de tipo string
\$stmt->execute();
</pre>";

    // Si el formulario ha sido procesado, mostrar el resultado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Consulta vulnerable
        echo "<h2>Consulta Vulnerable (Peligrosa)</h2>";
        echo "<p>Consulta ejecutada (insegura): <code>$query</code></p>";

        // Mostrar el resultado de la consulta vulnerable
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>Acceso concedido (consulta vulnerable).</p>";
        } else {
            echo "<p style='color: red;'>Acceso denegado (consulta vulnerable).</p>";
        }

        echo "<hr>";

        // Consulta segura con parámetros
        echo "<h2>Consulta Segura (Protegida)</h2>";
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        if ($stmt) {
            // Vincular parámetros de forma segura
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $secureResult = $stmt->get_result();

            // Mostrar el resultado de la consulta segura
            if ($secureResult && $secureResult->num_rows > 0) {
                echo "<p style='color: green;'>Acceso concedido (consulta segura).</p>";
            } else {
                echo "<p style='color: red;'>Acceso denegado (consulta segura).</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error al preparar la consulta segura.</p>";
        }
    }

    // Botón para retornar a la página principal
    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
        </form>";

    // Estilos CSS
    echo "<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
        color: #333;
    }
    h2 {
        color: #2c3e50;
    }
    .form-container, .result-container, .mitigation-container {
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        background: #f9f9f9;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    label {
        font-weight: bold;
    }
    input {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        padding: 10px;
        font-size: 16px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color: #2980b9;
    }
    .success {
        color: green;
        font-weight: bold;
    }
    .error {
        color: red;
        font-weight: bold;
    }
    pre {
        background: #ecf0f1;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
    code {
        background: #e3f2fd;
        padding: 2px 4px;
        border-radius: 3px;
    }
    </style>";
}


// Ejemplo 2: SQL Injection con UNION
function unionSQLInjection($conn)
{
   
    // Formulario para pruebas
    echo "<h2>Prueba de SQL Injection con UNION</h2>";
    echo "<p>Intenta ingresar como nombre de usuario algunas de estas pruebas:: <code>' UNION SELECT 1, @@version --'</code> 
     <code>SELECT @@version;</code> 
    <code>' UNION SELECT 1, @@version #'</code> 
    <code>' UNION SELECT 1, 'a' --'</code> para demostrar la vulnerabilidad.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar entrada del usuario
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        // Validar que el username no esté vacío
        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        // Consulta vulnerable (Ejemplo inseguro)
        echo "<h2>Consulta Vulnerable</h2>";
        echo "<p>Consulta ejecutada (insegura): <code>SELECT id, username FROM users WHERE username = '$username';</code></p>";

        // Consulta vulnerable, en la que un atacante puede inyectar código
        $query = "SELECT id, username FROM users WHERE username = '$username';";
        // Protege de errores si la entrada del usuario está malformada
        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p>ID: {$row['id']}, Usuario: {$row['username']}</p>";
                }
            } else {
                echo "<p>No se encontraron resultados (consulta vulnerable).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";

        // Explicación de mitigación
        echo "<h3>Mitigación</h3>";
        echo "<p>La consulta anterior es vulnerable a ataques de inyección SQL, donde un atacante podría insertar código malicioso en el campo <code>username</code>. Para prevenirlo, utilizamos consultas preparadas.</p>";
    }

    // Botón para retornar a index.php
    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

    // Estilos para la página
    echo "<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
        color: #333;
    }
    h2 {
        color: #2c3e50;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    label {
        font-weight: bold;
    }
    input {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        padding: 10px;
        font-size: 16px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color: #2980b9;
    }
    pre {
        background: #ecf0f1;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
    code {
        background: #e3f2fd;
        padding: 2px 4px;
        border-radius: 3px;
    }
    </style>";
}



// Ejemplo 3: SQL Injection basado en booleanos
function booleanSQLInjection($conn)
{
    

    // Formulario con diseño mejorado
    echo "<div class='form-container'>";
    echo "<h2>Prueba de SQL Injection basado en booleanos</h2>";
    echo "<p>Prueba con: <code>' OR 1=1 --'</code> <code>' OR 'a' = 'a' --' </code> <code>' OR 1=1# </code>  para demostrar la vulnerabilidad.</p>";
    echo "<form method='POST'>
        <label for='username'>Usuario:</label>
        <input type='text' id='username' name='username' placeholder='Ingresa el usuario' required>
        <button type='submit'>Probar</button>
    </form>";
    echo "</div>";

    // Explicación de mitigación
    echo "<div class='mitigation-container'>";
    echo "<h3>Mitigación</h3>";
    echo "<p>Para prevenir este tipo de ataque, es importante usar consultas preparadas. Ejemplo:</p>";
    echo "<pre>
\$stmt = \$conn->prepare(\"SELECT * FROM users WHERE username = ?\");
\$stmt->bind_param(\"s\", \$username);
\$stmt->execute();
</pre>";
    echo "</div>";


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar entrada del usuario
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        // Verificar que el campo no esté vacío
        if (empty($username)) {
            echo "<div class='error'>Por favor, ingresa un nombre de usuario.</div>";
            return;
        }

        echo "<div class='result-container'>";
        echo "<h2>Consulta Vulnerable</h2>";

        // Consulta vulnerable
        $query = "SELECT * FROM users WHERE username = '$username';";
        echo "<p>Consulta ejecutada (insegura): <code>$query</code></p>";

        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<div class='success'>Usuario encontrado (consulta vulnerable).</div>";
            } else {
                echo "<div class='error'>Usuario no encontrado (consulta vulnerable).</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>Error en la consulta SQL: {$e->getMessage()}</div>";
        }

        echo "<hr>";

        echo "<h2>Consulta Segura</h2>";

        // Consulta segura con consultas preparadas
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $secureResult = $stmt->get_result();

            if ($secureResult && $secureResult->num_rows > 0) {
                echo "<div class='success'>Usuario encontrado (consulta segura).</div>";
            } else {
                echo "<div class='error'>Usuario no encontrado (consulta segura).</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='error'>Error al preparar la consulta segura.</div>";
        }
        echo "</div>";
    }
   // Botón para retornar a index.php
   echo "<br><br>";
   echo "<form action='index.php' method='get'>
           <button type='submit'>Volver a la página principal</button>
         </form>";
    // Estilos CSS
    echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}


function blindSQLInjection($conn)
{
    echo "<h2>Prueba de Blind SQL Injection</h2>";
    echo "<p>Intenta ingresar como usuario: <code>' AND 1=1 --</code> o <code>' AND 1=2 --</code> para demostrar la vulnerabilidad.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT id, username FROM users WHERE username = '$username';";

        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<p>¡Vulnerabilidad de Blind SQL Injection detectada!</p>";
            } else {
                echo "<p>No se encontraron resultados (consulta vulnerable).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Utiliza consultas preparadas para evitar que el atacante inyecte valores booleanos que afecten la consulta.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

          echo "<style>
          body {
              font-family: Arial, sans-serif;
              line-height: 1.6;
              margin: 20px;
              color: #333;
          }
          h2 {
              color: #2c3e50;
          }
          .form-container, .result-container, .mitigation-container {
              border: 1px solid #ccc;
              border-radius: 8px;
              padding: 20px;
              margin: 20px 0;
              background: #f9f9f9;
          }
          form {
              display: flex;
              flex-direction: column;
              gap: 10px;
          }
          label {
              font-weight: bold;
          }
          input {
              padding: 10px;
              font-size: 16px;
              border: 1px solid #ccc;
              border-radius: 5px;
          }
          button {
              padding: 10px;
              font-size: 16px;
              background-color: #3498db;
              color: white;
              border: none;
              border-radius: 5px;
              cursor: pointer;
          }
          button:hover {
              background-color: #2980b9;
          }
          .success {
              color: green;
              font-weight: bold;
          }
          .error {
              color: red;
              font-weight: bold;
          }
          pre {
              background: #ecf0f1;
              padding: 10px;
              border-radius: 5px;
              overflow-x: auto;
          }
          code {
              background: #e3f2fd;
              padding: 2px 4px;
              border-radius: 3px;
          }
      </style>";
}



function secondOrderSQLInjection($conn)
{
    echo "<h2>Prueba de Second-Order SQL Injection</h2>";
    echo "<p>Intenta enviar valores como: <code>' OR 1=1 --</code> para demostrar la vulnerabilidad al ejecutar un segundo paso.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        // Simulación de la inyección a través de un segundo paso
        $query = "INSERT INTO users (username) VALUES ('$username');";
        try {
            $conn->query($query);
            echo "<p>Valor insertado correctamente, pero es vulnerable a un ataque de Second-Order SQL Injection.</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error al insertar: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Utiliza consultas preparadas para evitar la inyección de datos maliciosos en la base de datos.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}


function errorBasedSQLInjection($conn)
{
    echo "<h2>Prueba de Error-Based SQL Injection</h2>";
    echo "<p>Intenta ingresar como nombre de usuario algo como: <code>' OR 1=1 --</code> para generar errores SQL y exponer información interna.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT id, username FROM users WHERE username = '$username';";
        try {
            $conn->query($query);
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>No expongas errores de SQL a los usuarios finales y utiliza consultas preparadas para proteger tus bases de datos.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

    
          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}


function oobSQLInjection($conn)
{
    echo "<h2>Prueba de Out-of-Band SQL Injection (OOB)</h2>";
    echo "<p>En esta prueba se intenta exfiltrar datos de la base de datos a través de un canal no relacionado.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        // Usamos un canal de salida para el ataque
        $query = "SELECT id, username FROM users WHERE username = '$username';";
        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<p>¡Vulnerabilidad detectada!</p>";
            } else {
                echo "<p>No se encontraron resultados (consulta vulnerable).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Usa filtros de entrada y salida para prevenir la transmisión de datos fuera de la red interna.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

          echo "<style>
          body {
              font-family: Arial, sans-serif;
              line-height: 1.6;
              margin: 20px;
              color: #333;
          }
          h2 {
              color: #2c3e50;
          }
          .form-container, .result-container, .mitigation-container {
              border: 1px solid #ccc;
              border-radius: 8px;
              padding: 20px;
              margin: 20px 0;
              background: #f9f9f9;
          }
          form {
              display: flex;
              flex-direction: column;
              gap: 10px;
          }
          label {
              font-weight: bold;
          }
          input {
              padding: 10px;
              font-size: 16px;
              border: 1px solid #ccc;
              border-radius: 5px;
          }
          button {
              padding: 10px;
              font-size: 16px;
              background-color: #3498db;
              color: white;
              border: none;
              border-radius: 5px;
              cursor: pointer;
          }
          button:hover {
              background-color: #2980b9;
          }
          .success {
              color: green;
              font-weight: bold;
          }
          .error {
              color: red;
              font-weight: bold;
          }
          pre {
              background: #ecf0f1;
              padding: 10px;
              border-radius: 5px;
              overflow-x: auto;
          }
          code {
              background: #e3f2fd;
              padding: 2px 4px;
              border-radius: 3px;
          }
      </style>";
          
}

function storedProcedureSQLInjection($conn)
{
    echo "<h2>Prueba de Stored Procedure Injection</h2>";
    echo "<p>Intenta inyectar código SQL en procedimientos almacenados usando entradas maliciosas.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        $query = "CALL validate_user('$username');"; // Llamada a procedimiento almacenado
        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<p>¡Vulnerabilidad de Stored Procedure Injection detectada!</p>";
            } else {
                echo "<p>No se encontraron resultados (consulta vulnerable).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Para evitar este tipo de ataques, asegúrate de no pasar entradas directamente a procedimientos almacenados sin validación previa.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";



          echo "<style>
          body {
              font-family: Arial, sans-serif;
              line-height: 1.6;
              margin: 20px;
              color: #333;
          }
          h2 {
              color: #2c3e50;
          }
          .form-container, .result-container, .mitigation-container {
              border: 1px solid #ccc;
              border-radius: 8px;
              padding: 20px;
              margin: 20px 0;
              background: #f9f9f9;
          }
          form {
              display: flex;
              flex-direction: column;
              gap: 10px;
          }
          label {
              font-weight: bold;
          }
          input {
              padding: 10px;
              font-size: 16px;
              border: 1px solid #ccc;
              border-radius: 5px;
          }
          button {
              padding: 10px;
              font-size: 16px;
              background-color: #3498db;
              color: white;
              border: none;
              border-radius: 5px;
              cursor: pointer;
          }
          button:hover {
              background-color: #2980b9;
          }
          .success {
              color: green;
              font-weight: bold;
          }
          .error {
              color: red;
              font-weight: bold;
          }
          pre {
              background: #ecf0f1;
              padding: 10px;
              border-radius: 5px;
              overflow-x: auto;
          }
          code {
              background: #e3f2fd;
              padding: 2px 4px;
              border-radius: 3px;
          }
      </style>";
}


function loadFileSQLInjection($conn)
{
    echo "<h2>Prueba de SQL Injection con LOAD_FILE</h2>";
    echo "<p>Este tipo de inyección permite a un atacante leer archivos del servidor. Puedes probar con un archivo como: <code>/etc/passwd</code> (en sistemas Linux).</p>";
    echo "<form method='POST'>
        <label>Ruta del archivo: <input type='text' name='file' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file = isset($_POST['file']) ? trim($_POST['file']) : '';

        if (empty($file)) {
            echo "<p style='color: red;'>El campo de archivo no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT LOAD_FILE('$file') AS file_content;";
        try {
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            if ($row && $row['file_content']) {
                echo "<pre><code>{$row['file_content']}</code></pre>";
            } else {
                echo "<p>No se pudo cargar el archivo. (Posible falta de permisos o archivo no encontrado)</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>No permitas que el parámetro de archivo sea accesible para consultas SQL directas. Usa consultas preparadas y valida entradas de manera exhaustiva.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";


          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}

function outfileSQLInjection($conn)
{
    echo "<h2>Prueba de SQL Injection con INTO OUTFILE</h2>";
    echo "<p>Este tipo de inyección permite escribir en archivos del servidor. Asegúrate de no permitir que los usuarios ingresen rutas de archivo.</p>";
    echo "<form method='POST'>
        <label>Ruta del archivo: <input type='text' name='file' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file = isset($_POST['file']) ? trim($_POST['file']) : '';

        if (empty($file)) {
            echo "<p style='color: red;'>El campo de archivo no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT * INTO OUTFILE '$file' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\n' FROM users;";
        try {
            $conn->query($query);
            echo "<p>Archivo exportado exitosamente (vulnerabilidad detectada).</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Evita permitir rutas de archivo especificadas por el usuario, y utiliza roles y privilegios de base de datos para limitar el acceso a este tipo de operaciones.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";


          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}

function timeBasedSQLInjection($conn)
{
    echo "<h2>Prueba de Time-Based SQL Injection</h2>";
    echo "<p>Este tipo de inyección espera una respuesta diferente basada en el tiempo (espera en el servidor) para confirmar la vulnerabilidad.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT id FROM users WHERE username = '$username' AND IF(1=1, SLEEP(5), 0);";
        try {
            $start = microtime(true);
            $conn->query($query);
            $end = microtime(true);
            if (($end - $start) > 3) {
                echo "<p>¡Vulnerabilidad de Time-Based SQL Injection detectada!</p>";
            } else {
                echo "<p>No hubo retraso (consulta segura).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Utiliza consultas preparadas y restricciones para evitar que la base de datos ejecute operaciones innecesarias que puedan ser explotadas.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";

          echo "<style>
          body {
              font-family: Arial, sans-serif;
              line-height: 1.6;
              margin: 20px;
              color: #333;
          }
          h2 {
              color: #2c3e50;
          }
          .form-container, .result-container, .mitigation-container {
              border: 1px solid #ccc;
              border-radius: 8px;
              padding: 20px;
              margin: 20px 0;
              background: #f9f9f9;
          }
          form {
              display: flex;
              flex-direction: column;
              gap: 10px;
          }
          label {
              font-weight: bold;
          }
          input {
              padding: 10px;
              font-size: 16px;
              border: 1px solid #ccc;
              border-radius: 5px;
          }
          button {
              padding: 10px;
              font-size: 16px;
              background-color: #3498db;
              color: white;
              border: none;
              border-radius: 5px;
              cursor: pointer;
          }
          button:hover {
              background-color: #2980b9;
          }
          .success {
              color: green;
              font-weight: bold;
          }
          .error {
              color: red;
              font-weight: bold;
          }
          pre {
              background: #ecf0f1;
              padding: 10px;
              border-radius: 5px;
              overflow-x: auto;
          }
          code {
              background: #e3f2fd;
              padding: 2px 4px;
              border-radius: 3px;
          }
      </style>";
}


function fullTextSearchSQLInjection($conn)
{
    echo "<h2>Prueba de Full-Text Search Injection</h2>";
    echo "<p>En este tipo de inyección, el atacante utiliza una consulta de búsqueda de texto completo para manipular los resultados.</p>";
    echo "<form method='POST'>
        <label>Buscar: <input type='text' name='search' required></label><br>
        <button type='submit'>Buscar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';

        if (empty($search)) {
            echo "<p style='color: red;'>El campo de búsqueda no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT id, username FROM users WHERE MATCH (username) AGAINST ('$search' IN NATURAL LANGUAGE MODE);";
        try {
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<p>Resultados encontrados:</p>";
                while ($row = $result->fetch_assoc()) {
                    echo "<p>Usuario: {$row['username']}</p>";
                }
            } else {
                echo "<p>No se encontraron resultados (consulta vulnerable).</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Valida las entradas de los usuarios y usa parámetros preparados para evitar la inyección de código malicioso en las búsquedas.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";



          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}

function syntacticSQLInjection($conn)
{
    echo "<h2>Prueba de Syntactic SQL Injection</h2>";
    echo "<p>Esta inyección ocurre cuando el atacante manipula la sintaxis de la consulta para provocar errores.</p>";
    echo "<form method='POST'>
        <label>Usuario: <input type='text' name='username' required></label><br>
        <button type='submit'>Probar</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';

        if (empty($username)) {
            echo "<p style='color: red;'>El campo de usuario no puede estar vacío.</p>";
            return;
        }

        $query = "SELECT id FROM users WHERE username = '$username';";
        try {
            // Intento de inyección sintáctica con una cadena maliciosa
            $malicious_input = "' OR 1=1 --";
            $query_injection = "SELECT id FROM users WHERE username = '$malicious_input';";
            $conn->query($query_injection);
            echo "<p>¡Inyección sintáctica exitosa!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error en la ejecución de la consulta: {$e->getMessage()}</p>";
        }

        echo "<hr>";
        echo "<h3>Mitigación</h3>";
        echo "<p>Utiliza consultas preparadas y evita el uso directo de datos de entrada de los usuarios para construir consultas SQL.</p>";
    }

    echo "<br><br>";
    echo "<form action='index.php' method='get'>
            <button type='submit'>Volver a la página principal</button>
          </form>";


          echo "<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        .form-container, .result-container, .mitigation-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        pre {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            background: #e3f2fd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>";
}


?>
