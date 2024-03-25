<?php
namespace Test3;

class newBase
{
    static private int $count = 0;//добавила тип int
    static private array $arSetName = [];//добавила тип array

    /**
     * @param int|string $name
     */
    //добавила тип int
    function __construct(int|string $name = 0)
    {
        if (empty($name)) {//если name пустой
            while (array_search(self::$count, self::$arSetName) != false) {//если в $arSetName нет $count, мы его добавляем
                ++self::$count;
            }
            $name = self::$count;//присваиваем количество $count
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;//добавляем уникальное имя в массив
    }
    private string|int $name;//добавила тип
    /**
     * @return string
     */
    //функция добавляет к name *
    public function getName(): string
    {
        return '*' . $this->name  . '*';
    }
    protected mixed $value;//добавила тип mixed
    /**
     * @param mixed $value
     */

    public function setValue(mixed $value): void//добавила тип аргумента mixed и возвращаемого значения void
    {
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getSize(): string//добавила тип возвращаемого значения
    {
        $size = strlen(serialize($this->value));//длина сериализованой строки
        return strlen($size) + $size;//длина $size + длина сериализованой строки,
    }
    public function __sleep()
    {
        return ['value'];
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        $value = serialize($this->value);//обращение к свойству, добавила $this->
        return $this->name . ':' . strlen($value) . ':' . $value;//name:кол-во элем $value:
    }
    /**
     * @return \Test3\newBase
     */
    public static function load(string $value): newBase//public static поменяла местами
    {
        $arValue = explode(':', $value);//массив элементов $value, расположенные между :
//        $a1 = substr($value, strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1);//начинаем поиск с 3 элем
//        $b1 = unserialize($a1, ['allowed_classes' => true]);//возвращаем в исходный вид; не поняла для чего тут $arValue[1], заменила на ['allowed_classes' => true]
//        return (new newBase($arValue[0]))->setValue($b1);
        return (new newBase($arValue[0]))->setValue(unserialize(substr($value, strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1), ['allowed_classes' => true]));//$arValue[1], заменила на ['allowed_classes' => true]
    }
}

class newView extends newBase
{
    private ?string $type = null;//добавила тип свойства
    private int $size = 0;//добавила тип свойства
    private $property = null;
    /**
     * @param mixed $value
     */
    public function setValue(mixed $value): void////добавила тип возвращаемого значения
    {
        parent::setValue($value);//вызываем метод родителя
        $this->setType();
        $this->setSize();

    }
    public function setProperty($value)
    {
        $this->property = $value;
        return $this;//возвращает экземпляр класса
    }
    private function setType(): void//добавила тип возвращаемого значения
    {
        $this->type = gettype($this->value);// проходимся по классам объекта $value
        // от внутреннего к внешнему в поисках "Test3\newBase" выводит класс родителя или "тест"
    }
    private function setSize(): void//добавила тип возвращаемого значения
    {
        if (is_subclass_of($this->value, 'Test3\newView')) {//Проверяет, принадлежит ли объект к потомкам класса'Test3\newView'
            //'Test3\newView' исправила на одинарные кавычки
            $this->size = parent::getSize() + 1 + strlen($this->property);
        } elseif ($this->type === 'test') {//исправила == на ===
            $this->size = parent::getSize();
        } else {
            $this->size = strlen($this->value);
        }
    }
    /**
     * @return string[]
     */
    //исправила тип возвращаемого значения
    public function __sleep()
    {
        return ['property'];
    }
    /**
     * @return string
     */
    public function getNameCl(): string//переименовала метод
    {
        if (empty($this->getName())) {//исправила обращение к приватному свойству, сделала через метод getName
            throw new \RuntimeException("The object doesn\'t have name");
        }
        return '"' . $this->getName()  . '": ';//getName обращение к приватному свойству
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return ' type ' . $this->type  . ';';
    }
    /**
     * @return string
     */
    public function getSize(): string
    {
        return ' size ' . $this->size . ';';
    }
    public function getInfo()
    {
        try {
            echo $this->getNameCl()//переименовала метод
                . $this->getType()
                . $this->getSize()
                . "\r\n";
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
        }
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        if ($this->type === 'test' && !is_string($this->value)) {//добавила проверку, тк выдает ошибку,
            // если $this->value строка, то не возможно применить getSave() метод к строке
            $this->value = $this->value->getSave();
        }
        return parent::getSave() . serialize($this->property);
    }
    /**
     * @return \Test3\newView
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);

        $objekt = new newView($arValue[0]);//изменила название класса на newView
        $objekt->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
        + strlen($arValue[1]) + 1), ['allowed_classes' => true]));
        $objekt->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1 + $arValue[1]), ['allowed_classes' => true]));//добавила параметр в функции unserialize ['allowed_classes' => true]
        //изменила структуру вывода, применяем метод setProperty непосредственно к объекту $objekt
        return $objekt;

//        return (new newView($arValue[0]))
//            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
//                + strlen($arValue[1]) + 1 + $arValue[1]), ['allowed_classes' => true]))
//            ->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
//                + strlen($arValue[1]) + 1 + $arValue[1]), ['allowed_classes' => true]));
    }
}

// проходимся по классам объекта $value от внутреннего к внешнему в поисках "Test3\newBase"
function gettype($value): string
{
    if (is_object($value)) {//объект ли это?
        $type = get_class($value);//имя класса, которому принадлежит объект
        do {
            if (strpos($type, 'Test3\newBase') !== false) {//проверяет содержится ли "Test3\newBase" в строке $type
                //исправила в 'Test3\newBase' кавычки на одинарные
                return 'test';
            }
        } while ($type = get_parent_class($type));//Получает имя родительского класса для объекта или класса,

    } else{//добавила else для того, чтобы не возникало зацикливание рекурсии
        return 'test';
    }
    return gettype($value);
}


$obj = new newBase('12345');
$obj->setValue('text');

$obj2 = new \Test3\newView('O9876');
$obj2->setValue($obj);
$obj2->setProperty('field');
$obj2->getInfo();

$save = $obj2->getSave();

$obj3 = newView::load($save);

var_dump($obj2->getSave() == $obj3->getSave());