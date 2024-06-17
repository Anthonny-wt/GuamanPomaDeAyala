<?php
// Arreglo con mensajes que puede recibir
$messages = [
    "1" => "Credenciales incorrectas",
    "2" => "No ha iniciado sesión"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | GuamanPomaAyala</title>
    <link rel="stylesheet" href="css/stylelogin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
<div class="ring">
  <i style="--clr:#00ff0a;"></i>
  <i style="--clr:#ff0057;"></i>
  <i style="--clr:#fffd44;"></i>
  <div class="login">
    <h2>Institución Educativa</h2>
    <h2>Guaman Poma de Ayala</h2>
    <div class="inputBx">
      <form method="post" class="form" action="login_post.php">
        <input type="text" placeholder="Usuario" name="username">
        <input type="password" placeholder="Contraseña" name="password">
        <input type="submit" value="Ingresar">
      </form>
    </div>
    <?php
    if (isset($_GET['err']) && is_numeric($_GET['err']) && $_GET['err'] > 0 && $_GET['err'] < 3) {
        echo '<span class="error">'.$messages[$_GET['err']].'</span>';
    }
    ?>
  </div>
</div>
  
</body>
</html>
