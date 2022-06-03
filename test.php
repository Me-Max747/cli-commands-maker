#!/usr/bin/php
<?php
require_once("libs/autoload.php");

use CLICommandsMaker\Agent;

// Создаем экземпляр мэйкера
$CLICommandsMaker = new Agent;

// Если зарегистрированных аргументов или параметров нет, то разрешено использовать любые
$oCommandName = $CLICommandsMaker->newCommand("command_name", "description for command", array(), array("param1", "param2", "param3")); // Регистрируем новую команду

$oCommandName->setDescription("description for command"); // Задает описание команды
echo $oCommandName->getDescription() . PHP_EOL; // Возвращает описание команды

$oCommandName->setArguments(array("verbose", "overwrite", "unlimited", "log")); // Регистрирует аргументы для команды
echo implode(", ", $oCommandName->getArguments()) . PHP_EOL; // Возвращает зарегистрированные аргументы

$oCommandName->setParams(array("log_file", "methods", "paginate")); // Регистрирует параметры для команды
echo implode(", ", $oCommandName->getParams()) . PHP_EOL; // Возвращает зарегистрированные параметры

$CLICommandsMaker->employ(); // Передаем обработку команд из консоли, только после регистрации всех команд их аргументов и параметров

$oCommandName->setArguments(array("log"));

// Пишем логику для каждой команды
if($oCommandName->isCalled()){
    echo $oCommandName->getName() . " called" . PHP_EOL;
    echo implode(", ", $oCommandName->getCalledArguments()) . PHP_EOL;
    echo var_dump($oCommandName->getCalledParams()) . PHP_EOL;
    echo var_dump($oCommandName->getValuesByCalledParam("param1")) . PHP_EOL;
}