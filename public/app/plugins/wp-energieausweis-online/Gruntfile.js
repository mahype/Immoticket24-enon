'use strict';
module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*!\n' +
            ' * WP Energieausweis online - Version <%= pkg.version %>\n' +
            ' * \n' +
            ' * <%= pkg.author.name %> <<%= pkg.author.email %>>\n' +
            ' */',
    pluginheader: '/*\n' +
                  'Plugin Name: WP Energieausweis online\n' +
                  'Plugin URI: <%= pkg.homepage %>\n' +
                  'Description: <%= pkg.description %>\n' +
                  'Version: <%= pkg.version %>\n' +
                  'Author: <%= pkg.author.name %>\n' +
                  'Author URI: <%= pkg.author.url %>\n' +
                  'License: Private\n' +
                  'Text Domain: wpenon\n' +
                  'Domain Path: /inc/languages/\n' +
                  'Tags: wordpress, plugin, app, energieausweis, online\n' +
                  'GitHub Plugin URI: felixarntz/wp-energieausweis-online\n' +
                  'GitHub Branch: master\n' +
                  '*/',
    fileheader: '/**\n' +
                ' * @package WPENON\n' +
                ' * @version <%= pkg.version %>\n' +
                ' * @author <%= pkg.author.name %> <<%= pkg.author.email %>>\n' +
                ' */',

    clean: {
      general: [
        'assets/general.min.js'
      ],
      admin: [
        'assets/admin.css',
        'assets/admin.min.css',
        'assets/admin.min.js'
      ],
      frontend: [
        'assets/frontend-bootstrap.css',
        'assets/frontend-bootstrap.min.css',
        'assets/frontend.css',
        'assets/frontend.min.css',
        'assets/frontend.min.js'
      ],
      translation: [
        'languages/wpenon.pot'
      ]
    },

    jshint: {
      options: {
        jshintrc: 'assets/.jshintrc',
        reporterOutput: ''
      },
      general: {
        src: [
          'assets/general.js'
        ]
      },
      admin: {
        src: [
          'assets/admin.js'
        ]
      },
      frontend: {
        src: [
          'assets/frontend.js'
        ]
      }
    },

    uglify: {
      options: {
        preserveComments: 'some',
        report: 'min'
      },
      general: {
        src: 'assets/general.js',
        dest: 'assets/general.min.js'
      },
      admin: {
        src: 'assets/admin.js',
        dest: 'assets/admin.min.js'
      },
      frontend: {
        src: 'assets/frontend.js',
        dest: 'assets/frontend.min.js'
      }
    },

    less: {
      admin: {
        options: {
          strictMath: true
        },
        files: {
          'assets/admin.css': 'assets/admin.less'
        }
      },
      frontend: {
        options: {
          strictMath: true
        },
        files: {
          'assets/frontend-bootstrap.css': 'assets/frontend-bootstrap.less',
          'assets/frontend.css': 'assets/frontend.less'
        }
      }
    },

    autoprefixer: {
      options: {
        browsers: [
          'Android 2.3',
          'Android >= 4',
          'Chrome >= 20',
          'Firefox >= 24',
          'Explorer >= 8',
          'iOS >= 6',
          'Opera >= 12',
          'Safari >= 6'
        ]
      },
      admin: {
        src: 'assets/admin.css'
      },
      frontend: {
        src: [
          'assets/frontend-bootstrap.css',
          'assets/frontend.css'
        ]
      }
    },

    cssmin: {
      options: {
        compatibility: 'ie8',
        keepSpecialComments: '*',
        noAdvanced: true
      },
      admin: {
        files: {
          'assets/admin.min.css': 'assets/admin.css'
        }
      },
      frontend: {
        files: {
          'assets/frontend-bootstrap.min.css': 'assets/frontend-bootstrap.css',
          'assets/frontend.min.css': 'assets/frontend.css'
        }
      }
    },

    usebanner: {
      options: {
        position: 'top',
        banner: '<%= banner %>'
      },
      general: {
        src: [
          'assets/general.min.js'
        ]
      },
      admin: {
        src: [
          'assets/admin.min.css',
          'assets/admin.min.js'
        ]
      },
      frontend: {
        src: [
          'assets/frontend.min.css',
          'assets/frontend.min.js'
        ]
      }
    },

    replace: {
      header: {
        src: [
          'wp-energieausweis-online.php'
        ],
        overwrite: true,
        replacements: [{
          from: /((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/,
          to: '<%= pluginheader %>'
        }]
      },
      version: {
        src: [
          'wp-energieausweis-online.php',
          'inc/**/*.php',
          'templates/**/*.php'
        ],
        overwrite: true,
        replacements: [{
          from: /\/\*\*\s+\*\s@package\s[^*]+\s+\*\s@version\s[^*]+\s+\*\s@author\s[^*]+\s\*\//,
          to: '<%= fileheader %>'
        }]
      }
    },

    makepot: {
      translation: {
        options: {
          domainPath: '/inc/languages',
          potComments: 'Copyright (c) 2014-<%= grunt.template.today("yyyy") %> <%= pkg.author.name %>',
          potFilename: 'wpenon.pot',
          potHeaders: {
            'report-msgid-bugs-to': '<%= pkg.homepage %>',
            'x-generator': 'grunt-wp-i18n 0.4.5',
            'x-poedit-basepath': '.',
            'x-poedit-language': 'English',
            'x-poedit-country': 'UNITED STATES',
            'x-poedit-sourcecharset': 'uft-8',
            'x-poedit-keywordslist': '__;_e;_x:1,2c;_ex:1,2c;_n:1,2; _nx:1,2,4c;_n_noop:1,2;_nx_noop:1,2,3c;esc_attr__; esc_html__;esc_attr_e; esc_html_e;esc_attr_x:1,2c; esc_html_x:1,2c;',
            'x-poedit-bookmars': '',
            'x-poedit-searchpath-0': '.',
            'x-textdomain-support': 'yes'
          },
          type: 'wp-plugin'
        }
      }
    }

  });
  
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-banner');
  grunt.loadNpmTasks('grunt-text-replace');
  grunt.loadNpmTasks('grunt-wp-i18n');

  grunt.registerTask('general', [
    'clean:general',
    'jshint:general',
    'uglify:general'
  ]);

  grunt.registerTask('admin', [
    'clean:admin',
    'jshint:admin',
    'uglify:admin',
    'less:admin',
    'autoprefixer:admin',
    'cssmin:admin'
  ]);

  grunt.registerTask('frontend', [
    'clean:frontend',
    'jshint:frontend',
    'uglify:frontend',
    'less:frontend',
    'autoprefixer:frontend',
    'cssmin:frontend'
  ]);

  grunt.registerTask('translation', [
    'clean:translation',
    'makepot:translation'
  ]);

  grunt.registerTask('plugin', [
    'usebanner',
    'replace:version',
    'replace:header'
  ]);

  grunt.registerTask('default', [
    'general',
    'admin',
    'frontend'
  ]);

  grunt.registerTask('build', [
    'general',
    'admin',
    'frontend',
    'translation',
    'plugin'
  ]);
};
