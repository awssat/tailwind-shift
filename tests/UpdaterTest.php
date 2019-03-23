<?php

namespace Awssat\TailwindShift\Test;

use Awssat\TailwindShift\Updater;
use PHPUnit\Framework\TestCase;

class UpdaterTest extends TestCase
{
    /** @var \Awssat\TailwindShift\Updater */
    protected $updater;

    protected function setUp()
    {
        $this->updater = new Updater();
    }

    /** @test */
    public function it_return_output()
    {
        $this->assertEquals(
            '<tag class="text-grey-500"></tag>',
            $this->updater->setContent('<tag class="text-grey"></tag>')->convert()->get()
        );
    }
}
