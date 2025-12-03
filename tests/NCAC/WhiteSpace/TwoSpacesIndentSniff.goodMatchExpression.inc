<?php

// GOOD: Basic match expression
$result = match($status) {
  'pending' => 'En attente',
  'approved' => 'Approuvé',
  'rejected' => 'Rejeté',
  default => 'Inconnu'
};

// GOOD: Match with complex expressions
$config = match(true) {
  $env === 'dev' => [
    'debug' => true,
    'cache' => false,
    'logging' => [
      'level' => 'debug',
      'handlers' => ['file', 'console']
    ]
  ],
  $env === 'prod' => [
    'debug' => false,
    'cache' => true,
    'logging' => [
      'level' => 'error',
      'handlers' => ['file']
    ]
  ],
  default => []
};

// GOOD: Match with method chaining
$processor = match($type) {
  'json' => $this->factory
    ->createJsonProcessor()
    ->withOptions(['strict' => true])
    ->withValidator($validator),
  'xml' => $this->factory
    ->createXmlProcessor()
    ->withSchema($schema),
  default => $this->factory
    ->createDefaultProcessor()
};

// GOOD: Match in array context
$responses = [
  'success' => match($format) {
    'json' => ['status' => 'ok'],
    'xml' => '<status>ok</status>',
    default => 'OK'
  },
  'error' => match($format) {
    'json' => [
      'status' => 'error',
      'message' => $error->getMessage()
    ],
    'xml' => '<error>' . htmlspecialchars($error->getMessage()) . '</error>',
    default => 'ERROR: ' . $error->getMessage()
  }
];

// GOOD: Nested match expressions
$final = match($category) {
  'user' => match($action) {
    'create' => $this->userService
      ->createUser($data),
    'update' => $this->userService
      ->updateUser($id, $data),
    default => throw new InvalidActionException()
  },
  'product' => match($action) {
    'create' => [
      'id' => $this->productService->create($data),
      'status' => 'created'
    ],
    default => null
  },
  default => []
};
