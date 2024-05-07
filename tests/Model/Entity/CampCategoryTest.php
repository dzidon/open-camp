<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\CampCategory;
use App\Tests\Library\DataStructure\TreeNodeMock;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\UuidV4;

class CampCategoryTest extends TestCase
{
    private const NAME = 'Name';
    private const URL_NAME = 'url-name';
    private const PRIORITY = 100;

    private CampCategory $campCategory;

    public function testId(): void
    {
        $id = $this->campCategory->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->campCategory->getName());

        $newName = 'New Name';
        $this->campCategory->setName($newName);
        $this->assertSame($newName, $this->campCategory->getName());
    }

    public function testUrlName(): void
    {
        $this->assertSame(self::URL_NAME, $this->campCategory->getUrlName());

        $newUrlName = 'new-url-name';
        $this->campCategory->setUrlName($newUrlName);
        $this->assertSame($newUrlName, $this->campCategory->getUrlName());
    }

    public function testIdentifier(): void
    {
        $this->assertSame($this->campCategory->getId(), $this->campCategory->getIdentifier());
    }

    public function testParent(): void
    {
        $this->assertNull($this->campCategory->getParent());

        $parent = new CampCategory('Parent', 'parent', 100);
        $this->campCategory->setParent($parent);

        $this->assertSame($parent, $this->campCategory->getParent());
        $this->assertContains($this->campCategory, $parent->getChildren());

        $this->campCategory->setParent(null);
        $this->assertNull($this->campCategory->getParent());
        $this->assertNotContains($this->campCategory, $parent->getChildren());
    }

    public function testChild(): void
    {
        $this->assertEmpty($this->campCategory->getChildren());

        $child = new CampCategory('Child', 'child', 100);
        $idString = $child->getIdentifier()->toRfc4122();
        $this->campCategory->addChild($child);

        $this->assertTrue($this->campCategory->hasChild($idString));
        $this->assertSame($child, $this->campCategory->getChild($idString));
        $this->assertSame($this->campCategory, $child->getParent());
        $this->assertContains($child, $this->campCategory->getChildren());

        $this->campCategory->removeChild($child);

        $this->assertFalse($this->campCategory->hasChild($idString));
        $this->assertNull($this->campCategory->getChild($idString));
        $this->assertNull($child->getParent());
        $this->assertNotContains($child, $this->campCategory->getChildren());
    }

    public function testParentRewriting(): void
    {
        $child = new CampCategory('Child', 'child', 100);
        $child->setParent($this->campCategory);

        $childNew = new CampCategory('Child New', 'child-new', 100);
        $this->setId($childNew, $child->getId());
        $childNew->setParent($this->campCategory);

        $this->assertNull($child->getParent());
        $this->assertSame($this->campCategory, $childNew->getParent());
        $this->assertNotContains($child, $this->campCategory->getChildren());
        $this->assertContains($childNew, $this->campCategory->getChildren());
    }

    public function testChildRewriting(): void
    {
        $child = new CampCategory('Child', 'child', 100);
        $this->campCategory->addChild($child);

        $childNew = new CampCategory('Child New', 'child-new', 100);
        $this->setId($childNew, $child->getId());
        $this->campCategory->addChild($childNew);

        $this->assertNull($child->getParent());
        $this->assertSame($this->campCategory, $childNew->getParent());
        $this->assertNotContains($child, $this->campCategory->getChildren());
        $this->assertContains($childNew, $this->campCategory->getChildren());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campCategory->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campCategory->getUpdatedAt());
    }

    public function testAncestors(): void
    {
        $child1 = new CampCategory('Child', 'child', 100);
        $child1->setParent($this->campCategory);

        $child2 = new CampCategory('Child 2', 'child-2', 100);
        $child2->setParent($child1);

        $ancestors = $child2->getAncestors();

        $this->assertSame([$this->campCategory, $child1], $ancestors);
    }

    public function testPathWithUrlNames(): void
    {
        $child = new CampCategory('Child', 'child', 100);
        $child->setParent($this->campCategory);

        $this->assertSame('url-name/child', $child->getPath());
    }

    public function testPathWithHumanNames(): void
    {
        $child = new CampCategory('Child', 'child', 100);
        $child->setParent($this->campCategory);

        $this->assertSame('Name/Child', $child->getPath(true));
    }

    public function testUnsupportedRelationType(): void
    {
        $unsupportedType = new TreeNodeMock('id');

        $this->expectException(LogicException::class);
        $this->campCategory->setParent($unsupportedType);

        $this->expectException(LogicException::class);
        $this->campCategory->addChild($unsupportedType);

        $this->expectException(LogicException::class);
        $this->campCategory->removeChild($unsupportedType);
    }

    private function setId(CampCategory $campCategory, UuidV4 $id): void
    {
        $reflectionClass = new ReflectionClass($campCategory);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($campCategory, $id);
    }

    protected function setUp(): void
    {
        $this->campCategory = new CampCategory(self::NAME, self::URL_NAME, self::PRIORITY);
    }
}