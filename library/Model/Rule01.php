<?php
class Model_Rule01 extends Model_Rule
{
    public function set_pattern()
    {
        //{PR} {ID}" (ej. "Gracias CFK!", "Aguante Carlo")
        $this->_pattern = "/.*". $this->_reservedWord->get_word() ." ". $this->_id .".*/i";
    }
}
