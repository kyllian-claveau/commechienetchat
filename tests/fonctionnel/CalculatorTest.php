<?php
namespace App\Tests;
use App\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calculator = new Calculator();
        $result = $calculator->add(1, 2);
        $this->assertEquals(3, $result);
    }
    public function testSubtract()
    {
        $calculator = new Calculator();
        $result = $calculator->subtract(5, 3);
        $this->assertEquals(2, $result);
    }
    public function testDivide()
    {
        $calculator = new Calculator();
        $result = $calculator->divide(6, 3);
        $this->assertEquals(2, $result);
    }
}
