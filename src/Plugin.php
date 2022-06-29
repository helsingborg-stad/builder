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
use Composer\Package\Package;
use Composer\Script\Event;

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
                ['install']
            ],
            'post-package-update' => [
                ['update']
            ],
            'post-update-cmd' => [
                ['rootCleanup']
            ]
        );
    }

    public function update(PackageEvent $packageEvent)
    {
        $package = $packageEvent->getOperation()->getTargetPackage();
        $this->builder($package);
    }

    public function install(PackageEvent $packageEvent)
    {
        $package = $packageEvent->getOperation()->getPackage();
        $this->builder($package);
    }

    public function builder(Package $package)
    {
        $installedPackageExtra = $package->getExtra();
        if (isset($installedPackageExtra['builder'])) {
            $currentPackageExtra = $this->composer->getPackage()->getExtra();
            $doCleanup = isset($currentPackageExtra['builder']['cleanup'])
                && $currentPackageExtra['builder']['cleanup'] == "true";

            $installPath = $this->composer->getInstallationManager()->getInstallPath($package);

            $builder = new Builder($package);
            $builder->runCommands($installPath);

            if ($doCleanup) {
                $cleanup = new Cleanup($package);
                $cleanup->doCleanup($installPath);
            }
        }
    }

    public function rootCleanup(Event $event)
    {
        $currentPackage = $this->composer->getPackage();
        $currentPackageExtra = $currentPackage->getExtra();
        $doCleanup = isset($currentPackageExtra['builder']['cleanup'])
            && $currentPackageExtra['builder']['cleanup'] == "true";

        if ($doCleanup) {
            $cleanup = new Cleanup($currentPackage);
            $cleanup->doCleanup(getcwd());
        }
    }
}
