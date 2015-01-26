Sass Watcher
============

It's PHP SCSS Compiler for WordPress and it's compatible with [Advanced Custom Field 5](http://advancedcustomfields.com) to send parameters(variables) to your SCSS codes.

We use [SCSSPHP](https://github.com/leafo/scssphp) to compile SCSS codes.

Features
----------

- Developer Mode:
-- Auto compile in each page refresh
- Manual Compile
- ACF Compatible
-- Send field values that field names start with **s_**
-- Compile codes every time ACF form saved
-- Compatible with all ACF 5 fields:
--- Repeater
--- Flexible Content
--- [Typography](https://github.com/reyhoun/acf-typography) 
--- [Background](https://github.com/reyhoun/acf-background)
--- and etc.

Getting Started
------------------

- **Important:** Create an empty file in root of your theme with **sass-watcher-config.txt** name.
- Download and Install Sass Watcher plugin(same as all WordPress plugins)
- Open WordPress dashboard and navigate to `Appearance > Sass Watcher`
- All **Initial Settings** are required. Complete all of them before continue.
	> - Sass Watcher only read **one** SCSS file and compile it in **one** CSS file.
	> - You can have **separate SCSS files** but you have to **import all** of them to **one** file for compile.
	> - In this documentation, I use **style.scss** file name for your main SCSS file that wants to compile it.

###Initial Settings
- **SCSS Directory Path:** This folder include your **style.scss** and **_theme-settings.scss** file.
	> - Prefix of all fields here is directory of active theme & we get it with `get_template_directory()` function
	> - Directory path example: `scss`
	> - **_theme-settings.scss** is a custom name for that file you wants to save your ACF 5 Sass variables on it.

- **Final SCSS file to compile:** This is path of your main SCSS file(for example: style.scss) for compile.

- **File for save variables sent from ACF:** This is path of your **_theme-settings.scss** file.
	> - Variables file example: `settings/_theme-settings.scss`
	> - If this file doesn't exist, Sass Watcher created it.

- **CSS output file:** This is path of your main CSS file(for example: style.css) for output of compiled SCSS codes.

###Developer Options
- **Developer Option:** Enable auto compile in each page refresh.
- **Compile Now:** When you want to compile your code manually use it.

###ACF Config
If you want set variable in your Sass codes with custom ACF field just add a `s_` before your field name and it's create a Sass variable with your field's name and store value of this field on it.

> - Some fields like **Repeater** and **Flexible Content** includes more than a field. If you add a `s_` before of this Repeater's field name, it's create a variable and store all sub fields value as a Sass array on it.
> - If you want don't store value of one of those sub fields, just add `ns_` before your sub field.

Bugs that we know
-----------------------
- After save **cache** and display old data in Config Form. **It's work correctly** but you have to refresh page to see your changes.
- I'm sorry! But at this moment, you have to write something for **File for save variables sent from ACF** but it's not important if you don't use it.
- It implements **SCSS 3.2.12** and it's related to [SCSSPHP](https://github.com/leafo/scssphp) project.