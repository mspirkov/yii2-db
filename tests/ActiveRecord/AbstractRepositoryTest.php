<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord;

use MSpirkov\Yii2\Db\Tests\AbstractTestCase;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\City;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\Country;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\Customer;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\Order;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\PaymentSystem;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories\CityRepository;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories\CustomerRepository;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories\OrderRepository;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories\PaymentSystemRepository;
use yii\db\Exception as DbException;

class AbstractRepositoryTest extends AbstractTestCase
{
    private const NON_EXISTENT_CUSTOMER_EMAIL = 'nonexistent@gmail.com';

    private CustomerRepository $customerRepository;

    private PaymentSystemRepository $paymentSystemRepository;

    private OrderRepository $orderRepository;

    private CityRepository $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerRepository = new CustomerRepository();
        $this->paymentSystemRepository = new PaymentSystemRepository();
        $this->orderRepository = new OrderRepository();
        $this->cityRepository = new CityRepository();
    }

    public function testFindOneNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findOne(['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]);
        self::assertNull($result);
    }

    public function testFindOne(): void
    {
        $customer = new Customer();
        $customer->email = 'testfindone@gmail.com';
        $customer->name = 'Test Customer';
        $customer->save();

        $result = $this->customerRepository->findOne(['email' => $customer->email]);

        self::assertNotNull($result);
        self::assertSame($customer->email, $result->email);
        self::assertSame($customer->name, $result->name);
    }

    public function testFindOneWithNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findOneWith(
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL],
            'order'
        );

        self::assertNull($result);
    }

    public function testFindOneWith(): void
    {
        $customer = new Customer();
        $customer->email = 'testfindonewith@gmail.com';
        $customer->name = 'Test Customer';
        $customer->save();

        $order = new Order();
        $order->customer_id = $customer->id;
        $order->save();

        $result = $this->customerRepository->findOneWith(['email' => $customer->email], 'orders');

        self::assertNotNull($result);
        self::assertSame($customer->email, $result->email);
        self::assertSame($customer->name, $result->name);
        self::assertTrue($result->isRelationPopulated('orders'));
        self::assertCount(1, $result->orders);
        self::assertSame($order->customer_id, $customer->id);
    }

    public function testFindAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findAll(['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]);
        self::assertCount(0, $result);
    }

    public function testFindAllWithoutCondition(): void
    {
        $resultBeforeSave = $this->paymentSystemRepository->findAll();
        self::assertCount(0, $resultBeforeSave);

        $paymentSystem1 = new PaymentSystem();
        $paymentSystem1->name = 'PaymentSystem 1';
        $paymentSystem1->save();

        $paymentSystem2 = new PaymentSystem();
        $paymentSystem2->name = 'PaymentSystem 2';
        $paymentSystem2->save();

        $resultAfterSave = $this->paymentSystemRepository->findAll();
        self::assertCount(2, $resultAfterSave);
        self::assertSame($paymentSystem1->id, $resultAfterSave[0]->id);
        self::assertSame($paymentSystem1->name, $resultAfterSave[0]->name);
        self::assertSame($paymentSystem2->id, $resultAfterSave[1]->id);
        self::assertSame($paymentSystem2->name, $resultAfterSave[1]->name);
    }

    public function testFindAll(): void
    {
        $customer = new Customer();
        $customer->email = 'testfindall@gmail.com';
        $customer->name = 'Test Customer';
        $customer->save();

        $order1 = new Order();
        $order1->customer_id = $customer->id;
        $order1->save();

        $order2 = new Order();
        $order2->customer_id = $customer->id;
        $order2->save();

        $result = $this->orderRepository->findAll(['customer_id' => $customer->id]);

        self::assertCount(2, $result);
        self::assertSame($customer->id, $result[0]->customer_id);
        self::assertSame($customer->id, $result[1]->customer_id);
    }

    public function testFindAllWithByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findAllWith(
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL],
            'orders'
        );

        self::assertCount(0, $result);
    }

    public function testFindAllWithWithoutCondition(): void
    {
        $resultBeforeSave = $this->cityRepository->findAllWith(null, 'country');
        self::assertCount(0, $resultBeforeSave);

        $country = new Country();
        $country->name = 'Some country';
        $country->save();

        $city1 = new City();
        $city1->country_id = $country->id;
        $city1->name = 'City 1';
        $city1->save();

        $city2 = new City();
        $city2->country_id = $country->id;
        $city2->name = 'City 2';
        $city2->save();

        $resultAfterSave = $this->cityRepository->findAllWith(null, 'country');
        self::assertCount(2, $resultAfterSave);

        self::assertSame($city1->id, $resultAfterSave[0]->id);
        self::assertSame($city1->name, $resultAfterSave[0]->name);
        self::assertTrue($resultAfterSave[0]->isRelationPopulated('country'));
        self::assertSame($country->id, $resultAfterSave[0]->country->id);
        self::assertSame($country->name, $resultAfterSave[0]->country->name);

        self::assertSame($city2->id, $resultAfterSave[1]->id);
        self::assertSame($city2->name, $resultAfterSave[1]->name);
        self::assertTrue($resultAfterSave[1]->isRelationPopulated('country'));
        self::assertSame($country->id, $resultAfterSave[1]->country->id);
        self::assertSame($country->name, $resultAfterSave[1]->country->name);
    }

    public function testFindAllWith(): void
    {
        $customer1 = new Customer();
        $customer1->name = 'Test find all with 1';
        $customer1->email = 'testfindallwith1@gmail.com';
        $customer1->save();

        $customer2 = new Customer();
        $customer2->name = 'Test find all with 2';
        $customer2->email = 'testfindallwith2@gmail.com';
        $customer2->save();

        $order1 = new Order();
        $order1->customer_id = $customer1->id;
        $order1->save();

        $order2 = new Order();
        $order2->customer_id = $customer1->id;
        $order2->save();

        $result = $this->customerRepository->findAllWith(
            ['in', 'id', [$customer1->id, $customer2->id]],
            'orders'
        );

        self::assertCount(2, $result);

        self::assertSame($customer1->id, $result[0]->id);
        self::assertSame($customer1->name, $result[0]->name);
        self::assertTrue($result[0]->isRelationPopulated('orders'));
        self::assertCount(2, $result[0]->orders);

        self::assertSame($order1->id, $result[0]->orders[0]->id);
        self::assertSame($customer1->id, $result[0]->orders[0]->customer_id);
        self::assertSame($order2->id, $result[0]->orders[1]->id);
        self::assertSame($customer1->id, $result[0]->orders[1]->customer_id);

        self::assertSame($customer2->id, $result[1]->id);
        self::assertTrue($result[1]->isRelationPopulated('orders'));
        self::assertCount(0, $result[1]->orders);
    }

    public function testSaveInvalidCustomerWithoutValidation(): void
    {
        $customer = new Customer();

        $this->expectException(DbException::class);
        $this->customerRepository->save($customer, false);
    }

    public function testSaveInvalidCustomerWithValidation(): void
    {
        $customer = new Customer();

        $result = $this->customerRepository->save($customer);
        self::assertFalse($result);
    }

    public function testSave(): void
    {
        $customer = new Customer();
        $customer->email = 'testsave1@gmail.com';
        $customer->name = 'Test Save 1';

        self::assertTrue($this->customerRepository->save($customer));

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        self::assertNotNull($customerFromDb);
        self::assertSame($customer->email, $customerFromDb->email);
        self::assertSame($customer->name, $customerFromDb->name);

        $customer->email = 'testsave2@gmail.com';
        $customer->name = 'Test Save 2';

        self::assertTrue($this->customerRepository->save($customer, true, ['email']));

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        self::assertNotNull($customerFromDb);
        self::assertSame($customer->email, $customerFromDb->email);
        self::assertSame('Test Save 1', $customerFromDb->name);
    }

    public function testDeleteNotSavedCustomer(): void
    {
        $customer = new Customer();
        $customer->email = 'testdeletenotsaved@gmail.com';
        $customer->name = 'Test delete';

        $result = $this->customerRepository->delete($customer);
        self::assertSame(0, $result);
    }

    public function testDelete(): void
    {
        $customer = new Customer();
        $customer->name = 'Test delete';
        $customer->email = 'testdelete@gmail.com';
        $customer->save();

        $result = $this->customerRepository->delete($customer);
        self::assertSame(1, $result);
    }

    public function testUpdateAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->updateAll(
            ['name' => 'test'],
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]
        );

        self::assertSame(0, $result);
    }

    public function testUpdateAllWithParams(): void
    {
        $customer = new Customer();
        $customer->name = 'Test update all with params';
        $customer->email = 'testupdateallwithparams@gmail.com';
        $customer->save();

        $newName = 'testupdateallwithparams';
        $result = $this->customerRepository->updateAll(
            ['name' => $newName],
            'email = :email',
            ['email' => $customer->email]
        );

        self::assertSame(1, $result);

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        self::assertNotNull($customerFromDb);
        self::assertSame($newName, $customerFromDb->name);
    }

    public function testUpdateAll(): void
    {
        $customer1 = new Customer();
        $customer1->name = 'Test update all 1';
        $customer1->email = 'testupdateall1@gmail.com';
        $customer1->save();

        $customer2 = new Customer();
        $customer2->name = 'Test update all 2';
        $customer2->email = 'testupdateall2@gmail.com';
        $customer2->save();

        $newName = 'testupdateall';
        $result = $this->customerRepository->updateAll(
            ['name' => $newName],
            ['in', 'email', [$customer1->email, $customer2->email]]
        );

        self::assertSame(2, $result);

        $customerFromDb1 = Customer::findOne(['email' => $customer1->email]);
        self::assertNotNull($customerFromDb1);
        self::assertSame($newName, $customerFromDb1->name);

        $customerFromDb2 = Customer::findOne(['email' => $customer2->email]);
        self::assertNotNull($customerFromDb2);
        self::assertSame($newName, $customerFromDb2->name);
    }

    public function testDeleteAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->deleteAll(['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]);
        self::assertSame(0, $result);
    }

    public function testDeleteAllWithParams(): void
    {
        $customer = new Customer();
        $customer->name = 'Test delete all with params';
        $customer->email = 'testdeleteallwithparams@gmail.com';
        $customer->save();

        $result = $this->customerRepository->deleteAll('email = :email', ['email' => $customer->email]);
        self::assertSame(1, $result);
        self::assertNull(Customer::findOne(['email' => $customer->email]));
    }

    public function testDeleteAll(): void
    {
        $customer1 = new Customer();
        $customer1->name = 'Test delete all 1';
        $customer1->email = 'testdeleteall1@gmail.com';
        $customer1->save();

        $customer2 = new Customer();
        $customer2->name = 'Test delete all 2';
        $customer2->email = 'testdeleteall2@gmail.com';
        $customer2->save();

        $condition = ['in', 'email', [$customer1->email, $customer2->email]];
        $result = $this->customerRepository->deleteAll($condition);

        self::assertSame(2, $result);
        self::assertFalse(Customer::find()->where($condition)->exists());
    }

    public function testGetTableSchema(): void
    {
        $customerSchema = $this->customerRepository->getTableSchema();
        self::assertSame('customers', $customerSchema->fullName);
        self::assertSame(['id', 'email', 'name'], $customerSchema->columnNames);

        $orderSchema = $this->orderRepository->getTableSchema();
        self::assertSame('orders', $orderSchema->fullName);
        self::assertSame(['id', 'customer_id'], $orderSchema->columnNames);

        $paymentSystemSchema = $this->paymentSystemRepository->getTableSchema();
        self::assertSame('payment_systems', $paymentSystemSchema->fullName);
        self::assertSame(['id', 'name'], $paymentSystemSchema->columnNames);
    }
}
