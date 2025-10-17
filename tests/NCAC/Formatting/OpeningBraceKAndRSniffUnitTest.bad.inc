<?php


class Test
{

  /**
   * 
   * @var string
   */
  private $var = 'Test';

  /**
   * This is a test function.
   * @return void 
   */
  public function testFunction(): void {
    if ($this->var === 'Test') {
      echo "This is a test.";
    } else {
      echo "This is not a test.";
    }
  }

}
