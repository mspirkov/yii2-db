<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii2 DB Extension</h1>
</p>

A package of helper classes for working with databases in Yii2.

[![PHP](https://img.shields.io/badge/%3E%3D7.4-7A86B8.svg?style=for-the-badge&logo=php&logoColor=white&label=PHP)](https://www.php.net/releases/7_4_0.php)
[![Yii 2.0.x](https://img.shields.io/badge/%3E%3D2.0.53-247BA0.svg?style=for-the-badge&logo=yii&logoColor=white&label=Yii)](https://github.com/yiisoft/yii2/tree/2.0.53)
[![Tests](https://img.shields.io/github/actions/workflow/status/mspirkov/yii2-db/ci.yml?branch=main&style=for-the-badge&logo=github&label=Tests)](https://github.com/mspirkov/yii2-db/actions/workflows/ci.yml)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/mspirkov/yii2-db/ci.yml?branch=main&style=for-the-badge&logo=github&label=PHPStan)](https://github.com/mspirkov/yii2-db/actions/workflows/ci.yml)
![Coverage](https://img.shields.io/badge/100%25-44CC11.svg?style=for-the-badge&label=Coverage)
![PHPStan Level Max](https://img.shields.io/badge/Max-7A86B8.svg?style=for-the-badge&label=PHPStan%20Level)

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

## Components

- [AbstractRepository](#abstractrepository)
- [DateTimeBehavior](#datetimebehavior)
- [TransactionManager](#transactionmanager)

### AbstractRepository

An abstract class for creating repositories that interact with ActiveRecord models. Contains the most commonly used methods: `findOne`, `findAll`, `save` and others. It also has several additional methods: `findOneWith`, `findAllWith`.

This way, you can separate the logic of executing queries from the ActiveRecord models themselves. This will make your ActiveRecord models thinner and simpler. It will also make testing easier, as you can mock the methods for working with the database.

#### Usage example:

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

    public function getCustomer(int $id): ?Customer
    {
        return $this->customerRepository->findOne($id);
    }
}
```

### DateTimeBehavior

Behavior for ActiveRecord models that automatically fills the specified attributes with the current date and time.

By default, this behavior uses the current date, time, and time zone. If necessary, you can specify your own
attributes and time zone.

#### Usage example:

```php
/**
 * @property int $id
 * @property string $content
 * @property string $created_at
 * @property string|null $updated_at
 */
class Message extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{messages}}';
    }

    public function behaviors(): array
    {
        return [
            DateTimeBehavior::class,
        ];
    }
}
```

### TransactionManager

A utility class for managing database transactions with a consistent and safe approach.

This class simplifies the process of wrapping database operations within transactions,
ensuring that changes are either fully committed or completely rolled back in case of errors.

It provides two main methods:

- `safeWrap` - executes a callable within a transaction, safely handling exceptions and logging them.
- `wrap` - executes a callable within a transaction.

#### Usage example:

```php
class TransactionManager extends \MSpirkov\Yii2\Db\TransactionManager
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
        private readonly TransactionManager $transactionManager,
        private readonly ProductFilesystem $productFilesystem,
        private readonly ProductRepository $productRepository,
    ) {}

    /**
     * @return array{success: bool, message?: string}
     */
    public function deleteProduct(int $id): array
    {
        $product = $this->productRepository->findOne($id);

        // There's some logic here. For example, checking for the existence of a product.

        $transactionResult = $this->transactionManager->safeWrap(function () use ($product) {
            $this->productRepository->delete($product);
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
