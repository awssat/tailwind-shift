---
prev: 2_installation
next: false
---

# Quick Start

## Upgrade TailwindCSS config file
TailwindShift offers a command to upgrade your config file, to use it: 
```bash
tailwind-shift tailwind.js --fixconfig=true
```
This will generate a new file called `tailwind.tw.js`, check the file and rename it to `tailwind.js` if everything is ok.

## Convert a whole directory 
```bash
tailwind-shift path/to/views/ --recursive=true --extensions=vue,php,html
```
This will generate new files (i.e: `view.tw.php`) as a safety procedure, you could use `--replace=true` to overwrite old files.



## Advanced Uses of the command
### Upgrade a directory (not recursive)
```bash
tailwind-shift path/to/directory/ 
```
### Recursively upgrade a directory
```bash
tailwind-shift path/to/directory/ --recursive=true
```
You can also use the short hand `-r true` instead of the full `--recursive=true`

### Update different file extensions
This will allow you to upgrade your `vue`, `twig`, `css` files and more!
```bash
tailwind-shift path/to/directory/ --extensions=vue,php,html
```
You can also use the short hand `-e vue,php,html` instead of the full `--extensions`

### Overwrite the original files
```bash
$ tailwind-shift path/to/directory/ --replace=true
```
::: tip 
Please note this can be considered a destructive action as it will replace the orignal file and will not leave a copy of the original any where.
:::

### Upgrade a raw code (a snippet of code)
```bash
tailwind-shift '<div class="text-grey pin-t"></div>'
```


### upgrade one file
By default this will copy the code into a new file like file.html -> file.tw.html
```bash
tailwind-shift file.blade.php
```
use `--replace=true` to overwrite the original file.
