<?php

namespace HelsingborgStad\Builder;

use PHPUnit\Framework\TestCase;
use Composer\Package\Package;

/**
 * @covers \HelsingborgStad\Builder\Cleanup
 */
class CleanupTest extends TestCase
{
    public function testGetRemovablesWithoutRemovablesPresent()
    {
        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder']);
        $cleanup = new Cleanup($package);
        $this->assertSame($cleanup->getRemovables(), []);
    }

    public function testGetRemovablesWithRemovablesPresent()
    {
        $removable = 'somethingToRemove';
        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder' => ['removables' => [0 => $removable]]]);
        $cleanup = new Cleanup($package);
        $this->assertSame($cleanup->getRemovables(), [0 => $removable]);
    }

    public function testDoCleanup()
    {
        $removable = 'somethingToRemove';
        $testPath = 'test/path';

        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder' => ['removables' => [0 => $removable]]]);

        $cleanup = $this->getMockBuilder(Cleanup::class)
                        ->setConstructorArgs([$package])
                        ->onlyMethods(['removePath'])
                        ->getMock();
        $cleanup->expects($this->exactly(1))
                ->method('removePath');

        $this->expectOutputString("Removing $removable from $testPath\n");

        $cleanup->doCleanup($testPath);
    }

    public function testRemovePathFile()
    {
        $testPath = 'test/path';

        $package = $this->createMock(Package::class);

        $cleanup = $this->getMockBuilder(Cleanup::class)
        ->setConstructorArgs([$package])
        ->onlyMethods(['remove'])
        ->getMock();

        $cleanup->expects($this->exactly(1))
                ->method('remove');

        $cleanup->removePath($testPath);
    }

    public function testRemovePathDir()
    {
        $testPath = __DIR__ . '/testassets';

        $package = $this->createMock(Package::class);

        $cleanup = $this->getMockBuilder(Cleanup::class)
        ->setConstructorArgs([$package])
        ->onlyMethods(['remove'])
        ->getMock();

        $cleanup->expects($this->exactly(3))
                ->method('remove');

        $cleanup->removePath($testPath);
    }
}
