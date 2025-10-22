<?php

class GoodExampleClass {

  /** @var array<string, mixed> $items */
  public array $items = [];

  protected string $label;

  private int $counter = 0;

  /**
   * Constructor.
   *
   */
  public function __construct(string $label = 'default') {
    $this->label = $label;
  }

  /**
   * Increments the counter.
   *
   */
  public function increment(int $step = 1): int {
    $this->counter += $step;
    return $this->counter;
  }

  /**
   * Adds an item to the list.
   *
   * @param mixed $item
   */
  public function addItem($item): void {
    $this->items[] = $item;
  }

  /**
   * Gets the label.
   *
   */
  public function getLabel(): string {
    return $this->label;
  }

  /**
   * Returns a summary string.
   *
   */
  public function summary(): string {
    return "Label: {$this->label}, Count: {$this->counter}, Items: " . count($this->items);
  }

}
