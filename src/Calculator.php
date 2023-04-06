<?php
namespace App;
class Calculator
{
    public function add($number1,$number2)
    {
        return $number1+$number2;
    }
    public function subtract($number1,$number2)
    {
        return $number1-$number2;
    }
    public function divide($number1,$number2)
    {
        return $number1/$number2;
    }
}