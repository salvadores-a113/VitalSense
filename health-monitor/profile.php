<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

// Obtener datos del paciente
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $age = sanitizeInput($_POST['age']);
    $weight = sanitizeInput($_POST['weight']);
    $height = sanitizeInput($_POST['height']);
    $disease = sanitizeInput($_POST['disease']);
    $disorder = sanitizeInput($_POST['disorder']);

    try {
        $stmt = $pdo->prepare("UPDATE patients SET full_name = ?, age = ?, weight = ?, height = ?, disease = ?, disorder = ? WHERE id = ?");
        $stmt->execute([$full_name, $age, $weight, $height, $disease, $disorder, $_SESSION['user_id']]);
        
        $_SESSION['success_message'] = "Perfil actualizado correctamente.";
        header('Location: profile.php');
        exit();
    } catch (PDOException $e) {
        $error = "Error al actualizar el perfil: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Paciente</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Perfil de <?php echo htmlspecialchars($patient['full_name']); ?></h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($patient['username']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($patient['email']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre completo:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($patient['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="age">Edad:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($patient['age']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="weight">Peso (kg):</label>
                <input type="number" step="0.01" id="weight" name="weight" value="<?php echo htmlspecialchars($patient['weight']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="height">Altura (m):</label>
                <input type="number" step="0.01" id="height" name="height" value="<?php echo htmlspecialchars($patient['height']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="disease">Enfermedad:</label>
                <input type="text" id="disease" name="disease" value="<?php echo htmlspecialchars($patient['disease']); ?>">
            </div>
            
            <div class="form-group">
                <label for="disorder">Trastorno:</label>
                <input type="text" id="disorder" name="disorder" value="<?php echo htmlspecialchars($patient['disorder']); ?>">
            </div>
            
            <button type="submit" class="btn">Actualizar Perfil</button>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>