# Tailwind Shift

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awssat/tailwind-shift.svg?style=flat-square)](https://packagist.org/packages/awssat/tailwind-shift)
[![StyleCI](https://styleci.io/repos/110390721/shield?branch=master)](https://styleci.io/repos/110390721)
[![Build Status](https://img.shields.io/travis/awssat/tailwind-shift/master.svg?style=flat-square)](https://travis-ci.org/awssat/tailwind-shift)


<p align="center">
 
</p>
                                                                         


This helper tool will upgrade your current Tailwindcss project to latest version.


## Installing `tailwind-shift` for CLI use

You can install the package via composer globally:

`$ composer global require awssat/tailwind-shift`

Then use it to upgrade/convert a snippet, a file or a folder.

### Using the command

#### Possible Options
##### Update a directory (just the files in this directory, it's not recursive)
```bash
$ tailwind-shift path/to/directory/ 
```
##### Recursively update a directory
```bash
$ tailwind-shift path/to/directory/ --recursive=true
```
You can also use the short hand `-r true` instead of the full `--recursive=true`

##### Update different file extensions
This will allow you to upgrade your `vue` files, `twig` files, and more!
```bash
$ tailwind-shift path/to/directory/ --extensions=vue,php,html
```
You can also use the short hand `-e vue,php,html` instead of the full `--extensions`

##### Overwrite the original files
_Please note this can be considered a destructive action as it will replace the orignal file and will not leave a copy of the original any where._
```bash
$ tailwind-shift path/to/directory/ --replace=true
```

##### Update raw code
just a snippet of code:

```bash
$ tailwind-shift '<div class="text-grey pin-t"></div>'
```


### update a file
By default this will copy the code into a new file like file.html -> file.tw.html
```bash
$ tailwind-shift file.blade.php
```
This option works with the `--replace=true` option

## Using the package in your project (If that's what your want)

You can install the package via composer locally in your project folder:

```bash 
$ composer require awssat/tailwind-shift
```

Then use it like this: 

```php
use Awssat\TailwindShift\Updater;

$input = '<div class="text-grey pint-t">hi</div>'; //old tailwindcss code

$output = (new Updater)
            ->setContent($input)
            ->convert()
            ->get(); // gets new updated code
```


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
