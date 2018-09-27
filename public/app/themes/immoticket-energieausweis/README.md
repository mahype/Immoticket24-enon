WordPress Grunt Starter Theme
=============================

[![endorse](https://api.coderwall.com/felixarntz/endorsecount.png)](https://coderwall.com/felixarntz)

A simple blank [WordPress](http://wordpress.org/) starter theme intended to use with [YeoPress](https://github.com/wesleytodd/YeoPress), [Grunt](http://gruntjs.com/) and [Bower](http://bower.io/). It is a solid flexible base, and gives you an efficient workflow since all the necessary stylesheets and scripts are compiled automatically whenever you change the source files. Of course you can also build them manually with one simple task.

**Important:** If you're not using YeoPress, make sure to require the [manual](https://github.com/felixarntz/wordpress-grunt-starter-theme/tree/manual) branch of this repository instead of master.

Features
--------

* blank WordPress theme with 1 nav menu and 1 sidebar predefined
* sticks to current WordPress guidelines
* preconfigured Gruntfile for efficient development (creates production CSS, JavaScript and POT file for translation)
* by default uses [LESS](http://lesscss.org/) syntax for stylesheet development, but can easily be switched to [SASS](http://sass-lang.com/) (by replacing the dependency `grunt-contrib-less` with `grunt-contrib-sass`)
* refreshes the theme's `style.css` header automatically
* comes with a lightweight version of [Bootstrap](http://getbootstrap.com/) (not much more than reset and grid system), however it can be replaced or removed if necessary
* comes with [FancyBox](http://fancybox.net/) and automatically applies it to all images; this behavior can easily be disabled or you can remove FancyBox completely
* automatically makes embeds appear responsive in 16:9 aspect ratio; this behavior can easily be disabled
* ready to be used with YeoPress (automatic slug, text-domain and prefixing based on theme directory name)

Getting Started
---------------

To enjoy the full advantages of this starter theme, you should know how to use the console.
You don't necessarily need to know how to use [Grunt](http://gruntjs.com/), but it will be easier if you already do.

**If you are using YeoPress...**

...simply use this theme as the default theme when executing `yo wordpress`. The theme's slug, name and all prefixes inside the code will be automatically created based on what you entered as `themeDir` during the YeoPress configuration. Make sure that whatever you enter there is lowercase and without any whitespace.

That's it already! This theme is prepared for YeoPress so that grunt and bower are automatically setup. You don't need to do anything here.

**If you aren't using YeoPress...**

...the most important thing is that you must use the [manual](https://github.com/felixarntz/wordpress-grunt-starter-theme/tree/manual) branch of this repository since the master branch only works with YeoPress.

The very first thing you should do is to open the theme's `package.json` and change all the field values in there to those of your choice. For example, put your theme slug as `name` and your theme name as `themeName`. The other fields should be as self-explanatory as these two are. It is important that you fill out every field except those under `engine` and `devDependencies` (which should not be changed). When you're done, save the file.

To get started with Grunt, you need to have [Node.js](http://nodejs.org/) installed on your system. Then, in Terminal, `cd` into the directory where the WordPress starter theme is located. Type `npm install` - if you successfully installed Node.js, some files should be downloaded, and a new folder named `node_modules` should appear. It contains the tools Grunt uses, and it also contains Grunt itself. Now you have everything installed. Still in Terminal, type `grunt setup`. This will run the tasks to initialize your theme correctly - which means all files are generated for the first time, bower dependencies will be installed and the slugs, class names, constant names and such in all the files (which are called something like 'mywptheme' by default) will be automatically changed to those you specified in the `package.json`. The theme header in `style.css` is refreshed too.

Automatic File Processing
-------------------------

Now let's get into business: First, go into your theme's directory. The stylesheets and scripts you should edit yourself are `assets/dev/style.less` (or `assets/dev/style.scss` if you're using SASS) and `assets/dev/scripts.js`. The files located in `assets/dist/` will be regenerated everytime you run grunt, so DO NOT EDIT THESE. The theme's `style.css` is only used for the theme header (since WordPress requires it), but you should not use it as an actual stylesheet.

The default procedure you will be using over and over is the following: By simply typing `grunt` into Terminal, you enable `watch` mode. This means that whenever you save a modified file, it is automatically processed so that you instantly see your changes.

To build the files manually, enter `grunt build` into terminal. This will also refresh the theme header in `style.css` so that possible changes in your `package.json` are properly reflected. You can also build scripts, stylesheets and the POT file independently from each other, by using `grunt scripts`, `grunt stylesheets` or `grunt translations`.

How To Use SASS instead of LESS
-------------------------------

To use [SASS](http://sass-lang.com/) instead of [LESS](http://lesscss.org/), you need to make a simple replacement in your `package.json` file. All you need to do is replace `grunt-contrib-less` with `grunt-contrib-sass` in the devDependencies section and adjust the version accordingly.

Of course you won't be able to use Bootstrap like before with SASS since it's written with LESS (however you can still get a [SASS version](http://getbootstrap.com/getting-started/#download), but I bet you already know some other great frameworks that work with SASS.

Contributions
-------------

If you have ideas on how the WordPress Grunt Starter Theme could be improved, feel free to [raise an issue](https://github.com/felixarntz/wordpress-grunt-starter-theme/issues) or [send a pull request](https://github.com/felixarntz/wordpress-grunt-starter-theme/pulls). But please remember that this is supposed to be a simple starter theme, so let's not bloat it up with cool functionality which some people will not need. But, especially regarding Grunt, I'm excited for your ideas.
