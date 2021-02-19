<?php

require __DIR__ . '/vendor/autoload.php';


/**
 * Archivo tomado de https://github.com/yannisg/Google-Drive-Uploader-PHP/blob/master/gdrive_upload.php
 * Author: Yannis Giovanos - https://github.com/yannisg
 */

class gdrive{
	
	//variables
	public $fileRequest;
	public $mimeType;
	public $filename;
	public $path;
	public $client;
	
	
	function __construct($client){
		require __DIR__ . '/vendor/autoload.php';
		$this->client = $client;
	}
	
	function processFile(){
		
		$fileRequest = $this->fileRequest;
		echo "Process File $fileRequest\n";
		$path_parts = pathinfo($fileRequest);
		$this->path = $path_parts['dirname'];
		$this->fileName = $path_parts['basename'];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->mimeType = finfo_file($finfo, $fileRequest);
		finfo_close($finfo);
		
		echo "Mime type is " . $this->mimeType . "\n";
		
		$this->upload();
			
	}
	
	function upload(){
		$client = $this->client;
		
		$file = new Google_Service_Drive_DriveFile();
		$file->title = $this->fileName;
		$chunkSizeBytes = 1 * 1024 * 1024;
		
		$fileRequest = $this->fileRequest;
		$mimeType = $this->mimeType;
		
		$service = new Google_Service_Drive($client);
		$request = $service->files->insert($file);

		// Create a media file upload to represent our upload process.
		$media = new Google_Http_MediaFileUpload(
		  $client,
		  $request,
		  $mimeType,
		  null,
		  true,
		  $chunkSizeBytes
		);
		$media->setFileSize(filesize($fileRequest));

		// Upload the various chunks. $status will be false until the process is
		// complete.
		$status = false;
		$handle = fopen($fileRequest, "rb");
		
		// start uploading		
		echo "Uploading: " . $this->fileName . "\n";  
		
		$filesize = filesize($fileRequest);
		
		// while not reached the end of file marker keep looping and uploading chunks
		while (!$status && !feof($handle)) {
			$chunk = fread($handle, $chunkSizeBytes);
			$status = $media->nextChunk($chunk);  
		}
		
		// The final value of $status will be the data from the API for the object
		// that has been uploaded.
		$result = false;
		if($status != false) {
		  $result = $status;
		}

		fclose($handle);
		// Reset to the client to execute requests immediately in the future.
		$client->setDefer(false);	
	}
	
}

?>