<?php
class Files {
    private $uploaddir;
    private $files;
    private $fileName;

    public function __construct($dir, $fil){
        if($dir == ''){
            throw new Exception("Upload dir not defined!", 1);
        }
        if(!$fil){
            throw new Exception("File error!");
        }

        $this->uploaddir = $dir;
        $this->files = $fil;
        $this->fileName = $this->files['inputfile']['name'];
        try{
            $this->upload();
        } catch(Exception $e){
            echo $e;
        }
    }

    private function upload(){
        if(!$this->fileName)
            throw new Exception("file name not defined", 1);

        $uploadfile = $this->uploaddir . basename($this->fileName);

        if (!move_uploaded_file($this->files['inputfile']['tmp_name'], $uploadfile)) {
            throw new Exception("Failed to move upload file!", 1);
        } else {
            chmod($uploadfile, 0777);
        }
    }

    public function getFileName(){
        return $this->fileName;
    }
}
