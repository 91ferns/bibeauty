'use strict';

module.exports = function open(grunt) {
	// Load task
	grunt.loadNpmTasks('grunt-express-server');

	// Options
	return {
    server: {
      url: 'http://localhost:<%= express.options.port %>'
    }
  };
};
