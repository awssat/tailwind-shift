<?php

namespace Awssat\TailwindShift\Test;

use Awssat\TailwindShift\SearchAndReplace;
use PHPUnit\Framework\TestCase;

class SearchAndReplaceTest extends TestCase
{
    /** @var \Awssat\TailwindShift\SearchAndReplace */
    protected $searchAndReplace;

    protected function setUp(): void
    {
        $this->searchAndReplace = new searchAndReplace();
    }

    /** @test */
    public function it_respects_repeated_regex_search_param()
    {
        $this->searchAndReplace
            ->setContent('<tag class="life-is-sad"></tag>')
            ->perform(
                '{regex_string}-{regex_string}-sad',
                '{regex_string}-{regex_string}-fun',
                SearchAndReplace::INSIDE_CLASSE_PROP
            );

        $this->assertEquals(
            '<tag class="life-is-fun"></tag>',
            $this->searchAndReplace->get()
        );
    }

    /** @test */
    public function it_respects_repeated_regex_replace_param()
    {
        $this->searchAndReplace
            ->setContent('<tag class="life-is-sad"></tag>')
            ->perform(
                'life-is-{regex_string}',
                '{regex_string}-is-{regex_string}',
                SearchAndReplace::INSIDE_CLASSE_PROP
            );

        $this->assertEquals(
            '<tag class="sad-is-sad"></tag>',
            $this->searchAndReplace->get()
        );
    }
}
