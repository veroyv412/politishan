<?php
class Model_Tweet extends Shanty_Mongo_Document
{
    protected static $_db = 'politician';
    protected static $_collection = 'tweets';
}