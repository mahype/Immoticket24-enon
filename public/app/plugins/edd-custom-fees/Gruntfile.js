'use strict';
module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		clean: {
			translation: [
				'languages/edd-custom-fees.pot'
			]
		},

		makepot: {
			translation: {
				options: {
					mainFile: 'edd-custom-fees.php',
					domainPath: '/languages',
					exclude: [ 'vendor/.*' ],
					potComments: 'Copyright (c) 2016-<%= grunt.template.today("yyyy") %> <%= pkg.author.name %>',
					potFilename: 'edd-custom-fees.pot',
					potHeaders: {
						'language-team': '<%= pkg.author.name %> <<%= pkg.author.email %>>',
						'last-translator': '<%= pkg.author.name %> <<%= pkg.author.email %>>',
						'project-id-version': '<%= pkg.name %> <%= pkg.version %>',
						'report-msgid-bugs-to': '<%= pkg.homepage %>',
						'x-generator': 'grunt-wp-i18n 0.5.3',
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
	grunt.loadNpmTasks('grunt-wp-i18n');

	grunt.registerTask('translation', [
		'clean:translation',
		'makepot:translation'
	]);

	grunt.registerTask('default', [
		'translation'
	]);
};
