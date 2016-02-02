<?php

namespace LizardsAndPumpkins;

use LizardsAndPumpkins\DataPool\KeyValue\Credis\CredisKeyValueStore;

class CredisKeyValueStoreTest extends \PHPUnit_Framework_TestCase
{
    const REDIS_HOST = 'localhost';

    const REDIS_PORT = '6379';

    /**
     * @var CredisKeyValueStore
     */
    private $keyValueStore;

    protected function setUp()
    {
        $client = new \Credis_Client(self::REDIS_HOST, self::REDIS_PORT);
        $client->del('foo');
        $client->del('key1');
        $client->del('key2');

        $this->keyValueStore = new CredisKeyValueStore($client);
    }

    public function testValueIsSetAndRetrieved()
    {
        $this->keyValueStore->set('foo', 'bar');
        $result = $this->keyValueStore->get('foo');

        $this->assertEquals('bar', $result);
    }

    public function testMultipleValuesAreSetAndRetrieved()
    {
        $items = ['key1' => 'foo', 'key2' => 'bar'];
        $keys = array_keys($items);

        $this->keyValueStore->multiSet($items);
        $result = $this->keyValueStore->multiGet($keys);

        $this->assertSame($items, $result);
    }

    public function testMissingValuesAreExcludedFromResultArray()
    {
        $items = ['key1' => 'foo', 'key2' => 'bar'];
        $keys = array_keys($items);

        $this->keyValueStore->multiSet($items);

        $keys[] = 'key3';
        $result = $this->keyValueStore->multiGet($keys);

        $this->assertSame($items, $result);
    }

    public function testFalseIsReturnedIfKeyDoesNotExist()
    {
        $this->assertFalse($this->keyValueStore->has('foo'));
    }
}