<?php

class GoodExampleClass {

  /**
   * @var int
   */
  private $counter = 0;

  /**
   * @var string
   */
  protected $label;

  /**
   * @var array
   */
  public $items = [];

  /**
   * Constructor.
   *
   * @param string $label
   */
  public function __construct(string $label = 'default') {
    $this->label = $label;
  }

  /**
   * Increments the counter.
   *
   * @param int $step
   * @return int
   */
  public function increment(int $step = 1): int {
    $this->counter += $step;
    return $this->counter;
  }

  /**
   * Adds an item to the list.
   *
   * @param mixed $item
   * @return void
   */
  public function addItem($item): void {
    $this->items[] = $item;
  }

  /**
   * Gets the label.
   *
   * @return string
   */
  public function getLabel(): string {
    return $this->label;
  }

  /**
   * Returns a summary string.
   *
   * @return string
   */
  public function summary(): string {
    return "Label: {$this->label}, Count: {$this->counter}, Items: " . count($this->items);
  }

}
