<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Instagram;
use PHPUnit\Framework\TestCase;

final class InstagramTest extends TestCase
{
    public function setUp(): void
    {
        kirby()->cache('bnomei.instagram')->flush();
    }

    public function testConstruct()
    {
        $inst = new Instagram();
        $this->assertInstanceOf(Instagram::class, $inst);
    }

    public function testOptions()
    {
        $inst = new Instagram();
        $options = $inst->option();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('debug', $options);
        $this->assertArrayHasKey('expires', $options);
        $this->assertArrayHasKey('token', $options);
        $this->assertArrayHasKey('api', $options);
        $this->assertArrayHasKey('endpoint', $options);
        $this->assertArrayHasKey('params', $options);

        $inst = new Instagram([
            'debug' => true,
            'token' => function() { return 'TOKEN'; },
        ]);
        $this->assertTrue($inst->option('debug'));
        $this->assertEquals('TOKEN', $inst->option('token'));
    }

    public function testRemoteGet()
    {
        $inst = new Instagram();
        $this->assertCount(0, $inst->remoteGet($inst->option('api')));

        $this->assertCount(
            1,
            $inst->remoteGet('https://repo.packagist.org/p/bnomei/kirby3-instagram.json', 'packages')
        );
    }

    public function testUrl()
    {
        $inst = new Instagram();
        $this->assertEquals(
            $inst->option('api').'endpoint?access_token=token&par=am',
            $inst->url('token', 'endpoint', ['par'=>'am'])
        );
    }

    public function testCacheId()
    {
        $inst = new Instagram();
        $this->assertRegExp(
            '/^1472529774-\d-\d-\d-NONE$/',
            $inst->cacheId('token.endpoint')
        );
    }

    public function testWriteRead()
    {
        $inst = new Instagram();
        $this->assertNull($inst->read('test'));
        $this->assertTrue($inst->write('test', ['data'=>1]));
        $this->assertIsArray($inst->read('test'));
        $this->assertCount(1, $inst->read('test'));
        $this->assertNull($inst->read('test', true));

        $inst = new Instagram(['debug' => true]); // flush
        $this->assertNull($inst->read('test'));
        $this->assertFalse($inst->write('test', ['data'=>1]));
        $this->assertNull($inst->read('test'));
    }

    public function testIsJson()
    {
        $inst = new Instagram();
        $this->assertTrue($inst->isJson(json_encode(['data'=>1])));
        $this->assertFalse($inst->isJson('{invalid}'));
    }

    public function testApi()
    {
        $inst = new Instagram();
        $key = 'TOKEN'.$inst->option('endpoint');

        $id = $inst->cacheId($key);
        $this->assertRegExp('/^2178974503-\d-\d-\d-NONE$/', $id);

        $this->assertCount(0, $inst->api('TOKEN'));
        $this->assertCount(0, $inst->api('TOKEN'));
        $this->assertIsArray($inst->read($key));
    }

    public function testStaticInstagram()
    {
        $inst = Instagram::instagram('TOKEN');
        $this->assertCount(0, $inst);

        $inst = Instagram::instagram();
        $this->assertCount(0, $inst);
    }

    public function testNonInstagramApi()
    {
        $inst = new Bnomei\Instagram([
            'token' => null,
            'api' => 'https://repo.packagist.org/',
            'endpoint' => 'p/bnomei/kirby3-instagram.json',
            'json-root' => 'packages',
        ]);
        $data = $inst->api();
        $this->assertCount(1, $data);

        $this->assertEquals(
            'Bruno Meilick',
            $data['bnomei/kirby3-instagram']['1.0.0']['authors'][0]['name']
        );
    }

}
