<?php
class Model_Rule06 extends Model_Rule
{
    public function set_pattern()
    {
        //{ID} {PR}" (ej. "CFK Yegua", "Mauricio Puto")
        $this->_pattern = "/.*". $this->_id ." sos una ". $this->_reservedWord->get_word() .".*/i";
    }
}