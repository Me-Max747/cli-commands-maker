#!/usr/bin/php
<?php
require_once("libs/autoload.php");

use CLICommandsMaker\Agent;

// Создаем экземпляр мэйкера
$CLICommandsMaker = new Agent;

// Если зарегистрированных аргументов или параметров нет, то разрешено использовать любые
$oSomeCommand = $CLICommandsMaker->newCommand("some_command", "This description for some_command"); // Регистрируем новую команду
$CLICommandsMaker->employ();

if($oSomeCommand->isCalled()){ // Если вызвана команда some_command
    $arCalledArguments = $oSomeCommand->getCalledArguments();
    $arCalledParams = $oSomeCommand->getCalledParams();

    echo PHP_EOL . "Called command: " . $oSomeCommand->getName() . PHP_EOL;

    if(!empty($arCalledArguments)){
        echo PHP_EOL . "Arguments:" . PHP_EOL;
        foreach($arCalledArguments as $sArgument){
            echo "\t- " . $sArgument . PHP_EOL;
        }
    }

    if(!empty($arCalledParams)){
        echo PHP_EOL . "Options:" . PHP_EOL;
        foreach($arCalledParams as $sParam => $arValues){
            echo "\t- " . $sParam . PHP_EOL;
            foreach($arValues as $sValue){
                echo "\t\t- " . $sValue . PHP_EOL;
            }
        }
    }
}