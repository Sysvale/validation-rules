# Validation Rules

A Laravel library with useful custom validation rules


## Installation
```bash
composer require sysvale/validation-rules
```

## Translations
If you wish to edit the package error messages, you can publish lang file

```bash
php artisan vendor:publish --tag="sysvale-validation-rules-messages"
```

## Development
- Set up environment
```bash
docker-composer up -d
```

- Install dependencies
```bash
./docker-exec.sh composer update
```

- Run tests
```bash
./docker-exec.sh vendor/bin/phpunit
```
