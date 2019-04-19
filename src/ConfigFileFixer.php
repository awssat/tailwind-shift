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
     * Run the fixer.
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
     * fix it for version 1.0.
     *
     * @return null
     */
    protected function fixForVersion1_0()
    {
        //1: get options{} values and add em to root of 'module.exports' and add last value as theme: {..
        preg_match('/options:\s*\{([^\}]+)\},?/', $this->searchAndReplace->get(), $match);
        $options = $match[1] ?? '';
        $updatedToThemeKey = "module.exports = {\n".$options."\n\ntheme: {\n";

        $this->searchAndReplace->perform('options:\s*\{([^\}]+)\},?', '', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('module.exports\s*=\s*\{', $updatedToThemeKey, SearchAndReplace::NO_ESCAPE);

        //2: add closing } before modules: {
        //3: change modules: {  to variants: {
        $this->searchAndReplace->perform('modules\:\s*\{', "},\n\nvariants: {", SearchAndReplace::NO_ESCAPE);

        //4: updates keys inside theme: { to new names.
        //5: updates keys inside variants: { to new names.
        $newNames = [
            'fonts\:\s*'                            => 'fontFamily: ',
            'textSizes\:\s*'                        => 'fontSize: ',
            'fontWeights\:\s*'                      => 'fontWeight: ',
            'leading\:\s*'                          => 'lineHeight: ',
            'tracking\:\s*'                         => 'letterSpacing: ',
            'textColors\:\s*'                       => 'textColor: ',
            'backgroundColors\:\s*'                 => 'backgroundColor: ',
            'borderWidths\:\s*'                     => 'borderWidth: ',
            'borderColors\:\s*'                     => 'borderColor: ',
            'shadows\:\s*'                          => 'boxShadow: ',
            'shadows\:\s*'                          => 'boxShadow: ',
            'svgFill\:\s*'                          => 'fill: ',
            'svgStroke\:\s*'                        => 'stroke: ',
            'lists\:{regex_line}'      => "listStylePosition:{regex_line}\nlistStyleType:{regex_line}",
            'position\:{regex_line}'   => "position:{regex_line}\ninset:{regex_line}",
            'whitespace\:{regex_line}' => "whitespace:{regex_line}\nwordBreak:{regex_line}",
            'textStyle\:{regex_line}'  => "fontStyle:{regex_line}\nfontSmoothing:{regex_line}".
                                                    "\ntextDecoration:{regex_line}\ntextTransform:{regex_line}",
            'flexbox\:{regex_line}' => "flexDirection:{regex_line}\nflexWrap:{regex_line}".
                                                    "\nalignItems:{regex_line}\nalignSelf:{regex_line}".
                                                    "\njustifyContent:{regex_line}\nalignContent:{regex_line}".
                                                    "\nflex:{regex_line}\nflexGrow:{regex_line}".
                                                    "\nflexShrink:{regex_line}",
        ];

        foreach ($newNames as $old => $new) {
            $this->searchAndReplace->perform($old, $new, SearchAndReplace::NO_ESCAPE);
        }

        //6: add corePlugins: { to root.
        //7: remove container from plugins and add it to corePlugins
        preg_match('/require\(\'tailwindcss\/plugins\/container\'\)\(\{([^\}]+)\}\)/', $this->searchAndReplace->get(), $match);
        $containerOptions = $match[1] ?? '';

        $this->searchAndReplace->perform('require\(\'tailwindcss\/plugins\/container\'\)\(\{([^\}]+)\}\),?', '', SearchAndReplace::NO_ESCAPE);
        $corePlugins = 'corePlugins: {'.(empty($containerOptions) ? "\ncontainer: false," : '')."\n}, \nplugins: [";

        $this->searchAndReplace->perform('plugins:\s*\[', $corePlugins, SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('theme:\s*\{', "theme: \n{\ncontainer: {\n".$containerOptions."\n},\n", SearchAndReplace::NO_ESCAPE);

        //8: fix colors
        $colors = "\n  transparent: 'transparent',\n\n  black: '#000',\n  white: '#fff',".
        "\n  gray: {\n100: '#f7fafc',\n200: '#edf2f7',\n300: '#e2e8f0',".
        "\n400: '#cbd5e0',\n500: '#a0aec0',\n600: '#718096',\n700: '#4a5568',".
        "\n800: '#2d3748',\n900: '#1a202c',\n},\n".
        "\n  red: {\n100: '#fff5f5',\n200: '#fed7d7',\n300: '#feb2b2',\n400: '#fc8181',".
        "\n500: '#f56565',\n600: '#e53e3e',\n700: '#c53030',\n800: '#9b2c2c',".
        "\n900: '#742a2a',\n},\n".
        "\n   orange: {\n100: '#fffaf0',\n200: '#feebc8',\n300: '#fbd38d',".
        "\n400: '#f6ad55',\n500: '#ed8936',\n600: '#dd6b20',\n700: '#c05621',".
        "\n800: '#9c4221',\n900: '#7b341e',\n},\n".
        "\n  yellow: {\n100: '#fffff0',\n200: '#fefcbf',\n300: '#faf089',".
        "\n400: '#f6e05e',\n500: '#ecc94b',\n600: '#d69e2e',\n700: '#b7791f',".
        "\n800: '#975a16',\n900: '#744210',\n},\n".
        "\n  green: {\n100: '#f0fff4',\n200: '#c6f6d5',\n300: '#9ae6b4',".
        "\n400: '#68d391',\n500: '#48bb78',\n600: '#38a169',\n700: '#2f855a',".
        "\n800: '#276749',\n900: '#22543d',\n},".
        "\n  teal: {\n100: '#e6fffa',\n200: '#b2f5ea',\n300: '#81e6d9',".
        "\n400: '#4fd1c5',\n500: '#38b2ac',\n600: '#319795',\n700: '#2c7a7b',".
        "\n800: '#285e61',\n900: '#234e52',\n},\n".
        "\n  blue: {\n100: '#ebf8ff',\n200: '#bee3f8',\n300: '#90cdf4',".
        "\n400: '#63b3ed',\n500: '#4299e1',\n600: '#3182ce',\n700: '#2b6cb0', ".
        "\n800: '#2c5282',\n900: '#2a4365',\n},".
        "\n  indigo: {\n100: '#ebf4ff',\n200: '#c3dafe',\n300: '#a3bffa',".
        "\n400: '#7f9cf5',\n500: '#667eea',\n600: '#5a67d8',\n700: '#4c51bf',".
        "\n800: '#434190',\n900: '#3c366b',\n},\n".
        "\n  purple: {\n100: '#faf5ff',\n200: '#e9d8fd',\n300: '#d6bcfa',".
        "\n400: '#b794f4',\n500: '#9f7aea',\n600: '#805ad5',\n700: '#6b46c1',".
        "\n800: '#553c9a',\n900: '#44337a',\n},\n".
        "\n  pink: {\n100: '#fff5f7',\n200: '#fed7e2',\n300: '#fbb6ce',".
        "\n400: '#f687b3',\n500: '#ed64a6',\n600: '#d53f8c',\n700: '#b83280',".
        "\n800: '#97266d',\n900: '#702459',\n},\n";

        $this->searchAndReplace->perform('let colors\s*=\s*\{([^\}]+)\}', '', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('\s+colors:\s*colors', '  colors: {'.$colors.'}', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('backgroundColor:\s*colors', 'backgroundColor: theme => theme(\'colors\')', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('textColor:\s*colors', 'textColor: theme => theme(\'colors\')', SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('borderColor:\s*g([^\n]+)', "borderColor: theme => {\nreturn global.Object.assign({ default: theme('colors.gray.300', 'currentColor') }, theme('colors'))\n},", SearchAndReplace::NO_ESCAPE);
        $this->searchAndReplace->perform('require\(\'tailwindcss\/defaultConfig\'\)\(\)', "require('tailwindcss/defaultConfig')", SearchAndReplace::NO_ESCAPE);
    
            //TODO: use javascript beautifier package ... 
    }
}
