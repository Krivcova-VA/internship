<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Массивы </title>
</head>
<body>

<?php
//Функция для многобайтовых строк, которая переворачивает строку задом наперёд
function mb_strrev($str){
    $r = '';
    for ($i = mb_strlen($str); $i>=0; $i--) {
        $r .= mb_substr($str, $i, 1);
    }
    return $r;
}

//Функция возвращает строку с инвертированной подстрокой(2 вхождение)
function convertString(string $a, string $b) : string
{
    if (empty($b) || (strlen($a) < strlen($b))) {
        throw new \RuntimeException('$b должна быть не пустой, причем длина $a должна быть больше $b');
    }
    if (substr_count($a, $b) < 2) {
        return $a;
    }
    $indexOne = strpos($a, $b) + strlen($b);
    $indexTwo = strpos($a, $b, $indexOne);
    $replace = mb_strrev($b);
    return substr_replace($a, $replace, $indexTwo, strlen($b));
}

try {
    $a = 'один один два';
    $b = 'один';
    echo "<p>Функция 1<br>Входные данные<br>";
    echo "Строка: " . $a . "<br>" . "Подстрока: " . $b . "<br>";
    echo "Ответ: <br>";
    print_r(convertString($a, $b));
    echo "<p/>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}


//Функция возвращает двумерный массив $a отсортированный по возрастанию значений для ключа $b
function mySortForKey(array $a, string $b): array
{
    if (empty($b)) {
        throw new \RuntimeException('значение ключа $b не может быть пустым(null)');
    }
    for ($i = 0; $i < count($a); $i++) {
        $arrayKey = array_keys($a[$i]);
        if (!(in_array($b, $arrayKey, true))) {
            throw new \RuntimeException("в подмассиве под индексом $i отсутствует ключ $b");
        }
    }

    $arrayVal = array_column($a, $b);
    $arrayValCopy = $arrayVal;
    //сортировка $a с одинаковыми значениями $b
    if(count(array_unique($arrayValCopy)) < count($arrayVal)) {
        for ($i = 0; $i < count($a); $i++) {
            $arrayKey = array_keys($a[$i]);
            //формируем строку из ключей, это позволит расставить подмассивы в алфавитном порядке
            $str = '';
            foreach ($arrayKey as $vstr) {
                $str .= $vstr;
            }
            $a[$i]['code'] = $str;
        }
        $arrZnach = array_column($a, 'code');
        $arrayVal = array_column($a, $b);
        array_multisort($arrayVal, SORT_ASC, $arrZnach, SORT_ASC, SORT_STRING, $a);

        for ($i = 0; $i < count($a); $i++) {
            $cnt = count($a[$i]);
            for ($j = 0; $j <= $cnt - 1; $j++) {
                unset($a[$i]['code']);
            }
        }
        return $a;
    }
    
    array_multisort($arrayVal, SORT_ASC, $a);
    return $a;
}

try {
    $arr = [['a' => 1, 'c' => 123], ['c' => 456], ['b' => 3, 'c' => 123]];
    $key = 'c';
    echo "<p>Функция 2<br>Входные данные<br>";
    echo "a = ";
    print_r($arr);
    echo "<br>" . " b = ";
    print_r($key );
    echo "<br>Ответ: <br>";
    print_r(mySortForKey($arr, $key));
    echo "<p/>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}
?>



<?php
//Импорт в бд xml файла с входящей кодировкой windows-1251
function importXml($a)
{
    //чтение xml файла и перекодировка в utf-8
    $readXml = file_get_contents($a);
    $readXml = mb_convert_encoding($readXml, "utf-8", "windows-1251");
    $readXml = str_replace("windows-1251", "utf-8", $readXml);
    $file = "2.xml";
    file_put_contents($file, $readXml);

//Проверка файла на синтаксис
    $xml = XMLReader::open('2.xml');
    $xml->setParserProperty(XMLReader::VALIDATE, true);

//Проверка файла на соответствие схеме
    function libxml_display_error($error): string
    {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    function libxml_display_errors(): void
    {
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            print libxml_display_error($error);
        }
        libxml_clear_errors();
    }


    libxml_use_internal_errors(true);

    $xml = new DOMDocument();
    $xml->load('2.xml');

    if (!$xml->schemaValidate('shema_xsd.xsd')) {
        print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
        libxml_display_errors();

    }

    //Парсер xml файла
    $domxmal = new DOMDocument();
    $domxmal->load("2.xml", LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NOENT);
    $root = $domxmal->documentElement;
    $prise = [];
    $product = [];
    $category = [];
    $property = [];
    $val_property = [];


    function readLevel($el, $level = 0)
    {
        global $prise, $product, $category, $property, $val_property;
        if ($el->childNodes) {
            foreach ($el->childNodes as $sub) {

                if ($sub->nodeType == XML_ELEMENT_NODE) {//если этот узел является узлом дерева
                    switch ($sub->nodeName) {
                        case "Товар":
                            $snew = [];
                            $snew[0] = $sub->getAttribute("Код");
                            $snew[1] = $sub->getAttribute("Название");
                            $product[] = $snew;
                            break;

                        case "Цена":
                            $snew = [];
                            $snew[0] = $sub->parentNode->getAttribute('Код');
                            //print_r($sub->getAttributeNode());
                            $snew[1] = $sub->getAttribute("Тип");
                            $snew[2] = $sub->nodeValue;
                            $prise[] = $snew;
                            break;

                        case "Разделы":
                            $snew1 = [];
                            $snew2 = [];
                            $snew1[0] = $sub->parentNode->getAttribute('Код');
                            $snew1[1] = $sub->firstChild->nodeValue;
                            $category[] = $snew1;
                            if ($sub->lastChild->nodeValue !== $sub->firstChild->nodeValue) {
                                $snew2[0] = $sub->parentNode->getAttribute('Код');
                                $snew2[1] = $sub->lastChild->nodeValue;
                                $category[] = $snew2;
                            }
                            break;

                        case "Свойства":
                            $snew = [];
                            $pnew = [];
                            $nodelist = $sub->childNodes;
                            for ($i = 0; $i < $nodelist->length; $i++) {
                                $child = $nodelist->item($i);
                                $snew[0] = $child->nodeName;
                                $pnew[0] = $sub->parentNode->getAttribute('Код');
                                $pnew[1] = $child->nodeName;
                                $pnew[2] = $child->nodeValue;

                                if (empty($child->getAttributeNames())) {
                                    $snew[1] = [];
                                } else {
                                    $arrAttr = $child->getAttributeNames();
                                    $snew[1] = [$arrAttr[0] => $child->getAttribute($arrAttr[0])];
                                }
                                $property[] = $snew;
                                $val_property[] = $pnew;

                            }
                    }
                    readLevel($sub, ($level + 1));
                } else if ($sub->nodeType == XML_ELEMENT_NODE) {
                    echo "этот узел не является узлом дерева";
                }
            }
        }
    }

    readLevel($root, 1);


    define("servername", 'internship');
    define("username", "root"); // имя пользователя
    define("password", ""); // пароль если существует
    define("dbname", "test_samson"); // база данных

// Подключение к бд
    $conn = new mysqli(servername, username, password, dbname);
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    //Загрузка данных в таблицы
    global $product, $prise, $category, $property, $val_property;
    //$total = null;

    $res_product = $conn->query('SELECT COUNT(*) FROM `a_product`');
    $row = mysqli_fetch_row($res_product);
    $total = $row[0];
    if (is_array($product) && !empty($product) && empty($total)) {
        foreach ($product as $el) {
            $val1 = mysqli_real_escape_string($conn, $el[0]);
            $val2 = mysqli_real_escape_string($conn, $el[1]);
            $sql = "INSERT INTO a_product (code, name) VALUES ( '" . $val1 . "','" . $val2 . "')";
            mysqli_query($conn, $sql);
        }
        $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_product`');
        $row = mysqli_fetch_row($res_value_property);
        if ($row[0]) {
            echo "<br>Успешно заполнена таблица a_product" . "<br>";
        } else {
            throw new \RuntimeException("Ошибка: " . $sql . "<br>" . $conn->error);
        }
    } else {
        throw new \RuntimeException("<br>".'$product должен быть не пустым массивом, а таблица a_product - пустой'."<br>");
    }

    $total = null;
    $res_prise = $conn->query('SELECT COUNT(*) FROM `a_price`');
    $row = mysqli_fetch_row($res_prise);
    $total = $row[0];
    if (is_array($prise) && !empty($prise) && empty($total)) {
        foreach ($prise as $el) {
            $val1 = mysqli_real_escape_string($conn, $el[0]);
            $val2 = mysqli_real_escape_string($conn, $el[1]);
            $val3 = mysqli_real_escape_string($conn, $el[2]);
            $sql = "INSERT INTO a_price (code, type, price) VALUES ( '" . $val1 . "','" . $val2 . "','" . $val3 . "')";
            mysqli_query($conn, $sql);
        }
        $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_price`');
        $row = mysqli_fetch_row($res_value_property);
        if ($row[0]) {
            echo "Успешно заполнена таблица a_price" . "<br>";
        } else {
            throw new \RuntimeException("Ошибка: " . $sql . "<br>" . $conn->error);
        }
    } else {
        throw new \RuntimeException('$prise должен быть не пустым массивом, а таблица a_price - пустой');
    }
    $total = null;
    $res_category = $conn->query('SELECT COUNT(*) FROM `a_category`');
    $row = mysqli_fetch_row($res_category);
    $total = $row[0];
    if (!empty($category) && empty($total)) {
        $ar = [];
        $code_r = 0;
        foreach ($category as $el) {
            if (!in_array($el[1], $ar)) {
                $ar[] = $el[1];
                $code_r = array_search($el[1], $ar) + 1;
            } else {
                $code_r = array_search($el[1], $ar) + 1;
            }
            $val1 = mysqli_real_escape_string($conn, $el[0]);
            $val2 = mysqli_real_escape_string($conn, $el[1]);
            $sql = "INSERT INTO a_category (code, code_r, name_r) VALUES ( '" . $val1 . "','" . $code_r . "','" . $val2 . "')";
            mysqli_query($conn, $sql);
        }
        $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_category`');
        $row = mysqli_fetch_row($res_value_property);
        if ($row[0]) {
            echo "Успешно заполнена таблица a_category" . "<br>";
        } else {
            throw new \RuntimeException("Ошибка: " . $sql . "<br>" . $conn->erro);
        }
    } else {
        throw new \RuntimeException('$category должен быть не пустым массивом, а таблица a_category - пустой');
    }

    $arr = [];
    foreach ($property as $elem) {
        if (!in_array($elem[0], $arr, true)) {
            $newProperty[] = [$elem[0], $elem[1]];
            $arr[] = $elem[0];

        }
    }
    $total = null;
    $res_property = $conn->query('SELECT COUNT(*) FROM `a_property`');
    $row = mysqli_fetch_row($res_property);
    $total = $row[0];
    if (is_array($newProperty) && !empty($newProperty) && empty($total)) {
        foreach ($newProperty as $el) {
            $val1 = mysqli_real_escape_string($conn, $el[0]);
            if (!empty($el[1])) {
                foreach ($el[1] as $key => $sn) {
                    $a = $key;
                    $b = $sn;
                    $sql = "INSERT INTO a_property (property, type, value_type) VALUES ('" . $val1 . "','" . $a . "','" . $b . "')";
                }
            } else {
                $sql = "INSERT INTO a_property ( property) VALUES ('" . $val1 . "')";
            }
            mysqli_query($conn, $sql);
        }
        $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_property`');
        $row = mysqli_fetch_row($res_value_property);
        if ($row[0]) {
            echo "Успешно заполнена таблица a_property" . "<br>";
        } else {
            throw new \RuntimeException("Ошибка: " . $sql . "<br>" . $conn->error);
        }
    } else {
        throw new \RuntimeException('$newProperty должен быть не пустым массивом, а таблица a_property - пустой');
    }

    $result = $conn->query('SELECT * FROM `a_property`'); // запрос на выборку
    while ($row = $result->fetch_assoc())// получаем все строки в цикле по одной
    {
        for ($i = 0; $i < count($val_property); $i++) {
            if ($row['property'] === $val_property[$i][1]) {
                $val_property[$i][1] = $row['id'];
            }
        }

    }

    $total = null;
    $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_value_property`');
    $row = mysqli_fetch_row($res_value_property);
    $total = $row[0];
    if (is_array($val_property) && !empty($val_property) && empty($total)) {
        foreach ($val_property as $el) {
            $val1 = mysqli_real_escape_string($conn, $el[0]);
            $val2 = mysqli_real_escape_string($conn, $el[1]);
            $val3 = mysqli_real_escape_string($conn, $el[2]);
            $sql = "INSERT INTO a_value_property (code, id_p, value) VALUES ( '" . $val1 . "','" . $val2 . "','" . $val3 . "')";
            mysqli_query($conn, $sql);
        }
        $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_value_property`');
        $row = mysqli_fetch_row($res_value_property);
        if ($row[0]) {
            echo "Успешно заполнена таблица a_value_property" . "<br>";
        } else {
            throw new \RuntimeException("Ошибка: " . $sql . "<br>" . $conn->error);
        }
    } else {
        throw new \RuntimeException('$prop должен быть не пустым массивом, а таблица a_value_property - пустой');
    }
    $conn->close();
    return "ok";
}


try {
    echo "<p>Функция импорта<br>";
    $a = "2_1.xml";
    echo "xml файл: " . $a ."<br>";
    print_r(importXml($a));
    echo "<p/>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}
?>



<?php
//Экспорт данных из бд в xml файл
function exportXml($a, int $b): void
{
    //Подключение к бд
//    define("servername", 'internship');
//    define("username", "root"); // имя пользователя
//    define("password", ""); // пароль если существует
//    define("dbname", "test_samson"); // база данных

    $conn = new mysqli(servername, username, password, dbname);
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    //Выгрузка данных из таблиц
    $product = $conn->query('SELECT * FROM `a_product`');
    $price = $conn->query('SELECT * FROM `a_price`');
    $category = $conn->query('SELECT * FROM `a_category`');
    $property = $conn->query('SELECT * FROM `a_property`');
    $value_property = $conn->query('SELECT * FROM `a_value_property`');

//Данные, полученные из таблиц, раскладываем по массивам
    $arr_product = [];
    while ($row = $product->fetch_assoc()) {
        $arr_product[] = [$row['code'], $row['name']];
    }
    if (empty($arr_product)) {echo 'warning: массив $arr_product пустой';}

    $arr_price = [];
    while ($row = $price->fetch_assoc()) {
        $arr_price[] = [$row['code'], $row['type'], $row['price']];
    }
    if (empty($arr_price)) {echo 'warning: массив $arr_price пустой';}

    $arr_category = [];
    while ($row = $category->fetch_assoc()) {
        $arr_category[] = [$row['code'], $row['code_r'], $row['name_r']];
    }
    if (empty($arr_category)) {echo 'warning: массив $arr_category пустой';}

    $arr_property = [];
    while ($row = $property->fetch_assoc()) {
        $arr_property[] = [$row['id'], $row['property'], $row['type'], $row['value_type']];
    }
    if (empty($arr_property)) {echo 'warning: массив $arr_property пустой';}

    $arr_value_property = [];
    while ($row = $value_property->fetch_assoc()) {
        for ($i = 0; $i < count($arr_property); $i++) {
            if ($row['id_p'] === $arr_property[$i][0]) {
                $arr_value_property[] = [$row['code'], $arr_property[$i][1], $arr_property[$i][2], $arr_property[$i][3], $row['value']];
            }
        }
    }
    if (empty($arr_value_property)) {echo 'warning: массив $arr_value_property пустой';}

    foreach ($arr_category as $elem) {
        if ($b == $elem[1]) {
            $resalt[] = $elem[0];
        }
    }

//Создание xml файла
    $dom = new domDocument("1.0", "utf-8");
    $root = $dom->createElement('Товары');
    $dom->appendChild($root);
    foreach ($arr_product as $elprod) {
        if (empty($b) || in_array($elprod[0], $resalt)) {
            $x_prod = $dom->createElement('Товар');
            $x_prod->setAttribute('Код', "$elprod[0]");
            $x_prod->setAttribute('Название', "$elprod[1]");


            foreach ($arr_price as $elprice) {
                if ($elprice[0] == $elprod[0]) {
                    $x_pris = $dom->createElement('Цена', "$elprice[2]");
                    $x_pris->setAttribute('Тип', "$elprice[1]");
                    $x_prod->appendChild($x_pris);
                }

            }
            $x_property = $dom->createElement('Свойства');
            foreach ($arr_value_property as $elproperty) {
                if ($elproperty[0] == $elprod[0]) {
                    $x_property_el = $dom->createElement("$elproperty[1]", "$elproperty[4]");
                    if ($elproperty[2]) {
                        $x_property_el->setAttribute("$elproperty[2]", "$elproperty[3]");
                    }
                    $x_property->appendChild($x_property_el);
                }
            }
            $x_prod->appendChild($x_property);

            $x_category = $dom->createElement('Разделы');
            foreach ($arr_category as $elcat) {
                if ($elprod[0] == $elcat[0]) {
                    $x_category_el = $dom->createElement('Раздел', "$elcat[2]");
                    $x_category->appendChild($x_category_el);
                }
            }
            $x_prod->appendChild($x_category);
            $root->appendChild($x_prod); //товар добавить в корневой узел
        }
    }
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->save($a);
    echo "успешно создан новый файл $a";
}

try {
    echo "<p>Функция экспорта<br>";
    $a = "new_1_2.xml";
    $b = 1;
    echo "файл для выгрузки данных: " . $a . "<br>" . "код категории" . $b . "<br>";
    print_r(exportXml($a, $b));
    echo "<p/>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}
?>


</body>
</html>
