<h1 align="center">Yii2 DB</h1>

A package of helper classes for working with databases in Yii2. Simplifies working with transactions and Active Record.

[![PHP](https://img.shields.io/badge/%3E%3D7.4-7A86B8.svg?style=for-the-badge&logo=php&logoColor=white&label=PHP)](https://www.php.net/releases/7.4/en.php)
[![Yii 2.0.x](https://img.shields.io/badge/%3E%3D2.0.53-247BA0.svg?style=for-the-badge&logo=yii&logoColor=white&label=Yii)](https://github.com/yiisoft/yii2/tree/2.0.53)
[![Tests](https://img.shields.io/github/actions/workflow/status/mspirkov/yii2-db/ci.yml?branch=main&style=for-the-badge&logo=github&label=Tests)](https://github.com/mspirkov/yii2-db/actions/workflows/ci.yml)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/mspirkov/yii2-db/ci.yml?branch=main&style=for-the-badge&logo=github&label=PHPStan)](https://github.com/mspirkov/yii2-db/actions/workflows/ci.yml)

## Installation

Run

```bash
php composer.phar require mspirkov/yii2-db
```

or add

```json
"mspirkov/yii2-db": "^0.1"
```

to the `require` section of your `composer.json` file.

## Usage

### ActiveRecord

### AbstractRepository

An abstract class for creating repositories that interact with ActiveRecord models. Contains the most commonly used methods: `findOne`, `findAll`, `save`, etc., and adds several additional methods: `findOneWith`, `findAllWith`.

Basic usage example:

```php
/**
 * @extends AbstractRepository<Customer>
 */
class CustomerRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Customer::class);
    }
}
```

```php
class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
    ) {}

    public function getCustomerData(int $id): ?Customer
    {
        return $this->customerRepository->findOne($id);
    }
}
```

## TransactionManager

A utility class for managing database transactions with a consistent and safe approach.

This class simplifies the process of wrapping database operations within transactions,
ensuring that changes are either fully committed or completely rolled back in case of errors.

It provides two main methods:
-   `wrap` for executing a callable within a transaction and re-throwing any exceptions
-   `safeWrap` for executing a callable within a transaction, logging exceptions, and returning a
    boolean indicating success.

Basic usage example:

```php
class DbTransactionManager extends TransactionManager
{
    public function __construct()
    {
        parent::__construct(Yii::$app->db);
    }
}
```

```php
class ProductService
{
    public function __construct(
        private readonly DbTransactionManager $dbTransactionManager,
        private readonly ProductFilesystem $productFilesystem,
        private readonly ProductRepository $productRepository,
    ) {}

    /**
     * @return array{success: bool, message?: string}
     */
    public function deleteProduct(int $id): array
    {
        // Some logic here

        $transactionResult = $this->dbTransactionManager->safeWrap(function () use ($product) {
            $this->productRepository->delete($product)
            $this->productFilesystem->delete($product->preview_filename);

            return [
                'success' => true,
            ];
        });

        if ($transactionResult === false) {
            return [
                'success' => false,
                'message' => 'Something went wrong',
            ];
        }

        return $transactionResult;
    }
}
```

## Quality code

![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-7A86B8.svg?style=for-the-badge&logo=php&logoColor=white)
