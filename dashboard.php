<?php
session_start();
$logged = $_SESSION['logged'];

if (!$logged) {
    echo "Ingreso no autorizado";
    die();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handylogg</title>
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
                    <a href="login.php">Cerrar sesión</a>
                </nav>
            </div>
            <!--barra-->

        </div>
        <!--contenedor-->
        <div class="site-header-fondo">
            <img src="img/fondo7.png" alt="Fondo">
        </div>
    </header>

    <section class="seccion-medidas">
        <fieldset class="margenes-fieldset">
            <label>Temperatura sonda</label>
            <h4 class="margenH4"><b id="display_temp">-- </b><span class="text-sm"> °C</span></h4>
            <label>Presion</label>
            <h4 class="margenH4"><b id="display_presion">-- </b><span class="text-sm"> kpa</span></h4>
            <label>Voltaje Bateria</label>
            <h4 class="margenH4"><b id="display_voltajeBat">-- </b><span class="text-sm"> V</span></h4>
        </fieldset>
    </section>

    <div id="grafico"></div>
    <script src="script.js"></script>

    <!-- build:js scripts/app.html.js -->
    <!-- jQuery -->
    <script src="libs/jquery/jquery/dist/jquery.js"></script>
    <!-- Bootstrap -->
    <script src="libs/jquery/tether/dist/js/tether.min.js"></script>
    <script src="libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
    <!-- core -->
    <script src="libs/jquery/underscore/underscore-min.js"></script>
    <script src="libs/jquery/jQuery-Storage-API/jquery.storageapi.min.js"></script>
    <script src="libs/jquery/PACE/pace.min.js"></script>

    <script src="html/scripts/config.lazyload.js"></script>

    <script src="html/scripts/palette.js"></script>
    <script src="html/scripts/ui-load.js"></script>
    <script src="html/scripts/ui-jp.js"></script>
    <script src="html/scripts/ui-include.js"></script>
    <script src="html/scripts/ui-device.js"></script>
    <script src="html/scripts/ui-form.js"></script>
    <script src="html/scripts/ui-nav.js"></script>
    <script src="html/scripts/ui-screenfull.js"></script>
    <script src="html/scripts/ui-scroll-to.js"></script>
    <script src="html/scripts/ui-toggle-class.js"></script>

    <script src="html/scripts/app.js"></script>

    <!-- ajax -->
    <script src="libs/jquery/jquery-pjax/jquery.pjax.js"></script>
    <script src="html/scripts/ajax.js"></script>

    
    <script src="https://unpkg.com/mqtt@4.2.1/dist/mqtt.min.js"></script>

    <script type="text/javascript">
        /*
         ***********************************************************************
         ************************PROCESOS***************************************
         ***********************************************************************
         */
        function update_values(temp, presion, voltajeBat) {
            $("#display_temp").html(temp);
            $("#display_presion").html(presion);
            $("#display_voltajeBat").html(voltajeBat);
        }

        function process_msg(topic, message) {
            if (topic == "values") {
                var msg = message.toString();
                var sp = msg.split(",");
                var temp = sp[0];
                var presion = sp[1];
                var voltajeBat = sp[2];
                update_values(temp, presion, voltajeBat);
            }
        }


        /*
         ************************************************************************
         ************************CONEXION MQTT***********************************
         ************************************************************************
         */
        // Retorna un entero aleatorio entre min (incluido) y max (excluido)
        // ¡Usando Math.round() te dará una distribución no-uniforme!
        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min)) + min;
        }

        var idClient = getRandomInt(1000, 1000000);

        const options = {
            connectTimeout: 4000,
            // Authentication
            clientId: 'pagina_web' + idClient,
            username: 'web_client',
            password: 'Victoria250415',
            keepalive: 60,
            clean: true,
        }

        const WebSocket_URL = 'wss://cursoiotvhm.ga:8094/mqtt';
        const client = mqtt.connect(WebSocket_URL, options);

        // after connect
        client.on('connect', () => {
            console.log('Mqtt conectado por WSS exito. Conectado a ', WebSocket_URL);
            // Suscribir a un topico
            client.subscribe('values', {
                qos: 0
            }, (error) => {
                if (!error) {
                    console.log('Suscripcion exitosa');
                } else {
                    console.log('Suscripcion fallida');
                }
            })
        })

        // handle message event
        client.on('message', (topic, message) => {
            console.log('Mensaje recibido bajo topico: ', topic, '->', message.toString());
            process_msg(topic, message);
        })

        // Reconectar
        client.on('reconnect', (error) => {
            console.log('Error al reconectar: ', error);
        })

        // Error en la conexion
        client.on('error', (error) => {
            console.log('Error de conexion: ', error);
        })
    </script>
</body>

</html>