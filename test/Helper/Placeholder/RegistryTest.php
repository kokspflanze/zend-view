<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper\Placeholder;

use Zend\View\Helper\Placeholder\Registry;
use Zend\View\Helper\Placeholder\Container;

/**
 * Test class for Zend\View\Helper\Placeholder\Registry.
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    public $registry;


    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->registry = new Registry();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->registry);
    }

    /**
     * @return void
     */
    public function testCreateContainer()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->createContainer('foo');
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testCreateContainerCreatesDefaultContainerClass()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $container = $this->registry->createContainer('foo');
        $this->assertInstanceOf('Zend\View\Helper\Placeholder\Container', $container);
    }

    /**
     * @return void
     */
    public function testGetContainerCreatesContainerIfNonExistent()
    {
        $this->assertFalse($this->registry->containerExists('foo'));
        $container = $this->registry->getContainer('foo');
        $this->assertInstanceOf('Zend\View\Helper\Placeholder\Container\AbstractContainer', $container);
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    /**
     * @return void
     */
    public function testSetContainerCreatesRegistryEntry()
    {
        $foo = new Container(['foo', 'bar']);
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->setContainer('foo', $foo);
        $this->assertTrue($this->registry->containerExists('foo'));
    }

    public function testSetContainerCreatesRegistersContainerInstance()
    {
        $foo = new Container(['foo', 'bar']);
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->registry->setContainer('foo', $foo);
        $container = $this->registry->getContainer('foo');
        $this->assertSame($foo, $container);
    }

    public function testContainerClassAccessorsSetState()
    {
        $this->assertEquals('Zend\View\Helper\Placeholder\Container', $this->registry->getContainerClass());
        $this->registry->setContainerClass('ZendTest\View\Helper\TestAsset\MockContainer');
        $this->assertEquals(
            'ZendTest\View\Helper\TestAsset\MockContainer',
            $this->registry->getContainerClass()
        );
    }

    public function testSetContainerClassThrowsExceptionWithInvalidContainerClass()
    {
        try {
            $this->registry->setContainerClass('ZendTest\View\Helper\TestAsset\BogusContainer');
            $this->fail('Invalid container classes should not be accepted');
        } catch (\Exception $e) {
        }
    }

    public function testDeletingContainerRemovesFromRegistry()
    {
        $this->registry->createContainer('foo');
        $this->assertTrue($this->registry->containerExists('foo'));
        $result = $this->registry->deleteContainer('foo');
        $this->assertFalse($this->registry->containerExists('foo'));
        $this->assertTrue($result);
    }

    public function testDeleteContainerReturnsFalseIfContainerDoesNotExist()
    {
        $result = $this->registry->deleteContainer('foo');
        $this->assertFalse($result);
    }

    public function testUsingCustomContainerClassCreatesContainersOfCustomClass()
    {
        $this->registry->setContainerClass('ZendTest\View\Helper\TestAsset\MockContainer');
        $container = $this->registry->createContainer('foo');
        $this->assertInstanceOf('ZendTest\View\Helper\TestAsset\MockContainer', $container);
    }

    /**
     * @group ZF-10793
     */
    public function testSetValueCreateContainer()
    {
        $this->registry->setContainerClass('ZendTest\View\Helper\TestAsset\MockContainer');
        $data = [
            'ZF-10793'
        ];
        $container = $this->registry->createContainer('foo', $data);
        $this->assertEquals(['ZF-10793'], $container->data);
    }
}
