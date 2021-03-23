<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class Range
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $args = $node->args;

        if (count($args) != 2) {
            return $node;
        }

        $code = <<<EOF
        <?php
        call_user_func(function (\$v, \$v2, \$v3 = 1) {
            \$r = [];
            \$k = 0;
            for (\$i = \$v; \$i <= \$v2; \$i++) {
                \$r[] = \$k;
                \$k = \$k + \$v3;
                if (\$k > \$v2) {
                    break;
                }
            }
            return \$r;
        }, null,null,null);
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