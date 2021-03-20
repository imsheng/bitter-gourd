<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class ArrayColumn
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $args = $node->args;

        if (count($args) != 2) {
            return $node;
        }

        $code = <<<EOF
        <?php
        call_user_func(function (\$v, \$v2) {
            \$r = [];
            foreach (\$v as \$i) {
                if (isset(\$i[\$v2])) {
                    \$r[] = \$i[\$v2];
                }
            }
            return \$r;
        }, null, null);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $newNode = $parser->parse(trim($code));
        $newNode = $newNode[0];

        //$newNode->expr->args[1]->value->items[0]->value = $fArgs;
        $newNode->expr->args = $args;

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

}