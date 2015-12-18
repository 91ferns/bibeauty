'use strict';

module.exports = function watch(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Options
	return {
		sass: {
			files: ['public/css/**/*.{scss,sass}'],
			tasks: ['styles']
		},
    scripts: {
      files: ['public/js/**/*.js'],
      tasks: ['scripts'],
      options: {
        spawn: false,
      },
    }, // scripts
		express: {
			files: [
				'controllers/**/*.js',
				'models/**/*.js',
				'services/**/*.js',
				'config/**/*.json',
				'config/**/*.js',
			],
			tasks: ['express:dev', 'wait'],
			options: {
				livereload: true,
				nospawn: true
			}
		}
	};
};
