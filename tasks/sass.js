'use strict';


module.exports = function sass(grunt) {
    // Load task
    grunt.loadNpmTasks('grunt-contrib-sass');

    // Options
    return {
      dist: {
        options: {
          style: 'expanded',
          // sourcemap: true,
          loadPath: ['public/css']
        },
        files: {
          '.build/css/app.css': 'public/css/app.scss', // 'destination': 'source'
        }
      }
    };
};
