Bitter Gourd
==========

It is a PHP source code obfuscator, it can make PHP code dirty and hard to read, so far, it is only a development version.
https://github.com/imsheng/bitter-gourd

Features
--------

 * Dependency-free.
 * For any PHP framework.

Quick Start
-----------

    git clone https://github.com/imsheng/bitter-gourd.git
    
    cd bitter-gourd
    
    composer install
    
    php bin/console -h
    
    # test
    php bin/console -p/my_project -t
    # windows: php bin/console -pc:\my_project -t

Functions that can be converted
-----------
array_merge, count, array_push, trim, strlen, mb_strlen, str_shuffle, hex2bin, time

Example
-----------
Before
```php
$str1 = 'abcde';
$str2 = 'fghij';
$str3 = '   fghij   ';
$str4 = '您好！';
$num1 = 1234;
$num2 = 6789;
$arr1 = [1, 2, 3, 4, 5];
$arr2 = ['a', 'b', 'c', 'd', 'e'];
$arr3 = ['a' => 'f', 'b' => 'g', 'c' => 'h', 'd' => 'i', 'e' => 'j'];
$arr4 = ['f' => 'l', 'g' => 'm', 'h' => 'n', 'i' => 'o', 'j' => 'p'];

echo '# if $str1 == $str2' . PHP_EOL;
if ($str1 == $str2) {
    echo '$str1 == $str2' . PHP_EOL;
} else {
    echo '$str1 != $str2' . PHP_EOL;
}
echo PHP_EOL;

echo '# foreach $arr1' . PHP_EOL;
foreach ($arr1 as $item) {
    echo $item . ' ';
}
echo PHP_EOL . PHP_EOL;

echo '# for $arr2' . PHP_EOL;
for ($i = 0; $i < count($arr2); $i++) {
    echo $arr2[$i] . ' ';
}
echo PHP_EOL . PHP_EOL;

echo '# switch $str1' . PHP_EOL;
switch ($str1) {
    case "abcde":
        echo "abcde!";
        break;
    case "fghij":
        echo "fghij!";
        break;
    default:
        echo '??';
}
echo PHP_EOL . PHP_EOL;

echo '# while $arr2' . PHP_EOL;
$i = 0;
while ($i < count($arr2)) {
    echo $arr2[$i] . ' ';
    $i++;
}
echo PHP_EOL . PHP_EOL;

echo '# array_merge $arr1 $arr2' . PHP_EOL;
print_r(array_merge($arr1, $arr2));
echo PHP_EOL . PHP_EOL;

echo '# count $arr1' . PHP_EOL;
echo count($arr1) . PHP_EOL;
echo PHP_EOL;

echo '# array_push $arr1' . PHP_EOL;
$tArr1 = $arr1;
array_push($tArr1, 6, 7, 8, 9);
print_r($tArr1);
echo PHP_EOL . PHP_EOL;

echo '# trim $str3' . PHP_EOL;
echo trim($str3) . PHP_EOL;
echo PHP_EOL;

echo '# time' . PHP_EOL;
echo time() . PHP_EOL;
echo PHP_EOL;
```
After
```php
<?php
${call_user_func(function () {
    return hex2bin('73747231');
})} = call_user_func(function () {
    return hex2bin('6162636465');
});
${call_user_func(function () {
    return hex2bin('73747232');
})} = call_user_func(function () {
    return hex2bin('666768696a');
});
${call_user_func(function () {
    return hex2bin('73747233');
})} = call_user_func(function () {
    return hex2bin('202020666768696a202020');
});
${call_user_func(function () {
    return hex2bin('73747234');
})} = call_user_func(function () {
    return hex2bin('e682a8e5a5bdefbc81');
});
${call_user_func(function () {
    return hex2bin('6e756d31');
})} = 1234;
${call_user_func(function () {
    return hex2bin('6e756d32');
})} = 6789;
${call_user_func(function () {
    return hex2bin('61727231');
})} = [1, 2, 3, 4, 5];
${call_user_func(function () {
    return hex2bin('61727232');
})} = [call_user_func(function () {
    return hex2bin('61');
}), call_user_func(function () {
    return hex2bin('62');
}), call_user_func(function () {
    return hex2bin('63');
}), call_user_func(function () {
    return hex2bin('64');
}), call_user_func(function () {
    return hex2bin('65');
})];
...
```
