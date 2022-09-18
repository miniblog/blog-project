<?php

declare(strict_types=1);

namespace Miniblog\BlogProject;

use function array_reverse;
use function passthru;
use function strlen;

class InstallCommand
{
    /** @var string */
    private const ENGINE_PACKAGE_NAME = 'miniblog/engine';

    private string $projectDir;

    private string $configFilePathname;

    /** @var array<string, string> */
    private array $thingsToCopy;

    /** @var string[] */
    private array $dirsToMake;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->configFilePathname = "{$this->projectDir}/config.php";

        $engineDir = "{$this->projectDir}/vendor/" . self::ENGINE_PACKAGE_NAME;

        $this->thingsToCopy = [
            "{$engineDir}/content/." => "{$this->projectDir}/content/",
            "{$engineDir}/public/." => "{$this->projectDir}/public/",
            "{$engineDir}/config.php.dist" => $this->configFilePathname,
        ];

        $this->dirsToMake = [
            "{$this->projectDir}/templates",
        ];
    }

    private function writeLn(string $message): void
    {
        // phpcs:ignore
        echo "{$message}\n";
    }

    private function success(string $message = ''): void
    {
        $message = strlen($message) ? " {$message}" : '';

        $blackOnGreen = "\033[30m\033[42m";
        $noColour = "\033[0m";

        $this->writeLn("\n{$blackOnGreen}[OK]{$message}{$noColour}\n");
    }

    public function up(): void
    {
        foreach ($this->thingsToCopy as $source => $destination) {
            passthru("cp --recursive --verbose {$source} {$destination}");
        }

        foreach ($this->dirsToMake as $dirToMake) {
            passthru("mkdir --parents --verbose {$dirToMake} && touch {$dirToMake}/.gitignore");
        }

        $this->success("Almost there.  Your next step is to customise `{$this->configFilePathname}`.");
    }

    public function down(): void
    {
        foreach (array_reverse($this->dirsToMake) as $dirToMake) {
            passthru("rm --recursive --force --verbose {$dirToMake}");
        }

        foreach (array_reverse($this->thingsToCopy) as $destination) {
            passthru("rm --recursive --force --verbose {$destination}");
        }

        $this->success();
    }
}
