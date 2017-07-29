<?php
function prePrintArray($array)
{
    echo '<pre>', PHP_EOL;
    print_r($array);
    echo '</pre>', PHP_EOL;
}

/*
 * Задание #1
 * 1. Дан XML файл. Сохраните его под именем data.xml:
 * 2. Написать скрипт, который выведет всю информацию из этого файла в удобно читаемом виде. Представьте, что результат
 *    вашего скрипта будет распечатан и выдан курьеру для доставки, разберется ли курьер в этой информации?
 */
function task1()
{
    $data = json_decode(json_encode(simplexml_load_file('data.xml')), true);
    printReadable($data);
}

/*
 * Выводит всю информацию из массива в удобно читаемом виде
 */
function printReadable($data, $parent = '')
{
    if (is_array($data)) {
        if (count($data) === 1 && $parent !== '@attributes') {
            echo "$parent:<br>", PHP_EOL;
        }
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                echo "$parent:<br>", PHP_EOL;
                printReadable($value, $parent);
            } else {
                printReadable($value, $key);
            }
        }
    } else {
        echo "$parent: $data<br>", PHP_EOL;
    }
}

/*
 * Задача #2
 * 1. Создайте массив, в котором имеется как минимум 1 уровень вложенности. Преобразуйте его в JSON.  Сохраните как
 *    output.json
 * 2. Откройте файл output.json. Случайным образом решите изменять данные или нет. Сохраните как output2.json
 * 3. Откройте оба файла. Найдите разницу и выведите информацию об отличающихся элементах
 */
function task2()
{
    //1.
    $data = [
        'firstName'    => 'Иван',
        'lastName'     => 'Иванов',
        'address'      => [
            'streetAddress' => 'Московское ш., 101, кв.101',
            'city'          => 'Ленинград',
            'postalCode'    => 101101
        ],
        'phoneNumbers' => [
            'home'   => '812 123-1234',
            'mobile' => '916 123-4567'
        ]
    ];
    $prettyUni = JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE;
    $jsonString = json_encode($data, $prettyUni);
    file_put_contents('output.json', $jsonString);
    echo
    '<h3>JSON:</h3>', PHP_EOL,
    '<pre>', PHP_EOL,
    $jsonString, PHP_EOL,
    '</pre>', PHP_EOL;

    //2.
    $data = json_decode(file_get_contents('output.json'), true);
    randChangeArray($data);
    file_put_contents('output2.json', json_encode($data, $prettyUni));

    //3.
    $data1 = json_decode(file_get_contents('output.json'), true);
    $data2 = json_decode(file_get_contents('output2.json'), true);
    echo '<h3>Массив1:</h3>', PHP_EOL;
    prePrintArray($data1);
    echo '<h3>Массив2:</h3>', PHP_EOL;
    prePrintArray($data2);
    $otherness = [];
    if (compare($data1, $data2, $otherness)) {
        echo '<h3>Отличия:</h3>', PHP_EOL;
        prePrintArray($otherness);
    } else {
        echo 'Изменений нет<br>', PHP_EOL;
    }
}

/*
 * Рекурсивно обходит многомерный массив и с верояностью 20% заменяет элементы на '***'
 */
function randChangeArray(&$array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            randChangeArray($array[$key]);
        } elseif (rand(0, 4) === 0) {
            $array[$key] = '***';
        }
    }
}

/*
 * Рекурсивно обходит массивы $array1, $array2
 * Отличия добавляются в массив $otherness
 * Возвращает 1 если есть отличия, 0 если нет
 */
function compare($array1, $array2, &$otherness)
{
    $result = 0;
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (compare($array1[$key], $array2[$key], $otherness[$key])) {
                $result = 1;
            } else {
                unset($otherness[$key]);
            }
        } elseif ($array1[$key] !== $array2[$key]) {
            $otherness[$key] = $array1[$key] . ' >>> ' . $array2[$key];
            $result = 1;
        }
    }
    return $result;
}

/*
 * Задача #3
 * 1. Программно создайте массив, в котором перечислено не менее 50 случайных числел от 1 до 100
 * 2. Сохраните данные в файл csv
 * 3. Откройте файл csv и посчитайте сумму четных чисел
 */
function task3()
{
    $rows = 5;
    $cols = 10;
    $table = [];
    $i = $rows;
    while ($i--) {
        $tableRow = [];
        $j = $cols;
        while ($j--) {
            $tableRow[] = rand(1, 100);
        }
        $table[] = $tableRow;
        echo implode(', ', $tableRow), '<br>', PHP_EOL;
    }

    //Сохраняем $table в файл csv
    $fileName = 'test.csv';
    $fp = fopen($fileName, 'w');
    foreach ($table as $tableRow) {
        fputcsv($fp, $tableRow);
    }
    fclose($fp);
    echo "Данные сохранены в файле \"$fileName\"<br>", PHP_EOL;

    //Открываем файл csv и считаем сумму четных чисел
    $fp = fopen($fileName, 'r');
    $sum = 0;
    while ($tableRow = fgetcsv($fp)) {
        foreach ($tableRow as $cell) {
            if ($cell % 2 === 0) {
                $sum += $cell;
            }
        }
    }
    echo "Сумма четных чисел: $sum<br>", PHP_EOL;
}

/*
 * Задача #4
 * 1. С помощью CURL запросить данные по адресу:
 *    https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json
 * 2. Вывести title и page_id
 */
function task4()
{
    $u = 'https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $u);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $s = curl_exec($curl);
    curl_close($curl);
    preg_match_all('/"pageid":\d+|"title":".*?"/', $s, $matches);
    foreach ($matches[0] as $item) {
        echo $item, '<br>', PHP_EOL;
    }
}
