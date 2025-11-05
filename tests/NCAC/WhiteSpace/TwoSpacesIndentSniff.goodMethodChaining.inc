<?php

class GoodMethodChaining {

  public function simpleChaining() {
    $result = $this->object
    ->method1()
    ->method2()
    ->method3();
    return $result;
  }

  public function chainingWithNestedBlocks() {
    if ($condition) {
      $result = $this->repository
      ->createQueryBuilder('u')
      ->where('u.status = :status')
      ->setParameter('status', 'active')
      ->getQuery()
      ->getResult();
    }
    return $result;
  }

  public function propertyAccessShouldNotChange(): void {
    $value = $this->property;
    $other = $object->property;
  }

}
