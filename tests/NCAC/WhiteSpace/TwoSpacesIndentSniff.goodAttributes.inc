<?php

// GOOD: Simple attribute
#[Route('/api/users')]
class UserController {

  #[Route('/{id}', methods: ['GET'])]
  public function show(int $id): JsonResponse {
    return $this->json(['id' => $id]);
  }
}

// GOOD: Multiple attributes with proper indentation
#[Entity]
#[Table(name: 'users')]
#[Index(
  name: 'idx_email',
  columns: ['email']
)]
class User {

  #[Id]
  #[GeneratedValue]
  #[Column(type: 'integer')]
  private int $id;

  #[Column(
    type: 'string',
    length: 255,
    unique: true
  )]
  private string $email;

  #[ManyToOne(targetEntity: Company::class)]
  #[JoinColumn(
    name: 'company_id',
    referencedColumnName: 'id'
  )]
  private ?Company $company = null;
}

// GOOD: Attributes with complex arguments
#[Route(
  path: '/api/v1/users/{id}/profile',
  methods: ['PUT', 'PATCH'],
  requirements: [
    'id' => '\d+'
  ],
  defaults: [
    'format' => 'json'
  ]
)]
#[Security("is_granted('ROLE_USER')")]
class UserProfileController {
  public function updateProfile(
    #[MapEntity] User $user,
    #[RequestBody] array $data
  ): JsonResponse {
    return $this->json([
      'user' => [
        'id' => $user->getId(),
        'updated' => true
      ]
    ]);
  }
}

// GOOD: Attributes in match expression context
#[Service]
class NotificationService {

  public function send(Notification $notification): bool {
    return match($notification->getType()) {
      NotificationType::EMAIL => $this->emailService
        ->withTemplate($notification->getTemplate())
        ->send($notification),
      NotificationType::SMS => $this->smsService
        ->withProvider('twilio')
        ->send($notification),
      default => false
    };
  }
}
