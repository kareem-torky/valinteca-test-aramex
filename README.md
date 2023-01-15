# valinteca-test-aramex
Aramex package from Valinteca (test)

### Installation steps

1- install package using composer:
```
composer require valinteca-test/aramex
```

2- Publish vendor
```
php artisan vvendor:publish --provider="Valinteca\Aramex\PackageServiceProvider"
```

### Example
```php
use Valinteca\Aramex\Aramex;

$countries = Aramex::getCountries();
```