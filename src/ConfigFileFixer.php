<?php

namespace Awssat\TailwindShift;

class ConfigFileFixer
{

    /** @var \Awssat\TailwindShift\SearchAndReplace */
    protected $searchAndReplace;

    /**
     * initiate the converter class.
     *
     * @param string $content
     *
     * @return ConfigFileFixer
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
     * @return ConfigFileFixer
     */
    public function setContent(string $content)
    {
        $this->searchAndReplace->setContent($content);

        return $this;
    }


    /**
     * Run the fixer
     *
     * @return ConfigFileFixer
     */
    public function fix()
    {
        $this->fixForVersion1_0();

        return $this;
    }

    /**
     * Get the fixed content.
     *
     * @return string
     */
    public function get()
    {
        return $this->searchAndReplace->get();
    }

    /**
     * fix it for version 1.0
     *
     * @return null
     */
    protected function fixForVersion1_0()
    {
        //1: get options{} values and add em to root of 'module.exports' and add last value as theme: {..
        preg_match('/options:\s*\{([^\}]+)\},?/', $this->searchAndReplace->get(), $match);
        $options = $match[1] ?? '';
        $updatedToThemeKey = "module.exports = {\n" . $options . "\n\n\ttheme: {\n";

        $this->searchAndReplace->perform('options:\s*\{([^\}]+)\},?', '', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('module.exports\s*=\s*\{', $updatedToThemeKey, SearchAndReplace::NO_ESCAPE);

        //2: add closing } before modules: {
         //3: change modules: {  to variants: {
        $this->searchAndReplace->perform('modules\:\s*\{',"},\n\nvariants: {", SearchAndReplace::NO_ESCAPE);

        //4: updates keys inside theme: { to new names.
        //5: updates keys inside variants: { to new names.
        $newNames = [
            'fonts\:\s*' => "fontFamily: ",
            'textSizes\:\s*' => "fontSize: ",
            'fontWeights\:\s*' => "fontWeight: ",
            'leading\:\s*' => "lineHeight: ",
            'tracking\:\s*' => "letterSpacing: ",
            'textColors\:\s*' => "textColor: ",
            'backgroundColors\:\s*' => "backgroundColor: ",
            'borderWidths\:\s*' => "borderWidth: ",
            'borderColors\:\s*' => "borderColor: ",
            'shadows\:\s*' => "boxShadow: ",
            'shadows\:\s*' => "boxShadow: ",
            'svgFill\:\s*' => "fill: ",
            'svgStroke\:\s*' => "stroke: ",
            'lists\:\s*\[\'{regex_string}\'\]' => "listStylePosition: ['{regex_string}'],\nlistStyleType: ['{regex_string}']",
            'position\:\s*\[\'{regex_string}\'\]' => "position: ['{regex_string}'],\ninset: ['{regex_string}']",
            'whitespace\:\s*\[\'{regex_string}\'\]' => "whitespace: ['{regex_string}'],\nwordBreak: ['{regex_string}']",
            'textStyle\:\s*\[\'{regex_string}\'\]' => "fontStyle: ['{regex_string}'],\nfontSmoothing: ['{regex_string}']" .
                                                    ",\ntextDecoration: ['{regex_string}'],\ntextTransform: ['{regex_string}']",
            'flexbox\:\s*\[\'{regex_string}\'\]' => "flexDirection: ['{regex_string}'],\nflexWrap: ['{regex_string}']" .
                                                    ",\nalignItems: ['{regex_string}'],\nalignSelf: ['{regex_string}']" .
                                                    ",\njustifyContent: ['{regex_string}'],\nalignContent: ['{regex_string}']" .
                                                    ",\nflex: ['{regex_string}'],\nflexGrow: ['{regex_string}']" .
                                                    ",\nflexShrink: ['{regex_string}']",
        ];

        foreach ($newNames as $old => $new) {
            $this->searchAndReplace->perform($old, $new, SearchAndReplace::NO_ESCAPE);
        }

        //6: add corePlugins: { to root.
         //7: remove container from plugins and add it to corePlugins
        preg_match('/require\(\'tailwindcss\/plugins\/container\'\)\(\{([^\}]+)\}\)/', $this->searchAndReplace->get(), $match);
        $containerOptions = $match[1] ?? '';

        $this->searchAndReplace->perform('require\(\'tailwindcss\/plugins\/container\'\)\(\{([^\}]+)\}\),?', '', SearchAndReplace::NO_ESCAPE);
        $corePlugins = "corePlugins: {". (empty($containerOptions) ? "\ncontainer: false," : '') . "\n}, \nplugins: [";

        $this->searchAndReplace->perform('plugins:\s*\[', $corePlugins, SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('theme:\s*\{', "theme: \n{\ncontainer: {\n" . $containerOptions . "\n},\n", SearchAndReplace::NO_ESCAPE);
        
        //8: fix colors
        $colors = "\n  transparent: 'transparent',\n\n  black: '#000',\n  white: '#fff'," . 
        "\n  gray: {\n\t100: '#f7fafc',\n\t200: '#edf2f7',\n\t300: '#e2e8f0'," .
        "\n\t400: '#cbd5e0',\n\t500: '#a0aec0',\n\t600: '#718096',\n\t700: '#4a5568'," .
        "\n\t800: '#2d3748',\n\t900: '#1a202c',\n\t},\n\t" .
        "\n  red: {\n\t100: '#fff5f5',\n\t200: '#fed7d7',\n\t300: '#feb2b2',\n\t400: '#fc8181'," .
        "\n\t500: '#f56565',\n\t600: '#e53e3e',\n\t700: '#c53030',\n\t800: '#9b2c2c',".
        "\n\t900: '#742a2a',\n\t},\n\t" .
        "\n   orange: {\n\t100: '#fffaf0',\n\t200: '#feebc8',\n\t300: '#fbd38d'," .
        "\n\t400: '#f6ad55',\n\t500: '#ed8936',\n\t600: '#dd6b20',\n\t700: '#c05621'," .
        "\n\t800: '#9c4221',\n\t900: '#7b341e',\n\t},\n\t" . 
        "\n  yellow: {\n\t100: '#fffff0',\n\t200: '#fefcbf',\n\t300: '#faf089'," . 
        "\n\t400: '#f6e05e',\n\t500: '#ecc94b',\n\t600: '#d69e2e',\n\t700: '#b7791f'," . 
        "\n\t800: '#975a16',\n\t900: '#744210',\n\t},\n\t" . 
        "\n  green: {\n\t100: '#f0fff4',\n\t200: '#c6f6d5',\n\t300: '#9ae6b4'," .
        "\n\t400: '#68d391',\n\t500: '#48bb78',\n\t600: '#38a169',\n\t700: '#2f855a'," . 
        "\n\t800: '#276749',\n\t900: '#22543d',\n\t}," .
        "\n  teal: {\n\t100: '#e6fffa',\n\t200: '#b2f5ea',\n\t300: '#81e6d9'," . 
        "\n\t400: '#4fd1c5',\n\t500: '#38b2ac',\n\t600: '#319795',\n\t700: '#2c7a7b'," . 
        "\n\t800: '#285e61',\n\t900: '#234e52',\n\t},\n\t" .
        "\n  blue: {\n\t100: '#ebf8ff',\n\t200: '#bee3f8',\n\t300: '#90cdf4'," .
        "\n\t400: '#63b3ed',\n\t500: '#4299e1',\n\t600: '#3182ce',\n\t700: '#2b6cb0', " .
        "\n\t800: '#2c5282',\n\t900: '#2a4365',\n\t}," . 
        "\n  indigo: {\n\t100: '#ebf4ff',\n\t200: '#c3dafe',\n\t300: '#a3bffa'," . 
        "\n\t400: '#7f9cf5',\n\t500: '#667eea',\n\t600: '#5a67d8',\n\t700: '#4c51bf'," .
        "\n\t800: '#434190',\n\t900: '#3c366b',\n\t},\n\t" . 
        "\n  purple: {\n\t100: '#faf5ff',\n\t200: '#e9d8fd',\n\t300: '#d6bcfa'," . 
        "\n\t400: '#b794f4',\n\t500: '#9f7aea',\n\t600: '#805ad5',\n\t700: '#6b46c1'," . 
        "\n\t800: '#553c9a',\n\t900: '#44337a',\n\t},\n\t" . 
        "\n  pink: {\n\t100: '#fff5f7',\n\t200: '#fed7e2',\n\t300: '#fbb6ce'," . 
        "\n\t400: '#f687b3',\n\t500: '#ed64a6',\n\t600: '#d53f8c',\n\t700: '#b83280'," .
        "\n\t800: '#97266d',\n\t900: '#702459',\n\t},\n";

        $this->searchAndReplace->perform('let colors\s*=\s*\{([^\}]+)\}', '', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('\s+colors:\s*colors', '  colors: {'.$colors.'}', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('backgroundColor:\s*colors', 'backgroundColor: theme => theme(\'colors\')', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('textColor:\s*colors', 'textColor: theme => theme(\'colors\')', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('borderColor:\s*g([^\n]+)', "borderColor: theme => {\nreturn global.Object.assign({ default: theme('colors.gray.300', 'currentColor') }, theme('colors'))\n},", SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('require\(\'tailwindcss\/defaultConfig\'\)\(\)', "require('tailwindcss/defaultConfig')", SearchAndReplace::NO_ESCAPE);
    }
}
