<?php

namespace RexSoftwareTest\ApiBundle\Tests\Entity;


use PHPUnit\Framework\TestCase;
use RexSoftwareTest\ApiBundle\Entity\Actor;

class ActorTest extends TestCase
{
    public function testSetBirthDate()
    {
        $actor = new Actor();
        $this->assertNull($actor->getBirthDate());
        $now = new \DateTime();
        $actor->setBirthDate($now);
        $this->assertNotNull($actor->getBirthDate());
        $this->assertEquals($now->getTimestamp(), $actor->getBirthDate()->getTimestamp());
    }

    public function testSetBirthDateAfter()
    {
        $actor = new Actor();
        try {
            $actor->setBirthDate((new \DateTime())->add(new \DateInterval('P1Y')));
            $this->fail('should have thrown an exception');
        } catch (\InvalidArgumentException $e) {
            $this->assertNull($actor->getBirthDate());
        }
    }

    public function testGetAge()
    {
        $actor = new Actor();
        $this->assertNull($actor->getAge());
        $actor->setBirthDate((new \DateTime())->sub(new \DateInterval('P1Y')));
        $this->assertEquals(1, $actor->getAge());
    }

    public function testGetAgeJustOverTwo()
    {
        $actor = new Actor();
        $actor->setBirthDate((new \DateTime())->sub(new \DateInterval('P2Y50D')));
        $this->assertEquals(2, $actor->getAge());
    }
}
