<?php
/**
 * Este codigo se encarga de mardar mensajes de error o logs a un archivo de texto
 */

function customLog($message) {
    $logDir = __DIR__ . '/logs'; // Ruta del directorio de logs
    $logFile = $logDir . '/log.txt'; // Ruta del archivo de log

    // Crear el directorio si no existe
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true); // Crea el directorio con permisos de escritura
    }

    // Crear el archivo de log si no existe
    if (!file_exists($logFile)) {
        file_put_contents($logFile, "Archivo de log creado el: " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
    }

    // Formato del mensaje de log
    $currentDate = date('Y-m-d H:i:s');
    $formattedMessage = "[$currentDate] $message" . PHP_EOL;

    // Escribir el mensaje en el archivo de log
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
