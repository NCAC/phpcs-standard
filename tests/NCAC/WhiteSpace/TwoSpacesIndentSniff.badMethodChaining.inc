<?php

class BadMethodChaining {

  public function wrongIndentationSimple(): mixed {
    $result = $this->object
    ->method1()   // Should be 6 spaces, found 4
    ->method2()   // Should be 6 spaces, found 4
    ->method3();  // Should be 6 spaces, found 4
    return $result;
  }

  public function wrongIndentationNested(): mixed {
    if (true) {
      $result = $this->repository
      ->createQueryBuilder('u')    // Should be 6 spaces (4+2), found 0
      ->where('u.status = :status')  // Should be 6 spaces (4+2), found 12
      ->setParameter('status', 'active') // Should be 6 spaces (4+2), found 4
      ->getQuery()         // Should be 6 spaces (4+2), found 14
      ->getResult();           // Should be 6 spaces (4+2), found 10
    }
    return $result;
  }

}
