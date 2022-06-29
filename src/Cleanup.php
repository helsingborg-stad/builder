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

    /**
     * Cleanup alla removables.
     * @param string $installPath install path for visualization.
     * @return void
     */
    public function doCleanup(string $installPath)
    {
        $originalPath = getcwd();
        chdir($installPath);
        foreach ($this->getRemovables() as $removable) {
            print "Removing $removable from $installPath\n";
            $this->removePath($removable);
        }
        chdir($originalPath);
    }

    /**
     * Get removable files and folder from composer.json extra/builder config.
     * @return array
     */
    public function getRemovables(): array
    {
        $extra = $this->package->getExtra();
        return isset($extra['builder']['removables']) ? $extra['builder']['removables'] : [];
    }

    /**
     * Remove file or folder.
     * @param string $path Path to file or folder.
     * @return void
     */
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
