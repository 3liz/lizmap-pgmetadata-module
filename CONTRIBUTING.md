# Contributing

## Testing with linters

Before the next part, you must have installed the dependencies with `composer install` at the project root.


### PHP CS Fixer for PHP

If you want to see all your issues on the PHP code without fixing it, you
can run the following command:

```bash
composer cs-check
```

If you want to fix the issues automatically, you can run the following command:

```bash
composer cs-fix
```

### PHPStan for PHP

If you want to see all your issues on the PHP code through PHPStan, you can run the following command:

```bash
composer phpstan
```

