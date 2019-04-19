<?php

namespace Awssat\TailwindShift;

class SearchAndReplace
{
    protected $changes = 0;

    protected $lastSearches = [];

    protected $givenContent = '';

    const INSIDE_CLASSE_PROP = 1;
    const NO_ESCAPE = 2;
    const AFTER_APPLY_DIRECTIVE = 4;

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
     * Get the number of comitted changes.
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

    /**
     * Search the given content and replace.
     *
     * @param string $search
     * @param string $replace
     *
     * @return null
     */
    public function perform($search, $replace, $options = null)
    {
        $currentContent = $this->givenContent;

        if ($options & self::INSIDE_CLASSE_PROP) {
            $regexStart = '(?<start>class\s*=\s*["\'].*?)';
            $regexEnd = '(?<end>.*?["\'])';
        } elseif ($options & self::AFTER_APPLY_DIRECTIVE) {
            $regexStart = '(?<start>@apply\s*.*?)';
            $regexEnd = '(?<end>.*?[;\n])';
        } else {
            $regexStart = '(?<start>\s*)';
            $regexEnd = '(?<end>\s*)';
        }

        if ($options ^ self::NO_ESCAPE) {
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
                foreach (['regex_string'=> '[a-zA-Z0-9]+', 'regex_number' => '[0-9]+', 'regex_line' => '[^\n]+'] as $regeName => $regexValue) {
                    $search = preg_replace('/\\\\?\{'.$regeName.'\\\\?\}/', '(?<'.substr($regeName, 6).'_'.$currentSubstitute.'>'.$regexValue.')', $search, 1);
                    $replace = preg_replace('/\\\\?\{'.$regeName.'\\\\?\}/', '${'.substr($regeName, 6).'_'.$currentSubstitute.'}', $replace);
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
    }
}
