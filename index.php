<?php
    
    require_once 'const.php';
    require_once 'src/util.php';

    //FIXME remove it
    //-----------------------------------------------    
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    //-----------------------------------------------
        
    /**
     * Checks if the following conditions are met:
     * - the upload process ended well
     * - the extension is "PHP"/the MIME type is PHP
     * - the file isn't too big
     * - the file isn't a symbolic link //TODO is it useful ?
     * @param $file
     * @return string|number error message or 1
     */
    function checkFile($file){
        if(!$file || $file["error"] > 0){
            return "No file";
        }
        $ext = strtolower(substr(strrchr($file['name'], '.'),1));
        if($ext !== "php" || ($file["type"] !== "application/x-php")){
            return "Not a PHP file";
        }
        if($file['size'] > MAX_FILE_SIZE){
            return "File is too big";
        }
        if(is_link($file["name"])){
            return "Symlink are not allowed";
        }
        if(strposOnArray($pFileName, array(" ", '"', "'", "&", "/", "\\", "?", "#", chr(0)))){
            $this->kill("File name contains almost one bad char");
        }
        return 1;
    }
    
    if(isset($_FILES["upload"])){
        $res = checkFile($_FILES["upload"]);
        if(is_string($res)){
            die($res);
        }
        else{
            echo "OK"; //TODO remove it
        }
    }
?>
<html>
	<head>
	</head>
	<body>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="upload">
			<br/>
			<br/>
			<input type="submit">
		</form>
	</body>
</html>