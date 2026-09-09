<?php
namespace Aura\Input\Filter;

use PHPUnit\Framework\TestCase;

class FailureCollectionTest extends TestCase
{
    protected FailureCollection $failures;

    protected function setUp(): void
    {
        $this->failures = new FailureCollection();
    }

    public function testIsEmptyOnConstruction()
    {
        $this->assertTrue($this->failures->isEmpty());
        $this->assertSame([], $this->failures->getMessages());
    }

    public function testAddAppendsAndSetReplaces()
    {
        $this->failures->add('name', 'first');
        $this->failures->add('name', 'second');
        $this->assertSame(['first', 'second'], $this->failures->getMessagesForField('name'));

        $this->failures->set('name', 'only');
        $this->assertSame(['only'], $this->failures->getMessagesForField('name'));
    }

    public function testAddAndSetReturnTheFailure()
    {
        $failure = $this->failures->add('name', 'message', ['arg']);
        $this->assertSame('name', $failure->getField());
        $this->assertSame('message', $failure->getMessage());
        $this->assertSame(['arg'], $failure->getArgs());

        $failure = $this->failures->set('name', 'other', ['x']);
        $this->assertSame(['x'], $failure->getArgs());
    }

    /**
     * Regression: add() and set() used to discard $args, so the arguments were
     * available on the returned Failure but never again from the collection.
     */
    public function testArgsSurviveInTheCollection()
    {
        $this->failures->add('name', 'first', ['a', 'b']);
        $this->failures->add('name', 'second');

        $stored = $this->failures->forField('name');
        $this->assertCount(2, $stored);
        $this->assertSame(['a', 'b'], $stored[0]->getArgs());
        $this->assertSame([], $stored[1]->getArgs());

        $this->failures->set('email', 'replaced', ['c']);
        $this->assertSame(['c'], $this->failures->forField('email')[0]->getArgs());
    }

    public function testForFieldReturnsFailureObjects()
    {
        $this->failures->add('name', 'message');
        $failures = $this->failures->forField('name');
        $this->assertInstanceOf(Failure::class, $failures[0]);
        $this->assertSame('name', $failures[0]->getField());
        $this->assertSame('message', $failures[0]->getMessage());
    }

    public function testForFieldOnMissingFieldReturnsEmptyArray()
    {
        $this->assertSame([], $this->failures->forField('nope'));
        $this->assertSame([], $this->failures->getMessagesForField('nope'));
    }

    public function testForPathIsEquivalentToForField()
    {
        $this->failures->add('address.city', 'City is required.');
        $this->assertEquals(
            $this->failures->forField('address.city'),
            $this->failures->forPath('address.city')
        );
        $this->assertSame(
            'City is required.',
            $this->failures->forPath('address.city')[0]->getMessage()
        );
    }

    public function testGetMessagesIsAFlatMapKeyedByPath()
    {
        $this->failures->add('name', 'Name is required.');
        $this->failures->add('address.city', 'City is required.');
        $this->failures->add('phone_numbers.2.number', 'Bad number.');

        $this->assertSame(
            [
                'name'                   => ['Name is required.'],
                'address.city'           => ['City is required.'],
                'phone_numbers.2.number' => ['Bad number.'],
            ],
            $this->failures->getMessages()
        );
    }

    public function testGetNestedMessagesMirrorsTheDataShape()
    {
        $this->failures->add('name', 'Name is required.');
        $this->failures->add('address.city', 'City is required.');
        $this->failures->add('phone_numbers.2.number', 'Bad number.');

        $this->assertSame(
            [
                'name'    => ['Name is required.'],
                'address' => ['city' => ['City is required.']],
                'phone_numbers' => [
                    2 => ['number' => ['Bad number.']],
                ],
            ],
            $this->failures->getNestedMessages()
        );
    }

    public function testGetNestedMessagesOnEmptyCollection()
    {
        $this->assertSame([], $this->failures->getNestedMessages());
    }

    public function testAddMessagesForFieldAcceptsStringOrArray()
    {
        $this->failures->addMessagesForField('name', 'one');
        $this->failures->addMessagesForField('name', ['two', 'three']);
        $this->assertSame(
            ['one', 'two', 'three'],
            $this->failures->getMessagesForField('name')
        );
    }

    /**
     * An empty message list must not register the field: a collection holding
     * only empty entries is still empty.
     */
    public function testAddMessagesForFieldWithNoMessagesLeavesCollectionEmpty()
    {
        $this->failures->addMessagesForField('name', []);
        $this->assertTrue($this->failures->isEmpty());
        $this->assertSame([], $this->failures->getMessages());
    }
}
