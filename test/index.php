<?php

abstract class A {
    static $c = 2;
    protected $d = 3;
    public $e = 4;
}

class B extends A {
    public function __construct () {
        print '<pre>';
        echo parent::$c;
        echo $this->d;
        echo $this->e;
        print '<pre>';
        $this->d = 22;
    }

    public function o () {
        echo $this->d;
    }
}

$o = new B();
$o->o();
print_r($o);
print '<pre>';
