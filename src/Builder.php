<?php

namespace HelsingborgStad\Builder;

use Composer\Package\Package;

class Builder
{
    protected $package;
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Get build commands composer.json extra/builder config.
     * @param $installPath Install path for visualization.
     * @return void
     */
    public function runCommands(string $installPath)
    {
        $originalPath = getcwd();
        chdir($installPath);
        foreach ($this->getBuildCommands() as $buildCommand) {
            print "---- Running build command '$buildCommand' for $installPath. ----\n";
            $timeStart = microtime(true);
            $exitCode = $this->executeCommand($buildCommand);

            if ($exitCode > 0) {
                $this->quit($exitCode);
            }
            $buildTime = round(microtime(true) - $timeStart);
            print "---- Done build command '$buildCommand' for $installPath.  Build time: $buildTime seconds. ----\n";
        }
        chdir($originalPath);
    }

    /**
     * Get build commands composer.json extra/builder config.
     * @return array
     */
    public function getBuildCommands(): array
    {
        $extra = $this->package->getExtra();
        return isset($extra['builder']['commands']) ? $extra['builder']['commands'] : [];
    }

    /**
     * Better shell script execution with live output to STDOUT and status code return.
     * @param  string $command Command to execute in shell.
     * @return int             Exit code.
     */
    public function executeCommand(string $command): int
    {
        $fullCommand = '';
        if ($this->isWindowsOS()) {
            $fullCommand = "cmd /v:on /c \"$command 2>&1 & echo Exit status : !ErrorLevel!\"";
        } else {
            $fullCommand = "$command 2>&1 ; echo Exit status : $?";
        }

        $proc = popen($fullCommand, 'r');

        $liveOutput = '';
        $completeOutput = '';

        while (!feof($proc)) {
            $liveOutput     = fread($proc, 4096);
            $completeOutput = $completeOutput . $liveOutput;
            print $liveOutput;
            @ flush();
        }

        pclose($proc);

        // Get exit status.
        preg_match('/[0-9]+$/', $completeOutput, $matches);

        // Return exit status.
        return intval($matches[0]);
    }

    /**
     * Check if using windows.
     * @return boolean
     * @codeCoverageIgnore
     */
    public function isWindowsOS()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Quit helper.
     * @param int $exitCode
     * @return void
     * @codeCoverageIgnore
     */
    public function quit(int $exitCode)
    {
        exit($exitCode);
    }
}
