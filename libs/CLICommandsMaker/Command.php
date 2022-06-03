<?php
namespace CLICommandsMaker;

use Exception;

class Command
{
    private string $sName;
    private string $sDescription;
    private array $arArguments;

    /**
     * Предустановленные аргументы
     * <p>Для их выполнения требуется метод public или private с идентичным названием</p>
     *
     * @var array|string[]
     */
    private array $arPresetArguments = array("help");
    private array $arParams;
    private array $arCalledArguments = array();
    private array $arCalledParams = array();
    private bool $isCalled = false;
    private bool $bEmployed = false;

    public function __construct(string $sName, string $sDescription = "", array $arArguments = array(), array $arParams = array())
    {
        $this->sName = $sName;
        $this->sDescription = $sDescription;
        $this->arArguments = $arArguments;
        $this->arParams = $arParams;
    }

    public function call(array $arCalledArguments, array $arCalledParams): void
    {
        // Сверяем входящие аргументы с зарегистрированными
        $arErrorArguments = array();
        if(!empty($this->arArguments)){
            foreach($arCalledArguments as $sCalledArgument){
                if(!in_array($sCalledArgument, $this->arPresetArguments) && !in_array($sCalledArgument, $this->arArguments)){
                    $arErrorArguments[] = "{" . $sCalledArgument . "}";
                }
            }
            if(!empty($arErrorArguments)){
                echo "Аргумент" . (count($arErrorArguments) > 1 ? "ы" : "") . " " . implode(", ", $arErrorArguments) .
                    " не зарегистрирован" . (count($arErrorArguments) > 1 ? "ы" : "") . " в команде" . PHP_EOL;
            }
        }

        // Сверяем входящие параметры с зарегистрированными
        $arErrorParams = array();
        if(!empty($this->arParams)){
            foreach($arCalledParams as $sCalledParam => $arCallParamValues){
                if(!in_array($sCalledParam, $this->arParams)){
                    $arErrorParams[] = "[" . $sCalledParam . "]";
                }
            }
            if(!empty($arErrorParams)){
                echo "Параметр" . (count($arErrorParams) > 1 ? "ы" : "") . " " . implode(", ", $arErrorParams) .
                    " не зарегистрирован" . (count($arErrorParams) > 1 ? "ы" : "") . " в команде" . PHP_EOL;
            }
        }

        if(!empty($arErrorArguments) || !empty($arErrorParams)){
            echo "Запустите команду с аргуметом {help} для получения списка всех зарегистрированных аргументов и параметров" . PHP_EOL;
        }

        $this->arCalledArguments = $arCalledArguments;
        $this->arCalledParams = $arCalledParams;
        $this->isCalled = true;

        foreach($arCalledArguments as $sCalledArgument){
            if(in_array($sCalledArgument, $this->arPresetArguments) && method_exists($this, $sCalledArgument)){
                $this->$sCalledArgument();
            }
        }
    }

    /**
     * Возвращает true если команда была вызвана, иначе возвращает false
     *
     * @return bool
     */
    public function isCalled(): bool
    {
        return $this->isCalled;
    }

    /**
     * Выводит в консоль всю информацию о команде
     */
    public function help(): void
    {
        echo $this->getDescription() . PHP_EOL;
        if(!empty($this->arArguments)){
            echo PHP_EOL . "Принимает аргументы:" . PHP_EOL;
            foreach($this->arArguments as $sArgument){
                echo "\t{" . $sArgument . "}" . PHP_EOL;
            }
        }
        if(!empty($this->arParams)){
            echo PHP_EOL . "Принимает параметры:" . PHP_EOL;
            foreach($this->arParams as $sParam){
                echo "\t[" . $sParam . "]" . PHP_EOL;
            }
        }
    }

    /**
     * Возвращает имя команды
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->sName;
    }

    /**
     * Возвращает описание команды
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->sDescription;
    }

    /**
     * Задает описание команде
     *
     * @param string $sDescription
     * @return bool
     */
    public function setDescription(string $sDescription): bool
    {
        $this->sDescription = $sDescription;
        return true;
    }

    /**
     * Возвращает массив доступных аргументов команды
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arArguments;
    }

    /**
     * Задает аргуемты команде
     *
     * @param array $arArguments
     * @return void
     */
    public function setArguments(array $arArguments): void
    {
        $this->arArguments = $arArguments;
    }

    /**
     * Возвращает массив доступных параметров команды
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->arParams;
    }

    /**
     * Задает параметры команде
     *
     * @param array $arParams - Массив параметров
     * @return void
     */
    public function setParams(array $arParams): void
    {
        $this->arParams = $arParams;
    }

    /**
     * Возвращает массив переданных аргументов в консоли
     *
     * @return array
     */
    public function getCalledArguments(): array
    {
        return $this->arCalledArguments;
    }

    /**
     * Возвращает массив переданных параметров и их значений
     * <p>Параметры в качестве ключа</p>
     * <p>Значения параметров в качестве массива</p>
     * <p>array("param_name" => array("param_value"))</p>
     *
     * @return array
     */
    public function getCalledParams(): array
    {
        return $this->arCalledParams;
    }

    /**
     * Возвращает массив значений параметра переданных в консоли
     *
     * @param string $sParam - Название параметра
     * @return array
     */
    public function getValuesByCalledParam(string $sParam): array
    {
        $arResult = array();
        if(!empty($this->arCalledParams) && key_exists($sParam, $this->arCalledParams)){
            $arResult = $this->arCalledParams[$sParam];
        }
        return $arResult;
    }
}