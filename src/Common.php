<?php


namespace BitterGourd;

use PhpParser\ParserFactory;
use PhpParser\Node;

class Common
{

    /**
     * @param $string
     * @return Node|null
     */
    public static function stringNToFuncN($string)
    {
        if (mb_strlen($string) <= 1) {
            return new Node\Scalar\String_($string);
        }

        $binString = str_rot13($string);
        $code = <<<EOF
            <?php
            str_rot13('$binString');
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));
        /** @var Node\Stmt\Expression $newNode */

        $node = $ast[0];

        return $node->expr;

    }

    static public function generateVarName()
    {
        return sprintf('v%s', uniqid());
    }

    /**
     * @param $str
     * @return string
     * Thinks for https://github.com/pk-fr/yakpro-po
     */
    public static function obfuscateString($str)
    {
        $l = strlen($str);
        $result = '';
        for ($i = 0; $i < $l; ++$i) {
            $result .= mt_rand(0, 1) ? "\x" . dechex(ord($str[$i])) : "\\" . decoct(ord($str[$i]));
        }
        return $result;
    }

}
