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
        //TODO: phpUnit 8 incompatibility, void error.
        $this->updater = new Updater();
    }

    /** @test */
    public function it_return_output()
    {
        $this->assertEquals(
            '<tag class="text-gray-500"></tag>',
            $this->updater->setContent('<tag class="text-grey"></tag>')->convert()->get()
        );
    }

    /** @test */
    public function it_convert_variants()
    {
        $this->assertEquals(
            '<tag class="hover:text-gray-500"></tag>',
            $this->updater->setContent('<tag class="hover:text-grey"></tag>')->convert()->get()
        );

        $this->assertEquals(
            '<tag class="bg-white  focus:w-100 custom-variant:text-gray-500"></tag>',
            $this->updater->setContent('<tag class="bg-white hover:no-underline focus:w-100 custom-variant:text-grey"></tag>')->convert()->get()
        );
    }

    /** @test */
    public function it_convert_multiline_classes()
    {
        $this->assertContains(
            'border-teal-500',
            $this->updater->setContent('<component
            class="group cursor-pointer bg-grey-dark"
            :class="{
                    \'border-l border-grey-light\': !isResponse,
                    \'ml-8 md:ml-16 border-l border-teal\': isResponse
                    }">')->convert()->get()
        );
    }
}
