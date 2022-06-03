<?php
namespace CLICommandsMaker;

use Exception;
use CLICommandsMaker\Command;

/**
 * Агент для рагистрации команды её аргуметов и параметров, обработки данных переданных пользователем в консоли
 * <p>Для регистрации команды нужно использовать метод newCommand</p>
 * <p>После регистрации всех команд необходимо вызвать метод employ для обработки данных
 * <p>После вызова метода employ необходимо использовать созданные объекты Command</p>
 *
 * Class Agent
 * @package CLICommandsMaker
 */
class Agent
{
    /**
     * Перечисляется как ["command_name" => object Command]
     *
     * @var array
     */
    private array $arCommands = array();

    /**
     * Содержит в себе набор необработанных аргументов из консоли
     *
     * @var array
     */
    private array $arCLIArgs;
    private bool $bEmployed = false;

    public function __construct()
    {
        global $argv;
        $this->arCLIArgs = $argv;
    }

    /**
     * Регистрирует новую команду
     * Возвращает объект Command
     *
     * @param string $sName - Имя команды
     * @param string $sDescription - Описание команды (не обязательно)
     * @param array $arArguments - Аргументы команды, если указаны будут приниматься только зарегистрированные аргументы (не обязательно)
     * @param array $arParams - Параметры команды, если указаны будут приниматься только зарегистрированные параметры (не обязательно)
     * @return Command
     * @throws Exception - Выбрасывает исключение, если будет попытка зарегистрировать уже зарегистрированную команду
     */
    public function newCommand(string $sName, string $sDescription = "", array $arArguments = array(), array $arParams = array()): Command
    {
        $sName = mb_strtolower($sName);
        if(key_exists($sName, $this->arCommands)){
            throw new Exception("Команда " . $sName . " уже зарегистрирована");
        }
        $oNewCommand = new Command($sName, $sDescription, $arArguments, $arParams);
        $this->arCommands[$sName] = $oNewCommand;
        return $oNewCommand;
    }

    /**
     * Основной метод агента, вызывается после регистрации всех команд их аргументов и параметров
     * <p>Определяет какая команда вызвана</p>
     * <p>Определяет и запоминает переданные аргуметы, параметры и их значения</p>
     *
     * @throws Exception - Выбрасывает исключение если employ был вызван повторно
     */
    public function employ(): void
    {
        if($this->bEmployed){
            throw new Exception("Агент уже был использован");
        }
        $oCalledCommand = $this->parseCalledCommand();
        if($oCalledCommand !== null){
            $arCalledArguments = $this->parseCallArguments();
            $arCalledParams = $this->parseCalledParams();
            $oCalledCommand->call($arCalledArguments, $arCalledParams);
        }
        $this->bEmployed = true;
    }

    /**
     * Определение имени вызвонной команды
     * <p>Если команда не найдена, печатает предупреждение</p>
     * <p>Если команда не указана, печатает список доступных команд</p>
     * <p>Возвращает объект вызванной команды если команда была найдена в зарегистрированных иначе возвращает null</p>
     *
     * @return Command|null
     */
    private function parseCalledCommand(): ?Command
    {
        $sCalledCommandName = "";
        if(count($this->arCLIArgs) > 1){
            $sCalledCommandName = preg_replace("/^(\{|\[).+(\}|\])$/", "", mb_strtolower(trim($this->arCLIArgs[1])));
        }
        if(empty($sCalledCommandName)){
            echo "Доступные команды в приложении " . trim($this->arCLIArgs[0]) . ":" . PHP_EOL;
            foreach($this->arCommands as $sRegCommandName => $oCommand){
                echo $sRegCommandName . "\t\t- " . $oCommand->getDescription() . PHP_EOL;
            }
        }else{
            if(key_exists($sCalledCommandName, $this->arCommands)){
                return $this->arCommands[$sCalledCommandName];
            }else{
                echo "Команды " . $sCalledCommandName . " в приложении " . trim($this->arCLIArgs[0]) . " не зарегистрировано!" . PHP_EOL .
                    "Запустите приложение без указания команды для вывода всех доступных команд." . PHP_EOL;
            }
        }
        return null;
    }

    /**
     * Определение аргументов переданных с командой
     *
     * @return array
     */
    private function parseCallArguments(): array
    {
        $arCalledArguments = array();
        foreach($this->arCLIArgs as $sArgumentV){
            if(mb_substr($sArgumentV, 0, 1) !== "{" && mb_substr($sArgumentV, -1, 1) !== "}") continue;
            $arMatchCalledArguments = explode(",", trim($sArgumentV, "{}"));
            if(!empty($arMatchCalledArguments)){
                $arCalledArguments = array_merge($arCalledArguments, $arMatchCalledArguments);
            }
        }
        return $arCalledArguments;
    }

    /**
     * Определение параметров и их значений переданных с командой
     *
     * @return array
     */
    private function parseCalledParams(): array
    {
        $arCalledParams = array();
        foreach($this->arCLIArgs as $sArgumentV){
            if(mb_substr($sArgumentV, 0, 1) !== "[" && mb_substr($sArgumentV, -1, 1) !== "]") continue;
            $arMatchCalledParams = explode("=", trim($sArgumentV, "[]"));
            $arCalledParams[$arMatchCalledParams[0]] = explode(",", trim($arMatchCalledParams[1], "{}"));
        }
        return $arCalledParams;
    }
}