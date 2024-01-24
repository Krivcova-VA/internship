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

//Функция возвращает строку с инвертированной подстрокой(2 вхождение)
function convertString(string $a, string $b) : string
{
    $pattern = '/^[a-z0-9]+$/i';
    if (preg_match($pattern, $a)) {
        if ((strlen($a) > strlen($b)) && !empty($b)) {
            if (substr_count($a, $b) >= 2) {
                $indexOne = strpos($a, $b) + strlen($b);
                $indexTwo = strpos($a, $b, $indexOne);
                $replace = strrev($b);
                return substr_replace($a, $replace, $indexTwo, strlen($b));
            } else {
                throw new \RuntimeException('$b не входит в $a или входит, но только 1 раз');
            }
        } else {
            throw new \RuntimeException('$a и $b должны быть не пустые, причем длина $a должна быть больше $b');
        }
    } else {
        throw new \RuntimeException('строка $a может содержать только латинские буквы и цифры');
    }
}

try {
    $a = 'qwertyasdqwe';
    $b = 'qwe';
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
    if (!empty($b)) {
        for ($i = 0; $i < count($a); $i++) {
            $arrayKey = array_keys($a[$i]);
            if (!(in_array($b, $arrayKey, true))) {
                throw new \RuntimeException("в подмассиве под индексом $i отсутствует ключ $b");
            }
        }
        if ($b == $arrayKey[0]) {
            $a1 = array_column($a, $arrayKey[0]);
            array_multisort($a1, SORT_ASC, $a);
            //print_r($a1);
        } else {
            $b1 = array_column($a, $arrayKey[1]);
            array_multisort($b1, SORT_ASC, $a);

        }
        return $a;
    } else {
        throw new \RuntimeException('значение ключа $b не может быть пустым(null)');
    }
}

try {
    $arr = [['a'=>2,'b'=>1],['a'=>1,'b'=>3]];
    $key = 'a';
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
    $tmpp = null;
    $curTag = "";
    $curCode = 0;
    $curProp = "";
    $product = [];
    $prise = [];
    $category = [];
    $property = [];
    $prop = [];
    $sql = "";
    $nameProd = '';
    $attrPrise = "";
    $newProperty = [];

//Функция обработки открывающего тега
    function startElement($p, $elname, $attr): void
    {
        global $level, $tmpp, $curTag, $curCode, $product, $property, $curProp, $nameProd, $attrPrise;
        $level++;
        $curTag = $elname;

        if ($curTag === "Товар") {
            $curCode = (int)$attr["Код"];
            $nameProd = $attr["Название"];
            $product[] = [$curCode, $nameProd];

        }
        if ($curTag === "Цена") {
            $attrPrise = $attr["Тип"];
            $tmpp = array();
        }
        if ($level === 4 && $curTag !== "Раздел") {
            $curProp = $curTag;
            $property[] = [$curTag, $attr];

        }

    }

// Функция обработки закрывающего тега
    function endElement($p, $elname): void
    {
        global $level;
        $level--;
    }

    //Функция обработки данных, находящихся внутри тегов
    function dataHandler($p, $dat): void
    {
        global $curTag, $category, $curCode, $prop, $curProp, $attrPrise, $prise;
        if (trim($dat) !== "") {
            //echo $dat."<br>";
            if ($curTag === "Цена" && !empty($dat)) {
                $prise[] = [$curCode, $attrPrise, $dat];
            }
            if ($curTag === "Раздел") {
                $category[] = [$curCode, $dat];
            }
            if ($curTag === $curProp) {
                $prop[] = [$curCode, $curTag, $dat];
            }
        }
    }

    $level = 0;
    $prise = array();
    $prods = array();
    $property = array();


    $p = xml_parser_create("UTF-8");
    xml_set_element_handler($p, "startElement", "endElement");
    xml_set_character_data_handler($p, "dataHandler");

    if (!($fp = fopen("2.xml", "r"))) {
        echo "File open error";
        exit();
    }
    while ($d = fread($fp, 4069)) {
        if (!xml_parse($p, $d, feof($fp))) {
            echo "error xml parsing" . "<br>";
        }
    }
    xml_parser_free($p);


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
    global $product, $prise, $category, $property, $prop;
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
            if (!in_array($el[1], $ar, true)) {
                $ar[] = $el[1];
                $code_r = array_search($el[1], $ar, true) + 1;
            } else {
                $code_r = array_search($el[1], $ar, true) + 1;
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
        for ($i = 0; $i < count($prop); $i++) {
            if ($row['property'] === $prop[$i][1]) {
                $prop[$i][1] = $row['id'];
            }
        }

    }

    $total = null;
    $res_value_property = $conn->query('SELECT COUNT(*) FROM `a_value_property`');
    $row = mysqli_fetch_row($res_value_property);
    $total = $row[0];
    if (is_array($prop) && !empty($prop) && empty($total)) {
        foreach ($prop as $el) {
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
    $xw = xmlwriter_open_memory();
    xmlwriter_set_indent($xw, 1);
    $res = xmlwriter_set_indent_string($xw, ' ');
    xmlwriter_start_document($xw, '1.0', 'UTF-8');

    xmlwriter_start_element($xw, 'Товары');
    foreach ($arr_product as $elprod) {
        if(empty($b) || in_array($elprod[0], $resalt, true)) {
            xmlwriter_start_element($xw, 'Товар');
            xmlwriter_start_attribute($xw, 'Код');
            xmlwriter_text($xw, (string)$elprod[0]);
            xmlwriter_start_attribute($xw, 'Название');
            xmlwriter_text($xw, "$elprod[1]");
            xmlwriter_end_attribute($xw);

            foreach ($arr_price as $elprice) {
                if ($elprice[0] == $elprod[0]) {
                    xmlwriter_start_element($xw, 'Цена');
                    xmlwriter_start_attribute($xw, 'Тип');
                    xmlwriter_text($xw, (string)$elprice[1]);
                    xmlwriter_end_attribute($xw);
                    xmlwriter_text($xw, (string)$elprice[2]);
                    xmlwriter_end_element($xw);
                }
            }
            xmlwriter_start_element($xw, 'Свойства');
            foreach ($arr_value_property as $elproperty) {
                if ($elproperty[0] == $elprod[0]) {
                    xmlwriter_start_element($xw, (string)$elproperty[1]);
                    if ($elproperty[2]) {
                        xmlwriter_start_attribute($xw, (string)$elproperty[2]);
                        xmlwriter_text($xw, (string)$elproperty[3]);
                        xmlwriter_end_attribute($xw);
                    }
                    xmlwriter_text($xw, (string)$elproperty[4]);
                    xmlwriter_end_element($xw);
                }
            }
            xmlwriter_end_element($xw);
            xmlwriter_start_element($xw, 'Разделы');
            foreach ($arr_category as $elcat) {
                if ($elprod[0] == $elcat[0]) {
                    xmlwriter_start_element($xw, 'Раздел');
                    xmlwriter_text($xw, (string)$elcat[2]);
                    xmlwriter_end_element($xw);
                }
            }
            xmlwriter_end_element($xw);
            xmlwriter_end_element($xw);//Закрывающий тег товар
        }
    }
    xmlwriter_end_element($xw);//Закрывающий тег товары

    $fileXml = xmlwriter_output_memory($xw);
    $file = $a;
    file_put_contents($file, $fileXml);
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