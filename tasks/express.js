'use strict';

module.exports = function express(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-express-server');

	// Options
	return {
    options: {
      port: process.env.PORT || 8000
    },
    dev: {
      options: {
        script: 'server.js',
        debug: true
      }
    },
  }
};
