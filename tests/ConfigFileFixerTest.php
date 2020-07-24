<?php

namespace Awssat\TailwindShift\Test;

use Awssat\TailwindShift\ConfigFileFixer;
use PHPUnit\Framework\TestCase;

class ConfigFileFixerTest extends TestCase
{
    /** @var \Awssat\TailwindShift\ConfigFileFixer */
    protected $configFixer;

    protected function setUp(): void
    {
        //TODO: phpUnit 8 incompatibility, void error.
        $this->configFixer = new ConfigFileFixer();
    }

    /** @test */
    public function it_return_output()
    {
        $this->assertStringContainsString(
            'theme:',
            $this->configFixer->setContent('let colors = {}; 
            module.exports = {
                colors: colors,
            }')->fix()->get()
        );
    }
}
