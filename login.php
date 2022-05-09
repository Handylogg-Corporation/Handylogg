<?php
session_start();
$_SESSION['logged'] = false;

$msg="";
$email="";

if(isset($_POST['email']) && isset($_POST['password'])) {

  if ($_POST['email']==""){
    $msg.="Debe ingresar un email <br>";
  }else if ($_POST['password']=="") {
    $msg.="Debe ingresar la clave <br>";
  }else {
    $email = strip_tags($_POST['email']);
    $password= sha1(strip_tags($_POST['password']));

    //momento de conectarnos a db
    $conn = mysqli_connect("localhost","admin_cursoiot","Victoria2504","admin_cursoiot");


    if ($conn==false){
      echo "Hubo un problema al conectarse a María DB";
      die();
    }

    $result = $conn->query("SELECT * FROM `users` WHERE `users_email` = '".$email."' AND  `users_password` = '".$password."' ");
    $users = $result->fetch_all(MYSQLI_ASSOC);


    //cuento cuantos elementos tiene $tabla,
    $count = count($users);

    if ($count == 1){

      //cargo datos del usuario en variables de sesión
      $_SESSION['user_id'] = $users[0]['users_id'];
      $_SESSION['users_email'] = $users[0]['users_email'];

      $msg .= "Exito!!!";
      $_SESSION['logged'] = true;

      echo '<meta http-equiv="refresh" content="1; url=dashboard.php">';
    }else{
      $msg .= "Acceso denegado!!!";
      $_SESSION['logged'] = false;
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&family=PT+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header class="site-header">
    <div class="contenedor">
      <div class="barra">
        <a href="/">
          <img src="img/LogoTM2.svg" alt="Logo">
        </a>
        <nav class="navegacion">
          <a href="index.html">Inicio</a>
          <a href="contacto.html">Contacto</a>
        </nav>
      </div><!--barra-->

    </div><!--contenedor-->
  </header>


  <main>
    

    <div class="formulario">
      <form target="" method="post" name="form">
        <h1 class="centrar-texto">Inicio de sesión</h1>
        <fieldset>
          <h3>Ingrese sus datos</h3>
          <label for="correo">Usuario</label>
          <input id="correo" name="email" type="email" class="md-input" value="<?php echo $email ?>" ng-model="user.email" required >
            
          <label for="clave">Contraseña</label>
          <input id="clave" name="password" type="password" class="md-input" ng-model="user.password" required >
    
        </fieldset>
    
        <button type="submit" class="boton boton-amarillo">Iniciar sesión</button>
      </form>
    
      <div style="color:red" class="">
        <?php echo $msg ?>
      </div>

    </div>
  </main>

</body>
</html>