<?php

namespace HelsingborgStad\Builder;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;
use Composer\Installer\LibraryInstaller;
use HelsingborgStad\Builder\Cleanup;
use HelsingborgStad\Builder\Builder;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            'post-package-install' => [
                ['builder']
            ]
        );
    }

    public function builder(PackageEvent $packageEvent)
    {
        $package = $packageEvent->getOperation()->getPackage();
        $installedPackageExtra = $package->getExtra();
        if (isset($installedPackageExtra['builder'])) {
            $currentPackageExtra = $this->composer->getPackage()->getExtra();
            $doCleanup = isset($currentPackageExtra['builder']['cleanup'])
                && $currentPackageExtra['builder']['cleanup'] == true;

            $installPath = $this->composer->getInstallationManager()->getInstallPath($package);

            chdir($installPath);

            $builder = new Builder($package);
            $builder->runCommands($installPath);

            if ($doCleanup) {
                $cleanup = new Cleanup($package);
                $cleanup->doCleanup($installPath);
            }
        }
    }
}
