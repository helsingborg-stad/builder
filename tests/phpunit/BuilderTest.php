<?php

namespace HelsingborgStad\Builder;

use PHPUnit\Framework\TestCase;
use Composer\Package\Package;

/**
 * @covers \HelsingborgStad\Builder\Builder
 */
class BuilderTest extends TestCase
{
    public function testGetBuildCommandsWithoutRemovablesPresent()
    {
        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder']);
        $builder = new Builder($package);
        $this->assertSame($builder->getBuildCommands(), []);
    }

    public function testGetBuildCommandsWithRemovablesPresent()
    {
        $testCommand = 'testcommand';
        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder' => ['commands' => [0 => $testCommand]]]);
        $builder = new Builder($package);
        $this->assertSame($builder->getBuildCommands(), [0 => $testCommand]);
    }

    public function testRunCommandsErrorCodeNotZero()
    {
        $testCommand = 'testcommand';
        $testPath = '/testpath';

        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder' => ['commands' => [0 => $testCommand]]]);

        $builder = $this->getMockBuilder(Builder::class)
                        ->setConstructorArgs([$package])
                        ->onlyMethods(['quit', 'executeCommand'])
                        ->getMock();

        $builder->method('executeCommand')
                        ->willReturn(1);

        $builder->expects($this->exactly(1))
                ->method('quit');

        $outputString = "---- Running build command '$testCommand' for $testPath. ----\n";
        $outputString .= "---- Done build command '$testCommand' for $testPath.  Build time: 0 seconds. ----\n";
        $this->expectOutputString($outputString);

        $builder->runCommands($testPath);
    }

    public function testRunCommandsMultiple()
    {
        $testCommand = 'testcommand';
        $testPath = '/testpath';

        $package = $this->createStub(Package::class);
        $package->method('getExtra')
                ->willReturn(['builder' => ['commands' => [$testCommand, $testCommand]]]);

        $builder = $this->getMockBuilder(Builder::class)
                        ->setConstructorArgs([$package])
                        ->onlyMethods(['quit', 'executeCommand'])
                        ->getMock();

        $builder->method('executeCommand')
                        ->willReturn(0);

        $builder->expects($this->exactly(2))
                ->method('executeCommand');

        $outputString = "---- Running build command '$testCommand' for $testPath. ----\n";
        $outputString .= "---- Done build command '$testCommand' for $testPath.  Build time: 0 seconds. ----\n";
        $outputString .= "---- Running build command '$testCommand' for $testPath. ----\n";
        $outputString .= "---- Done build command '$testCommand' for $testPath.  Build time: 0 seconds. ----\n";
        $this->expectOutputString($outputString);

        $builder->runCommands($testPath);
    }
}
