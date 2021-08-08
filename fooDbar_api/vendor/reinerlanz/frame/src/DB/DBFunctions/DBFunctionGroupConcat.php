<?php


namespace Frame;


class DBFunctionGroupConcat {
    protected $args_expr = null;
    protected $argc = 1;

    public function getDescription() {
        return array(
            "Field"     => "GROUP_CONCAT",
            "Type"      => "",
            "Null"      => "NO",
            "Key"       => "",
            "Default"   => null,
            "Extra"     => ""
        );
    }

    public function getSkeleton() {
        return array(
            ['str',     "GROUP_CONCAT("  ],
            ['arg',     0       ],
            ['str',     " SEPARATOR ';')"     ]
        );
    }
}
