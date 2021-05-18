<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class ArraySearch
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $args = $node->args;

        $code = <<<EOF
            <?php
            call_user_func(function (\$v, \$v2, \$v3 = false) {
                foreach (\$v2 as \$k => \$i) {
                    if ((!\$v3 && \$i == \$v) || (\$v3 && \$i === \$v)) {
                        return \$k;
                    }
                }
                return false;
            }, null, null, false);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $newNode = $parser->parse(trim($code));
        $newNode = $newNode[0];

        //$newNode->expr->args[1]->value->items[0]->value = $fArgs;
        $newNode->expr->args[1] = $args[0];
        $newNode->expr->args[2] = $args[1];

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

}