# IMPORTANT

_Most_ of the files in this folder are automatically created by `brunch` and should not be manually updated.

## Brunch

* The `.less` and `.css` files that are compiled into this folder are found in `library\styles`
* The `.js` files that are compiled into this folder are found in `library\js\js_for_brunch`

* `Brunch` config is in `brunch-config.js` at the root level.

To compile these files run

```` bash
shell> cd <application home>
shell> npm install
shell> ./node_modules/brunch/bin/brunch watch
````

## DataTables

These files come from <https://datatables.net/download/>, although only the images are actually used. Currently, `sort_both.png` is being over-ridden with a darker icon in `tables.less` to make it visible.
