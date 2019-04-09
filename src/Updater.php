<?php

namespace Awssat\TailwindShift;

class Updater
{
    /** @var \Awssat\TailwindShift\SearchAndReplace */
    protected $searchAndReplace;

    protected $currentFileExtension;

    /**
     * initiate the converter class.
     *
     * @param string $content
     *
     * @return Updater
     */
    public function __construct($content = null)
    {
        $this->searchAndReplace = new SearchAndReplace($content);

        return $this;
    }

    /**
     * Set the content.
     *
     * @param string $content
     *
     * @return Updater
     */
    public function setContent(string $content)
    {
        $this->searchAndReplace->setContent($content);

        return $this;
    }

    /**
     * Set file extension.
     *
     * @param string $extension
     *
     * @return Updater
     */
    public function setFileExtension(string $extension)
    {
        $this->currentFileExtension = $extension;

        return $this;
    }

    /**
     * Run the conversion.
     *
     * @return Updater
     */
    public function convert()
    {
        $this->convertToVersion1_0();

        return $this;
    }

    /**
     * Get the converted content.
     *
     * @return string
     */
    public function get()
    {
        return $this->searchAndReplace->get();
    }

    /**
     * Get the number of comitted changes.
     *
     * @return int
     */
    public function changes()
    {
        return $this->searchAndReplace->changes();
    }

    /**
     * Convert to 1.0.
     *
     * @return null
     */
    protected function convertToVersion1_0()
    {
        $isCSSfile = false;

        if (in_array($this->currentFileExtension, ['css', 'scss'])) {

            $isCSSfile = true;

            $cssChanges = [
                    '@tailwind\s*preflight;'                         => '@tailwind base;',
                    '\@import\s*("|\')tailwindcss\/preflight("|\');' => '@import "tailwindcss/base";',
                    'config\('                                       => 'theme(',
            ];

            foreach ($cssChanges as $old => $new) {
                $this->searchAndReplace->perform($old, $new, SearchAndReplace::NO_ESCAPE);
            }

        }

        $classes = [
                'list-reset'     => 'list-none p-0',
                'pin-none'       => 'inset-auto',
                'pin'            => 'inset-0',
                'pin-y'          => 'inset-y-0',
                'pin-x'          => 'inset-x-0',
                'pin-t'          => 'top-0',
                'pin-r'          => 'right-0',
                'pin-l'          => 'left-0',
                'pin-b'          => 'bottom-0',
                'roman'          => 'not-italic',
                'flex-no-grow'   => 'flex-grow-0',
                'flex-no-shrink' => 'flex-shrink-0',
                'flex-no-shrink' => 'flex-shrink-0',
                'no-underline'   => '',
                'tracking-tight' => 'tracking-tighter',
                'tracking-wide'  => 'tracking-wider',

                //colors
                '{regex_string}-grey'                    => '{regex_string}-grey-500',
                '{regex_string}-red'                     => '{regex_string}-red-500',
                '{regex_string}-orange'                  => '{regex_string}-orange-500',
                '{regex_string}-yellow'                  => '{regex_string}-yellow-500',
                '{regex_string}-green'                   => '{regex_string}-green-500',
                '{regex_string}-teal'                    => '{regex_string}-teal-500',
                '{regex_string}-blue'                    => '{regex_string}-blue-500',
                '{regex_string}-indigo'                  => '{regex_string}-indigo-500',
                '{regex_string}-purple'                  => '{regex_string}-purple-500',
                '{regex_string}-pink'                    => '{regex_string}-pink-500',
                '{regex_string}-{regex_string}-darkest'  => '{regex_string}-{regex_string}-900',
                '{regex_string}-{regex_string}-darker'   => '{regex_string}-{regex_string}-800',
                '{regex_string}-{regex_string}-dark'     => '{regex_string}-{regex_string}-600',
                '{regex_string}-{regex_string}-light'    => '{regex_string}-{regex_string}-400',
                '{regex_string}-{regex_string}-lighter'  => '{regex_string}-{regex_string}-200',
                '{regex_string}-{regex_string}-lightest' => '{regex_string}-{regex_string}-100',
        ];

        $htmlTags = [
                '<h1>' => '<h1 class="text-xl font-semibold">',
                '<ul>' => '<ul class="list-disc pl-4">',
        ];

        foreach ($classes as $beforeClass => $afterClass) {
            $this->searchAndReplace->perform(
                    ($isCSSfile ? '.' : '') . $beforeClass,
                    ($isCSSfile ? '.' : '') . $afterClass,
                    $isCSSfile ? SearchAndReplace::AFTER_APPLY_DIRECTIVE : SearchAndReplace::INSIDE_CLASSE_PROP
            );
        }

        if($isCSSfile) {
            return;
        }

        foreach ($htmlTags as $beforeTag => $afterTag) {
            $this->searchAndReplace->perform($beforeTag, $afterTag);
        }
    }
}
