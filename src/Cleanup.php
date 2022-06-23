<?php

namespace HelsingborgStad\Builder;

use Composer\Package\Package;

class Cleanup
{
    protected $package;
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function doCleanup(string $installPath)
    {
        foreach ($this->getRemovables() as $removable) {
            print "Removing $removable from $installPath\n";
            $this->removePath($removable);
        }
    }

    public function getRemovables(): array
    {
        $extra = $this->package->getExtra();
        return isset($extra['builder']['removables']) ? $extra['builder']['removables'] : [];
    }

    public function removePath(string $path)
    {
        if (is_dir($path)) {
            $directoryIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            $recursiveIterator = new \RecursiveIteratorIterator(
                $directoryIterator,
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($recursiveIterator as $file) {
                $this->remove($file->getRealPath());
            }
        }
        $this->remove($path);
    }

    /**
     * Remove file or direcotry.
     * @param string $path Path to file and folder to remove.
     * @return void
     * @codeCoverageIgnore
     */
    public function remove(string $path)
    {
        if (file_exists($path)) {
            is_dir($path) ? rmdir($path) : unlink($path);
        }
    }
}
