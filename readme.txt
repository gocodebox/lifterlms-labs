=== LifterLMS Labs ===
Contributors: chrisbadgett, strangerstudios, lifterlms, codeboxllc, brianhogg
Donate link: https://lifterlms.com/
Tags: lms, course, elearning, learning management system, quiz
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 5.3
Tested up to: 6.7
Requires PHP: 
Stable tag: 1.8.1

A collection of experimental, conceptual, and possibly silly features which improve and enhance the functionality of the LifterLMS core.


== Description ==

LifterLMS Labs is a collection of experimental features to improve and enhance the functionality of the LifterLMS core

We've created this free LifterLMS add-on plugin in order to provide these optional features to the LifterLMS community which may or may not be useful to everyone.

Some labs will ultimately find their way into the LifterLMS Core, some may remain in LifterLMS Labs.


### Current Labs

**Action Manager**

Quickly remove specific elements like course author, syllabus, and more without having to write any code. [Documentation and more information](https://lifterlms.com/docs/lab-action-manager/?utm_source=readme&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=actionmanager).

**Beaver Builder**

Add LifterLMS elements as pagebuilder modules and enable row and module visibility settings based on student enrollment in courses and memberships. [Documentation and more information](https://lifterlms.com/docs/lab-beaver-builder/?utm_source=readme&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=beaverbuilder).


**Lifti: Divi Theme Compatibility**

Enable LifterLMS compatibility with the Divi Theme and Page Builder. [Documentation and more information](https://lifterlms.com/docs/lab-lifti/?utm_source=readme&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=lifti).


**Simple Branding**

Customize the default colors of various LifterLMS elements. [Documentation and more information](https://lifterlms.com/docs/simple-branding-lab?utm_source=readme&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=simplebranding).


**Super Sidebars**

Very quickly configure LifterLMS sidebars to work with your theme. [Documentation and more information](https://lifterlms.com/docs/super-sidebars-lab?utm_source=readme&utm_campaign=lifterlmslabsplugin&utm_medium=product&utm_content=supersidebars).


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the LifterLMS -> Labs screen to activate and configure the labs you wish to use


== Changelog ==

= v1.8.1 - 2024-12-12 =

##### Bug Fixes

+ Fixing translation warnings, which break translations. [#58](https://github.com/gocodebox/lifterlms-labs/issues/58)


= v1.8.0 - 2023-11-02 =

##### New Features

+ Added settings for the lessons favorite button visibility.

##### Developer Notes

+ Changed priority of the action manager settings loading.


= v1.7.0 - 2023-10-26 =

##### New Features

+ Added an option to hide the lesson count on course catalogs.

##### Bug Fixes

+ Improved compatibility with PHP 8.2 (fixed deprecation warnings).
+ Fix use of deprecated filter `llms_get_quiz_theme_settings` with the Divi Theme Compatibility lab (aka Lifti). [#16](https://github.com/gocodebox/lifterlms-labs/issues/16)
+ Fixed 'LLMS Certificates' and 'LLMS Awarded Certificates' Post Types not visible in Beaver Builder Post Type settings. [#29](https://github.com/gocodebox/lifterlms-labs/issues/29)

##### Deprecations

+ Deprecated method `LLMS_Lab_Lifti::quiz_settings`.

##### Developer Notes

+ Use strict comparison for `in_array` and `array_search`.
+ Improved docblocks and coding standards.


= v1.6.1 - 2023-02-17 =

##### Deprecations

+ Fixed display issues on the Labs settings page.


= v1.6.0 - 2021-07-13 =

+ **Raises the minimum supported WordPress core version to 5.3**.
+ Adds support for LifterLMS Core 5.0+.
+ Fixes issue encountered when activating the Beaver Builder lab.


= v1.5.3 - 2020-01-28 =

+ Tested to WordPress core 5.3.2
+ Beaver Builder: Fix module visibility issue when hiding modules based on enrollment in specific courses or memberships.
+ Action Manager: Remove non-functioning featured image hook. Featured images are output by themes not by LifterLMS.


= v1.5.2 - 2018-07-12 =

+ Beaver Builder: Minor changes to accommodate changes in LifterLMS 3.20.0
+ Lifti: Minor changes to accommodate changes in LifterLMS 3.20.0


= v1.5.1 - 2018-03-13 =

+ Lifti: Custom builder classes will now function as expected when used on free lessons
+ Lifti: Add support for LifterLMS Quiz layouts


= v1.5.0 - 2018-02-15 =

+ Beaver Builder: Updated to support Beaver Builder 2.0 (long over due, I know.)
+ Lifti: Changes to Divi prevent loading programmatically created layouts. The predefined layout for a LifterLMS course can now be downloaded [here](http://lifterlms.com/wp-content/uploads/2017/01/LifterLMS-Divi-Layouts.json).
+ Simple Branding: Add branding overrides for LifterLMS instructor information cards
+ Simple Branding: Add branding overrides for LifterLMS 3.16.0 quiz styles and LifterLMS Advanced Quizzes styles
+ Simple Branding: Save default values in database & generate CSS when the lab is enabled.


= v1.4.0 - 2017-09-05 =

##### Simple Branding Updates

+ Add support for LifterLMS notifications
+ Set default colors for branding options. Fixes issues with invalid CSS when options aren't set after enabling the lab
+ Make all branding color settings required


[Read the full changelog](https://make.lifterlms.com/tag/lifterlms-labs)
