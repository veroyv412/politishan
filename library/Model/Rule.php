<?php
class Model_Rule
{
    /** The reserved word object
     *
     * @var Model_ReservedWord
     */
    protected $_reservedWord;

    /** Is the synonym of the twitter screen name
     *
     * @var string
     */
    protected $_id;

    /** The regex that will fulfill the rule validation
     *
     * @var string
     */
    protected $_pattern;


    public function get_reservedWord() {
        return $this->_reservedWord;
    }

    public function set_reservedWord($_reservedWord) {
        $this->_reservedWord = $_reservedWord;
    }

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_pattern() {
        return $this->_pattern;
    }

    public function isValid( $text )
    {
        $this->set_pattern();
        return preg_match($this->_pattern, $text) === 1;
    }

}

