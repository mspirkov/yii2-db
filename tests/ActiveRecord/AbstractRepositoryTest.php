<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Extensions\Db\Tests\ActiveRecord;

use MSpirkov\Yii2\Extensions\Db\Tests\AbstractTestCase;
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
        $this->assertNull($result);
    }

    public function testFindOne(): void
    {
        $customer = new Customer();
        $customer->email = 'testfindone@gmail.com';
        $customer->name = 'Test Customer';
        $customer->save();

        $result = $this->customerRepository->findOne(['email' => $customer->email]);

        $this->assertNotNull($result);
        $this->assertSame($customer->email, $result->email);
        $this->assertSame($customer->name, $result->name);
    }

    public function testFindOneWithNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findOneWith(
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL],
            'order'
        );

        $this->assertNull($result);
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

        $this->assertNotNull($result);
        $this->assertSame($customer->email, $result->email);
        $this->assertSame($customer->name, $result->name);
        $this->assertTrue($result->isRelationPopulated('orders'));
        $this->assertCount(1, $result->orders);
        $this->assertSame($order->customer_id, $customer->id);
    }

    public function testFindAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findAll(['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]);
        $this->assertCount(0, $result);
    }

    public function testFindAllWithoutCondition(): void
    {
        $resultBeforeSave = $this->paymentSystemRepository->findAll();
        $this->assertCount(0, $resultBeforeSave);

        $paymentSystem1 = new PaymentSystem();
        $paymentSystem1->name = 'PaymentSystem 1';
        $paymentSystem1->save();

        $paymentSystem2 = new PaymentSystem();
        $paymentSystem2->name = 'PaymentSystem 2';
        $paymentSystem2->save();

        $resultAfterSave = $this->paymentSystemRepository->findAll();
        $this->assertCount(2, $resultAfterSave);
        $this->assertSame($paymentSystem1->id, $resultAfterSave[0]->id);
        $this->assertSame($paymentSystem1->name, $resultAfterSave[0]->name);
        $this->assertSame($paymentSystem2->id, $resultAfterSave[1]->id);
        $this->assertSame($paymentSystem2->name, $resultAfterSave[1]->name);
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

        $this->assertCount(2, $result);
        $this->assertSame($customer->id, $result[0]->customer_id);
        $this->assertSame($customer->id, $result[1]->customer_id);
    }

    public function testFindAllWithByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->findAllWith(
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL],
            'orders'
        );

        $this->assertCount(0, $result);
    }

    public function testFindAllWithWithoutCondition(): void
    {
        $resultBeforeSave = $this->cityRepository->findAllWith(null, 'country');
        $this->assertCount(0, $resultBeforeSave);

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
        $this->assertCount(2, $resultAfterSave);

        $this->assertSame($city1->id, $resultAfterSave[0]->id);
        $this->assertSame($city1->name, $resultAfterSave[0]->name);
        $this->assertTrue($resultAfterSave[0]->isRelationPopulated('country'));
        $this->assertSame($country->id, $resultAfterSave[0]->country->id);
        $this->assertSame($country->name, $resultAfterSave[0]->country->name);

        $this->assertSame($city2->id, $resultAfterSave[1]->id);
        $this->assertSame($city2->name, $resultAfterSave[1]->name);
        $this->assertTrue($resultAfterSave[1]->isRelationPopulated('country'));
        $this->assertSame($country->id, $resultAfterSave[1]->country->id);
        $this->assertSame($country->name, $resultAfterSave[1]->country->name);
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

        $this->assertCount(2, $result);

        $this->assertSame($customer1->id, $result[0]->id);
        $this->assertSame($customer1->name, $result[0]->name);
        $this->assertTrue($result[0]->isRelationPopulated('orders'));
        $this->assertCount(2, $result[0]->orders);

        $this->assertSame($order1->id, $result[0]->orders[0]->id);
        $this->assertSame($customer1->id, $result[0]->orders[0]->customer_id);
        $this->assertSame($order2->id, $result[0]->orders[1]->id);
        $this->assertSame($customer1->id, $result[0]->orders[1]->customer_id);

        $this->assertSame($customer2->id, $result[1]->id);
        $this->assertTrue($result[1]->isRelationPopulated('orders'));
        $this->assertCount(0, $result[1]->orders);
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
        $this->assertFalse($result);
    }

    public function testSave(): void
    {
        $customer = new Customer();
        $customer->email = 'testsave1@gmail.com';
        $customer->name = 'Test Save 1';

        $this->assertTrue($this->customerRepository->save($customer));

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        $this->assertNotNull($customerFromDb);
        $this->assertSame($customer->email, $customerFromDb->email);
        $this->assertSame($customer->name, $customerFromDb->name);

        $customer->email = 'testsave2@gmail.com';
        $customer->name = 'Test Save 2';

        $this->assertTrue($this->customerRepository->save($customer, true, ['email']));

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        $this->assertNotNull($customerFromDb);
        $this->assertSame($customer->email, $customerFromDb->email);
        $this->assertSame('Test Save 1', $customerFromDb->name);
    }

    public function testDeleteNotSavedCustomer(): void
    {
        $customer = new Customer();
        $customer->email = 'testdeletenotsaved@gmail.com';
        $customer->name = 'Test delete';

        $result = $this->customerRepository->delete($customer);
        $this->assertSame(0, $result);
    }

    public function testDelete(): void
    {
        $customer = new Customer();
        $customer->name = 'Test delete';
        $customer->email = 'testdelete@gmail.com';
        $customer->save();

        $result = $this->customerRepository->delete($customer);
        $this->assertSame(1, $result);
    }

    public function testUpdateAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->updateAll(
            ['name' => 'test'],
            ['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]
        );

        $this->assertSame(0, $result);
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

        $this->assertSame(1, $result);

        $customerFromDb = Customer::findOne(['email' => $customer->email]);
        $this->assertNotNull($customerFromDb);
        $this->assertSame($newName, $customerFromDb->name);
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

        $this->assertSame(2, $result);

        $customerFromDb1 = Customer::findOne(['email' => $customer1->email]);
        $this->assertNotNull($customerFromDb1);
        $this->assertSame($newName, $customerFromDb1->name);

        $customerFromDb2 = Customer::findOne(['email' => $customer2->email]);
        $this->assertNotNull($customerFromDb2);
        $this->assertSame($newName, $customerFromDb2->name);
    }

    public function testDeleteAllByNonExistentCustomerEmail(): void
    {
        $result = $this->customerRepository->deleteAll(['email' => self::NON_EXISTENT_CUSTOMER_EMAIL]);
        $this->assertSame(0, $result);
    }

    public function testDeleteAllWithParams(): void
    {
        $customer = new Customer();
        $customer->name = 'Test delete all with params';
        $customer->email = 'testdeleteallwithparams@gmail.com';
        $customer->save();

        $result = $this->customerRepository->deleteAll('email = :email', ['email' => $customer->email]);
        $this->assertSame(1, $result);
        $this->assertNull(Customer::findOne(['email' => $customer->email]));
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

        $this->assertSame(2, $result);
        $this->assertFalse(Customer::find()->where($condition)->exists());
    }

    public function testGetTableSchema(): void
    {
        $customerSchema = $this->customerRepository->getTableSchema();
        $this->assertSame('customers', $customerSchema->fullName);
        $this->assertSame(['id', 'email', 'name'], $customerSchema->columnNames);

        $orderSchema = $this->orderRepository->getTableSchema();
        $this->assertSame('orders', $orderSchema->fullName);
        $this->assertSame(['id', 'customer_id'], $orderSchema->columnNames);

        $paymentSystemSchema = $this->paymentSystemRepository->getTableSchema();
        $this->assertSame('payment_systems', $paymentSystemSchema->fullName);
        $this->assertSame(['id', 'name'], $paymentSystemSchema->columnNames);
    }
}
