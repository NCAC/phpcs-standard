<?php




class TestIndentClass {

    const TEST_NAME = "Yo !"; // Error, should be exactly 2 spaces

    public static function yoyo() {} // Error, should be exactly 2 spaces

    public function foo() { // Error, should be exactly 2 spaces
        return false; // Error, should be exactly 4 spaces
    } // Error, should be exactly 2 spaces

}
