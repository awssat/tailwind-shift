# Tailwind Shift 🚧

![tailwind-shift](https://i.imgur.com/xZ6ydio.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awssat/tailwind-shift.svg?style=flat-square)](https://packagist.org/packages/awssat/tailwind-shift)
[![StyleCI](https://styleci.io/repos/177280907/shield?branch=master)](https://styleci.io/repos/177280907)
[![Build Status](https://img.shields.io/travis/awssat/tailwind-shift/master.svg?style=flat-square)](https://travis-ci.org/awssat/tailwind-shift)

A helper tool to upgrade your current codes of a project to latest version of TailwindCSS without a hassle.

## TailwindCSS versions support:
It's currently support shifting from 0.7.? to 1.0  

## Installing `tailwind-shift` for CLI use

You can install the package via composer globally:

`$ composer global require awssat/tailwind-shift`

Then use it to upgrade/convert a snippet, a file or a folder.


### How to Use to upgrade your project
#### Upgrade TailwindCSS config file
TailwindShift offers a command to upgrade your config file, to use it: 
```bash
$ tailwind-shift tailwind.js --fixconfig=true
```
This will generate a new file called tailwind.tw.js, check the file and rename it if everything is ok.

#### Upgrade views templates
```bash
$ tailwind-shift path/to/views/ --recursive=true --extensions=vue,php,html
```
This will generate news files as in `view.tw.php` as a safety procedure, you could use `--replace=true` to overwrite old files.



### Advanced Usses of the command

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
This will allow you to upgrade your `vue`, `twig`, `css` files and more!
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


#### update a file
By default this will copy the code into a new file like file.html -> file.tw.html
```bash
$ tailwind-shift file.blade.php
```
This option works with the `--replace=true` option

##### Using the package in your project (If that's what your want)

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
