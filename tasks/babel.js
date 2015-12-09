'use strict';

module.exports = function babelify(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-babel');

	// Options
	return {
    options: {
      sourceMap: true,
      presets: ['es2015']
    },
    dist: {
			files: { 'build/js/**/*.js': 'public/js/**/*.{js,jsx}' }
    }
	};
};
