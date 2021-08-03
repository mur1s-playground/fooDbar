<?php

namespace Frame;

class Condition {
    const CONDITION_CONST       = 'CONDITION_CONST';
    const CONDITION_CONST_ARRAY = 'CONDITION_CONST_ARRAY';

    const COMPARISON_EQUALS = '=';
    const COMPARISON_NOT_EQUAL = '<>';
    const COMPARISON_GREATER = '>';
    const COMPARISON_GREATER_EQUALS = '>=';
    const COMPARISON_LESS = '<';
    const COMPARISON_LESS_EQUALS = '<=';
    const COMPARISON_LIKE = 'LIKE';
    const COMPARISON_IN = 'IN';
    const COMPARISON_NOT_IN = 'NOT IN';

    private $expr = "";
    private $value_array = "";

    public function __construct($expr, $value_array) {
        $this->expr = $expr;
        $this->value_array = $value_array;
    }

    public function getExpr() {
        return $this->expr;
    }

    public function getValueArray() {
        return $this->value_array;
    }
}
