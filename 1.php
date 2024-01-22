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

//функция нахождения простых чисел в массиве 
function findSimple(int $a, int $b) : array
{
    if (($a > 0) && ($b > $a)) {
        $arr = [];
        for ($i = $a; $i <= $b; $i++){
            $cnt = 0;
            for ($j = 1; $j <= $i; $j++) {
                if ($i % $j === 0) {
                    $cnt++;
                }
            }
            if ($cnt === 2) {
                $arr[] = $i;
            }
        }
        return $arr;
    } else {
        throw new \RuntimeException('a и b положительные числа, причем a > b');
    }

}

try {
    echo "<h2>Результаты выполнения первого задания</h2>";
    echo "<h4>Номер 1</h4>";
    $a = -1;
    $b = 7;
    echo "<p>Входные данные<br>a = $a, b = $b<br>";
    echo "Массив простых чисел<br>";
    print_r(findSimple($a, $b));
} catch (Exception $exception) {
    echo $exception->getMessage();
}

//функция для преобразования массива чисел в ассоциативный массив
function createTrapeze(array $a): array
{
    if (!empty($a) && count($a) % 3 === 0 && min($a) > 0) {
        $arrTry = array_chunk($a, 3);
        $arrAbc = array();
        foreach ($arrTry as $v) {
            $arrAbc[] = array_combine(array('a', 'b', 'c'), $v);
        }
        return $arrAbc;
    }else {
        throw new \RuntimeException('массив $a должен быть заполнен положительными элементами, кол-во которых кратно 3');
    }
}
try {
    echo "<h4>Номер 2</h4>";
    echo "<p>Входные данные<br>";
    $a = [1, 2, 3, 4, 5, 6, 4, 8, 2];
    print_r($a);
    echo "<br>Ассоциативный массив<br>";
    print_r(createTrapeze($a));
    echo "</p>";
} catch (Exception $exception) {
    echo $exception->getMessage();
    return;
}


//функция подсчета площади трапеции
function squareTrapeze(array &$a): array
{
    foreach ($a as &$values) {
    if (!isset($values['a'], $values['b'], $values['c'])) {
        throw new \RuntimeException('массив $a должен быть ассоциативным');
    }
    $values['s'] = ($values['a'] + $values['b']) * $values['c'] * 0.5;
    }
    return $a;
}
try {
    echo "<h4>Номер 3</h4>";
    echo "<p>Входные данные<br>";
    $arrAbcs = createTrapeze($a);
    print_r($arrAbcs);
    echo "<br>Значения площади трапеций<br>";
    squareTrapeze($arrAbcs);
    print_r($arrAbcs);
    echo "</p>";
} catch (Exception $exception) {
    echo $exception->getMessage();
    return;
}

//print_r(squareTrapeze($arrAbcs));

//$a = [
//    ['a' => 1, 'b' => 3, 'c' => 5],
//    ['a' => 4, 'b' => 6, 'c' => 8],
//    ['a' => 6, 'b' => 4, 'c' => 2],s
//];
//squareTrapeze($a);
//echo $a[0]['s'];


//функция, выводящая максимальную площадь, которая <= заданной
function getSizeForLimit(array $a, float $b)
{
    $maxS = 0;
    $arrMaxS = [];
    foreach ($a as $elem) {
        if (isset($elem['s'])) {
            if ($elem['s'] <= $b && $elem['s'] > $maxS) {
                $maxS = $elem['s'];
                $arrMaxS = $elem;
            }
        } else {
            throw new \RuntimeException('массив $a должен быть многомерным ассоциативным');
        }
    }
    return $arrMaxS;
}
try {
    $size = 20;
    echo "<h4>Номер 4</h4>";
    echo "<p>Входные данные<br>площадь трапеции <= $size <br>";
    $test = [1, 2, 3];
    //print_r($test);
    print_r($arrAbcs);
    echo "<br>Максимальная площадь трапеции<br>";
    print_r(getSizeForLimit($arrAbcs , $size));
    echo "</p>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}

//функция нахождения минимального элемента массива
function getMin(array $a)
{
    $currMin = max($a);
    for ($i = 0; $i < count($a)-1; $i++) {
        if ($a[$i] < $a[$i+1]) {
            if ($a[$i] < $currMin) {
                $currMin = $a[$i];
            }
        } elseif ($a[$i+1] < $currMin) {
            $currMin = $a[$i+1];
        }
    }
    return $currMin;
}
echo "<p><h4>Номер 5</h4></p>";
echo "<p>Входные данные<br>";
print_r($a);
echo "<br>Минимальный элемент массива<br>";
print_r(getMin($a));
echo "</p>";


/*функция построения таблицы на основе массива с размерами и площадью трапеций
строки с нечетным значением площади выделены
*/
function printTrapeze(array $a): void
{
?>
<table border = "1">
    <tr>
        <th>a</th>
        <th>b</th>
        <th>c</th>
        <th>s</th>
    </tr>
    <?php
    for ($i = 0, $iMax = count($a); $i < $iMax; $i++) {
        ?>
        <tr>
        <?php
        foreach ($a[$i] as $v) {
            if (($a[$i]['s'] > (int)$a[$i]['s']) || ( $a[$i]['s'] % 2 !== 0)) {
                echo '<td style = "background-color: #0000ff">' . $v . '</td>';
            } else {
                echo '<td>' . $v . '</td>';
            }
        }
        ?>
        </tr>
        <?php
    }
    ?>
</table>

<?php
}
?>
<?php
try {
    echo "<h4>Номер 6</h4>";
    echo "<p>Входные данные<br>";
    print_r(squareTrapeze($arrAbcs));
    $arrAbcs2 = squareTrapeze($arrAbcs);
    echo "<br>Таблица площадей трапеций, строки с нечетным значением площади выделены синим цветом<br>";
    $test = printTrapeze($arrAbcs2);
    echo $test . "</p>";
} catch (Exception $exception) {
    echo $exception->getMessage();
}
?>


<?php
/*абстрактный класс, высчитывающий значения по формулам
a*(b^c)
(a/b)^c
*/
abstract class BaseMath {
    protected function exp1($a, $b, $c) : int {
        return $a * ($b ** $c);
    }

    protected function exp2($a, $b, $c) : float {
        return ($a / $b) ** $c;
    }

    abstract protected function getValue();
}

/*класс наследник класса BaseMath, реализующий расчет по формуле
 f=(a*(b^c)+(((a/c)^b)%3)^min(a,b,c))*/
class F1 extends BaseMath {
    private int $a;
    private int $b;
    private int $c;

    public function __construct(int $a, int $b, int $c) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function getValue() {
        try{
            if ($this->a < $this->c) {
                throw new \RuntimeException ('значение $a должно быть больше $c');
            }
            return $this->exp1($this->a, $this->b, $this->c) + (($this->exp2($this->a, $this->c, $this->b)) % 3) ** min($this->a, $this->b, $this->c);
        } catch (Exception $e) {
            return $e->getMessage();
        }     
    }

}
echo "<p><h4>Задание №7</h4></p>";
echo "<p>Входные данные<br>";
$test = new F1(4, 3, 2);
print_r($test);
echo "<br>Результат расчета по формуле f=(a*(b^c)+(((a/c)^b)%3)^min(a,b,c))<br>";
print $test->getValue();
echo "</p>";
?>



</body>
</html>