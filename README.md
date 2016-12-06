# Language selector plugin for Omeka
## Installation
Copy directory with plugin to `plugins` directory of your Omeka installation.

Go to `/admin/plugins` in browser and click "Install" button in "Language selector" row.

## Configuration
Go to `/admin/plugins` in browser and click "Configure" button in "Language selector" row.

There will be a form with only one field. Data in this field will be used to generate `<select>` tag.

Each row is one option in `<select>` tag.

Syntax:

`locale;label`

where `locale` should be one from listed at this page and `label` will be used for particular option.

## Displaying the widget
Insert the following code in any place in your theme's view file you want to place the widget:
```PHP
<?php fire_plugin_hook('language_selector', array('view' => $this)); ?>
```