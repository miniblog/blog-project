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

    /** @var array<string,string> */
    private array $thingsToCopy;

    /** @var string[] */
    private array $dirsToMake;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;

        $engineDir = "{$this->projectDir}/vendor/" . self::ENGINE_PACKAGE_NAME;

        $this->thingsToCopy = [
            "{$engineDir}/bin/." => "{$this->projectDir}/bin/",
            "{$engineDir}/data/." => "{$this->projectDir}/data/",
            "{$engineDir}/public/." => "{$this->projectDir}/public/",
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

    /**
     * See https://gist.github.com/egmontkob/eb114294efbcd5adb1944c9f3cb5feda
     */
    private function createHyperlink(string $url, string $label): string
    {
        $osc = "\033]";

        $left = "{$osc}8;;";
        $right = "\033\\";

        return "{$left}{$url}{$right}{$label}{$left}{$right}";
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

        passthru("cd {$this->projectDir} && bin/console refresh");

        $linkToInstructions = $this->createHyperlink(
            'https://github.com/miniblog/engine/blob/main/doc/installation.md#quick-start',
            'the installation instructions'
        );

        $this->success("Almost there!  To finish up, check {$linkToInstructions}");
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
