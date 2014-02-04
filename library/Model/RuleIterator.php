<?php
class Model_RuleIterator implements Iterator
{
    private $_position = 0;

    private $_list;

    public function  __construct()
    {
        $this->_position = 0;
        $this->_list[0] = new Model_Rule01();
        $this->_list[1] = new Model_Rule02();
        $this->_list[2] = new Model_Rule03();
        $this->_list[3] = new Model_Rule04();
        $this->_list[4] = new Model_Rule05();
        $this->_list[5] = new Model_Rule06();
    }

    function rewind() {
        $this->_position = 0;
    }

    function current() {
        return $this->_list[$this->_position];
    }

    function key() {
        return $this->_position;
    }

    function next() {
        ++$this->_position;
    }

    function valid() {
        return isset($this->_list[$this->_position]);
    }
}
