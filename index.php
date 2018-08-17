<?php

$l = '28192';

$d = '52';

function getSum($l, $d)
{
    $pattern = "/^[0-9]+(\.[0-9]+)?$/";
    $isFloat = false;

    if(!preg_match($pattern, $l) || !preg_match($pattern, $d)) {    // проверяем что переданные значения являются числами (в т.ч.
        throw new Exception('Incorrect number format');             // дробные и отрицательные)
    }

    $funcCheckFloat = function($x) use (&$isFloat){                          // Проверка на float, по присутствию точки и
        $patternFloat = "/\.{1}/";                                   // перестройка дальнейшего алгоритма
        if(preg_match($patternFloat, $x)) {
            $isFloat = true;
        }
    };

    $funcCheckFloat($l);
    $funcCheckFloat($d);

    $isChangePrimal = false;                                        // признак что основное число увеличим на 1

    $funcCalculate = function($l,$d, $float = false) use (&$isChangePrimal){   // Высчитываем столбиком ^_^
        $lenL = strlen($l);
        $lenD = strlen($d);
        $bigInt = $d;
        $lowInt = $l;
        if($lenL !== $lenD) {
            $delta = abs($lenL - $lenD);                            // разница по кол-ву цифр
            if($lenL > $lenD) {
                $bigInt = $l;
                $lowInt = $d;
            }
            $lowInt = $float ?                                      // меньшему числу добавляем нули
                $lowInt . str_repeat('0',$delta)  :
                str_repeat('0',$delta) . $lowInt;
        }

        $res = '';
        $isRem = $isChangePrimal ? true : false;

        for($i = strlen($bigInt)-1; $i >= 0; $i--) {                // циклом пробегаемся по массиву цифр
            $sum = (int)$bigInt[$i] + (int)$lowInt[$i];
            if($isRem) {
                $sum++;
            }
            if($sum >= 10) {                // режем 'переднюю' цифру если число больше 10
                if($float || $i !== 0) {  // последнюю цифру основного числа не отрезаем
                    $sum = substr(strval($sum), 1);
                }
                $isRem = true;
            } else {
                $isRem = false;
            }
            if($i === 0 && $isRem && $float) {
                $isChangePrimal = true;
            }
            $res = $sum . $res;

        }
        return $res;
    };

    if($isFloat) {
        $l = explode('.',$l);
        $d = explode('.',$d);
        $l[1] = empty($l[1]) ? '0' : $l[1];
        $d[1] = empty($d[1]) ? '0' : $d[1];
        $res2 = $funcCalculate($l[1],$d[1], true);  // считаем справа от запятой
        $res1 = $funcCalculate($l[0],$d[0]);    // считаем часть слева от запятой

        $res = $res1 . '.' . $res2;
    } else {
        $res = $funcCalculate($l,$d);
    }

    $res = preg_replace("/^0*/",'', $res);  // убираем лишние нули, если такие есть

    return $res;
}

    echo '<div style="text-align: right; width: 600px;">';
    echo $l;echo '<br>';
    echo $d;echo '<br>';

    try {
        echo getSum($l,$d);
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    echo '</div>';

