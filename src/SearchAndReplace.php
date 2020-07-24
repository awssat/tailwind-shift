<?php

namespace Awssat\TailwindShift;

class SearchAndReplace
{
    protected $changes = 0;

    protected $lastSearches = [];

    protected $givenContent = '';

    protected $escape = true;

    protected $inlineCSS = false;

    protected $afterApply = false;

    /**
     * initiate the converter class.
     *
     * @param string $content
     *
     * @return SearchAndReplace
     */
    public function __construct($content = null)
    {
        $this->givenContent = $content;

        return $this;
    }

    public function shouldEscape($toggle): self
    {
        $this->escape = $toggle;

        return $this;
    }

    public function isInlineCSS($toggle)
    {
        $this->inlineCSS = $toggle;
        if ($toggle) {
            $this->afterApply = false;
        }
        return $this;
    }

    public function isAfterApply($toggle)
    {
        $this->afterApply = $toggle;
        if ($toggle) {
            $this->inlineCSS = false;
        }
        return $this;
    }

    /**
     * Get the converted content.
     *
     * @return string
     */
    public function get()
    {
        return $this->givenContent;
    }

    /**
     * Set the content.
     *
     * @param string $content
     *
     * @return SearchAndReplace
     */
    public function setContent(string $content)
    {
        $this->givenContent = $content;

        return $this;
    }

    /**
     * Get the number of committed changes.
     *
     * @return int
     */
    public function changes()
    {
        return $this->changes;
    }

    /**
     * search for a word in the last searches.
     *
     * @param string $searchFor
     * @param int    $limitLast limit the search to last $limitLast items
     *
     * @return bool
     */
    protected function isInLastSearches(string $searchFor, $limitLast = 0)
    {
        $i = 0;

        foreach ($this->lastSearches as $search) {
            if (strpos($search, $searchFor) !== false) {
                return true;
            }

            if ($i++ >= $limitLast && $limitLast > 0) {
                return false;
            }
        }

        return false;
    }

    protected function addToLastSearches($search)
    {
        $this->changes++;

        $search = stripslashes($search);

        if ($this->isInLastSearches($search)) {
            return;
        }

        $this->lastSearches[] = $search;

        if (count($this->lastSearches) >= 50) {
            array_shift($this->lastSearches);
        }
    }

    /**
     * Search the given content and replace.
     *
     * @param string $search
     * @param string $replace
     */
    public function perform($search, $replace)
    {
        $currentContent = $this->givenContent;

        if ($replace instanceof \Closure) {
            $callableReplace = \Closure::bind($replace, $this, self::class);
            $replace = $callableReplace();
        }

        $regexStart = $this->afterApply ?  '(?<start>@apply\s*.*?)' : '(?<start>\s*)';
        $regexEnd = $this->afterApply ? '(?<end>.*?[;\n])' : '(?<end>\s*)';

        if($this->inlineCSS) {
            $regexStart = '(?<start>class\s*=\s*(?<quotation>["\'])((?!\k<quotation>).)*)';
            $regexEnd = '(?<end>((?!\k<quotation>).)*\k<quotation>)';
        }

        if ($this->escape) {
            $search = preg_quote($search);
        }

        $currentSubstitute = 0;

        while (true) {
            if (
                strpos($search, 'regex_string') !== false
                || strpos($search, 'regex_number') !== false
                || strpos($search, 'regex_line') !== false
            ) {
                $currentSubstitute++;
                foreach (['regex_string' => '[a-zA-Z0-9]+', 'regex_number' => '[0-9]+', 'regex_line' => '[^\n]+'] as $regexName => $regexValue) {
                    $regexMatchCount = preg_match_all('/\\\\?\{'.$regexName.'\\\\?\}/', $search);
                    $search = preg_replace('/\\\\?\{'.$regexName.'\\\\?\}/', '(?<'.substr($regexName, 6).'_'.$currentSubstitute.'>'.$regexValue.')', $search, 1);
                    $replace = preg_replace('/\\\\?\{'.$regexName.'\\\\?\}/', '${'.substr($regexName, 6).'_'.$currentSubstitute.'}', $replace, $regexMatchCount > 1 ? 1 : -1);
                }
                continue;
            }

            break;
        }

        //class=" given given-md something-given-md"
        $this->givenContent = preg_replace_callback(
            '/'.$regexStart.'(?<given>(?<![\-_.\w\d])'.$search.'(?![\-_.\w\d]))'.$regexEnd.'/is',
            function ($match) use ($replace) {
                $replace = preg_replace_callback('/\$\{(number|string|line)_(\d+)\}/', function ($m) use ($match) {
                    return $match[$m[1].'_'.$m[2]];
                }, $replace);

                return $match['start'].$replace.$match['end'];
            },
            $this->givenContent
        );

        if (strcmp($currentContent, $this->givenContent) !== 0) {
            $this->changes++;

            $this->lastSearches[] = stripslashes($search);

            if (count($this->lastSearches) >= 10) {
                $this->lastSearches = array_slice($this->lastSearches, -10, 10, true);
            }
        }

        return $this;
    }
}
