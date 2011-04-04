<?php
class Autoloader
{
    public function autoload($className)
    {
        set_include_path(
            get_include_path()
          . PATH_SEPARATOR
          . '/usr/share/php/libzend-framework-php/'
        );

        $directories = array(
            '',
            'html/',
            'tests/'
        );

        //Add your file naming formats here
        $fileNameFormats = array(
          '%s.php'
        );

        $path = $className;

        if(@include_once $path.'.php'){
            return;
        }

        foreach($directories as $directory){
            foreach($fileNameFormats as $fileNameFormat){
                $path = $directory . sprintf($fileNameFormat, $className);
                if(file_exists($path)){
                    include_once $path;
                    return;
                }
            }
        }
         // get separated directories
        $pathchunks=explode("_",$className);

        //re-build path without last item
        for($i = 0; $i < (count($pathchunks)-1); $i++) {
            $fullclasspath .= $pathchunks[$i] . '/';
            $className = $pathchunks[$i+1];
        }
        include_once $fullclasspath . $className . '.php';
    }
}