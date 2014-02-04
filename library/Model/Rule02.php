<?php
class Model_Rule02 extends Model_Rule
{
    public function set_pattern()
    {
        //{ID} {PR}" (ej. "CFK Yegua", "Mauricio Puto")
        $this->_pattern = "/.*". $this->_id ." ". $this->_reservedWord->get_word() .".*/i";
    }
}

