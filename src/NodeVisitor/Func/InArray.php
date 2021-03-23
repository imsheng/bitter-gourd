<?php

namespace BitterGourd\NodeVisitor\Func;

use PhpParser\Node;
use PhpParser\ParserFactory;

class InArray
{

    function __invoke(Node\Expr\FuncCall $node)
    {
        $args = $node->args;

        if (count($args) != 2) {
            return $node;
        }

        $code = <<<EOF
        <?php
        call_user_func(function (\$v, \$v2, \$v3 = false) {
            foreach (\$v2 as \$i) {
                if ((\$v3 == true && \$i === \$v) || (\$v3 == false && \$i == \$v)) {
                    return true;
                }
            }
            return false;
        }, null,null);
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