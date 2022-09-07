<?php

declare(strict_types=1);

namespace Miniblog\BlogProject;

use function array_reverse;
use function passthru;

class InstallCommand
{
    /** @var string */
    private const ENGINE_PACKAGE_NAME = 'miniblog/engine';

    private string $projectDir;

    /** @var array<string, string> */
    private array $thingsToCopy;

    /** @var string[] */
    private array $dirsToMake;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;

        $engineDir = "{$this->projectDir}/vendor/" . self::ENGINE_PACKAGE_NAME;

        $this->thingsToCopy = [
            "{$engineDir}/content/." => "{$this->projectDir}/content/",
            "{$engineDir}/public/." => "{$this->projectDir}/public/",
            "{$engineDir}/config.php.dist" => "{$this->projectDir}/config.php",
        ];

        $this->dirsToMake = [
            // "{$this->projectDir}/templates",
        ];
    }

    public function up(): void
    {
        foreach ($this->thingsToCopy as $source => $destination) {
            passthru("cp --recursive --verbose {$source} {$destination}");
        }

        foreach ($this->dirsToMake as $dirToMake) {
            passthru("mkdir --parents --verbose {$dirToMake} && touch {$dirToMake}/.gitignore");
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->dirsToMake) as $dirToMake) {
            passthru("rm --recursive --force --verbose {$dirToMake}");
        }

        foreach (array_reverse($this->thingsToCopy) as $destination) {
            passthru("rm --recursive --force --verbose {$destination}");
        }
    }
}
