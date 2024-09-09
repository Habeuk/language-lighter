# language-lighter

drupal module which separates translation activation forms from content

## Installation

1. Retrieve this module files in your site module folder or user composer to do using the following command:<br/>
   `composer require habeuk/language_lighter:^1.0.0`
2. Instal The module using **Drush**(only if you have it and from the folder containing de web folder) or through the GUI. The drush command is<br/>
   `vendor/bin/drush en language_lighter`
3. Clear cache

## Use

First you have to enable the module fonctionnality on your entity(ies). To do so, go to the language_lighter config form( Route:`/admin/config/system/language-lighter`)

![setting form example](/assets/images/config_form_example.png)

After that all you have to do is access your bundle translation form with the contextual entity operation link (**Translation settings**)

![contextual link example](/assets/images/contextual_example.png)
