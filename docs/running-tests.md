# Running Tests

Testing with Pest PHP framework.

## 🧪 Test Commands

### Run All Tests
```bash
php artisan test
```

### Run Specific Test
```bash
php artisan test tests/Feature/UserFeatureTest.php
```

### Run with Filter
```bash
php artisan test --filter="test_user_can_be_created"
```

### Run with Coverage
```bash
php artisan test --coverage
```

## 📁 Test Structure

```
tests/
├── Feature/          # API endpoint tests
├── Unit/            # Service/repository tests
└── Mock/            # Mock data classes
```

## 🎯 Example Tests

### Feature Test
```php
it('can create user', function () {
    $response = $this->postJson('/api/v1/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(201);
});
```

### Unit Test
```php
it('can get user by id', function () {
    $user = User::factory()->create();
    $service = app(UserService::class);
    
    $result = $service->findById($user->id);
    
    expect($result->id)->toBe($user->id);
});
```

## Test Configuration

**Database**: Uses in-memory SQLite
**Traits**: RefreshDatabase for clean state
**Mocking**: Mock data classes for consistent test data

## Quick Test

```bash
# Test authentication
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "superadmin@ims.com", "password": "123456"}'

# Test users endpoint
curl -X GET http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

*For detailed documentation, see the [Features Guide](features.md).*