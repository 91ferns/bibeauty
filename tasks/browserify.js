'use strict';


module.exports = function browserify(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-browserify');

	// Options
	return {
		dist: {
			files: {
				'.build/js/app.js': 'public/js/app.js'
			},
			options: {
				transform: [
					["babelify", {
          }]
				]

			}
		}
	};
};
