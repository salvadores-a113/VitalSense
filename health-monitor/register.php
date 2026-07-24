<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    $email = sanitizeInput($_POST['email']);
    $full_name = sanitizeInput($_POST['full_name']);
    $age = sanitizeInput($_POST['age']);
    $weight = sanitizeInput($_POST['weight']);
    $height = sanitizeInput($_POST['height']);
    $disease = sanitizeInput($_POST['disease']);
    $disorder = sanitizeInput($_POST['disorder']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO patients (username, password, email, full_name, age, weight, height, disease, disorder) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $email, $full_name, $age, $weight, $height, $disease, $disorder]);
        
        $_SESSION['success_message'] = "Registration successful! Please log in.";
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Paciente</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Registro de Paciente</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre completo:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="age">Edad:</label>
                <input type="number" id="age" name="age" required>
            </div>
            
            <div class="form-group">
                <label for="weight">Peso (kg):</label>
                <input type="number" step="0.01" id="weight" name="weight" required>
            </div>
            
            <div class="form-group">
                <label for="height">Altura (m):</label>
                <input type="number" step="0.01" id="height" name="height" required>
            </div>
            
            <div class="form-group">
                <label for="disease">Enfermedad:</label>
                <input type="text" id="disease" name="disease">
            </div>
            
            <div class="form-group">
                <label for="disorder">Trastorno:</label>
                <input type="text" id="disorder" name="disorder">
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>