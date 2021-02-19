<?php

/**
 * Este codigo esta basado en  video https://www.youtube.com/watch?v=zI0obqa8r80&ab_channel=FreddySarango
 * siguiendo las instrucciones al pie de la letra del video esta funciona 
 * en google developt cambia un poco la forma de crear la credencial pero en teoria es lo mismo
 */
include __DIR__ . '/vendor/autoload.php';


$file_path = 'prueba.docx';
$file_name = 'documento';
$carpeta_id = '1v3Sn0dBdrMyFIV6RyqSo4jW0rlrPhs7I';

putenv('GOOGLE_APPLICATION_CREDENTIALS='.__DIR__.'/acceso.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);

try {
    $service = new Google_Service_Drive($client);

    $file = new Google_Service_Drive_DriveFile();

    $fileMimeType = mime_content_type($file_path); 

    $file->setName($file_name);
    $file->setParents([$carpeta_id]); // id de la carpeta en google drive
    $file->setDescription('Archivo subido desde php');
    $file->setMimeType($fileMimeType);

    $result = $service->files->create(
        $file,
        [
            'data' => file_get_contents($file_path),
            'mimeType' => $fileMimeType,
            'uploadType' => 'media',
        ]
    );

    print_r($result->id);exit;

} catch (Google_Service_Exception $gs) {
    $message = json_decode($gs->getMessage());
    print_r($message); exit;
} catch (Exception $e) {
    print_r($e->getMessage());
}