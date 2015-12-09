'use strict';

module.exports = function babelify(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-autoprefixer');

	// Options
	return {
    options: {
      // Task-specific options go here.
      browsers: ['last 2 versions', 'ie 8', 'ie 9']
    },
    single_file: {
      // Target-specific file lists and/or options go here.
      src: '.build/css/app.css',
      dest: '.build/css/app.css'
    },
	};
};
