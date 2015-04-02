<?php

/*
 * This file is part of the Acquia Platform: Cloud Data Model.
 *
 * (c) Acquia, Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acquia\Platform\Cloud\Tests\Hosting\Realm;

use Acquia\Platform\Cloud\Hosting\Realm;
use Acquia\Platform\Cloud\Hosting\Realm\RealmList;

/**
 * @coversDefaultClass Acquia\Platform\Cloud\Hosting\Realm\RealmList
 */
class RealmListTest extends \PHPUnit_Framework_TestCase
{
    private $daughtersOfGaia = ['Mnemosyne',  'Tethys', 'Theia', 'Phoebe', 'Rhea', 'Themis'];
    private $sonsOfUranus = ['Oceanus', 'Hyperion', 'Coeus', 'Cronus', 'Crius', 'Iapetus'];

    protected function getBasicRealmList()
    {
        $realmList = new RealmList();
        $titans = array_merge($this->daughtersOfGaia, $this->sonsOfUranus);
        foreach ($titans as $titanName) {
            $realmList->append(new Realm($titanName));
        }
        return $realmList;
    }

    /**
     * @covers ::filter()
     */
    public function testRealmListCanReturnAFilteredListOfContents()
    {
        $realmList = $this->getBasicRealmList();
        $this->assertEquals(12, $realmList->count());

        // filter by array
        $realmsOfSons = $realmList->filter($this->sonsOfUranus);
        $this->assertInstanceOf('Acquia\Platform\Cloud\Hosting\Realm\RealmList', $realmsOfSons);
        $this->assertEquals(6, $realmsOfSons->count());
        $iterator = $realmsOfSons->getIterator();
        while ($iterator->valid()) {
            /** @var \Acquia\Platform\Cloud\Hosting\Realm $realm */
            $realm = $iterator->current();
            $this->assertInstanceOf('Acquia\Platform\Cloud\Hosting\Realm', $realm);
            $this->assertTrue(in_array($realm->getName(), $this->sonsOfUranus));
            $this->assertFalse(in_array($realm->getName(), $this->daughtersOfGaia));
            $iterator->next();
        }

        // filter by comma-delimited string
        $realmsOfDaughters = $realmList->filter(implode(',', $this->daughtersOfGaia));
        $this->assertInstanceOf('Acquia\Platform\Cloud\Hosting\Realm\RealmList', $realmsOfDaughters);
        $this->assertEquals(6, $realmsOfDaughters->count());
        $iterator = $realmsOfDaughters->getIterator();
        while ($iterator->valid()) {
            /** @var \Acquia\Platform\Cloud\Hosting\Realm $realm */
            $realm = $iterator->current();
            $this->assertInstanceOf('Acquia\Platform\Cloud\Hosting\Realm', $realm);
            $this->assertTrue(in_array($realm->getName(), $this->daughtersOfGaia));
            $this->assertFalse(in_array($realm->getName(), $this->sonsOfUranus));
            $iterator->next();
        }
    }

    /**
     * @covers ::filter
     * @expectedException \InvalidArgumentException
     * @dataProvider exceptionalFilterProvider()
     */
    public function testFilterWillThrowExceptionForBadParameter($filter)
    {
        $realmList = $this->getBasicRealmList();
        $realmList->filter($filter);
    }

    public function exceptionalFilterProvider()
    {
        return [
            'null' => [null],
            'stdClass' => [new \stdClass()],
        ];
    }

    /**
     * @covers ::offsetSet
     * @expectedException \InvalidArgumentException
     * @dataProvider exceptionalValueProvider()
     */
    public function testOffsetSetWillThrowExceptionForNonRealm($value)
    {
        $realmList = $this->getBasicRealmList();
        $realmList->offsetSet(0, $value);
    }

    public function exceptionalValueProvider()
    {
        return [
            'null' => [null],
            'stdClass' => [new \stdClass()],
        ];
    }
}
