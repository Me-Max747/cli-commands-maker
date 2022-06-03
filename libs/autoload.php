<?php
spl_autoload_register(function ($sClass){
    $sSep = DIRECTORY_SEPARATOR;
    $sPath = __DIR__ . $sSep . trim(str_replace(array("/", "\\"), $sSep, $sClass), $sSep) . ".php";
    if(file_exists($sPath)) require $sPath;
});