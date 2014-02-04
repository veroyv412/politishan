<?php
class Model_ReservedWord
{
    /** The Reserved Word
     *
     * @var String
     */
    private $_word;

    /** The connotation number of the reserved word. Positive or Negative
     * Ex. 1 positive | -1 negative
     *
     * @var int
     */
    private $_connotation;


    public function get_word() {
        return $this->_word;
    }

    public function set_word($_word) {
        $this->_word = $_word;
    }

    public function get_connotation() {
        return $this->_connotation;
    }

    public function set_connotation($_connotation) {
        $this->_connotation = $_connotation;
    }
}

