'use strict';


module.exports = function (grunt) {

    // Load the project's grunt tasks from a directory
    require('grunt-config-dir')(grunt, {
        configDir: require('path').resolve('tasks')
    });

    grunt.loadNpmTasks('grunt-makara-browserify');

    grunt.registerTask('styles', ['sass', 'autoprefixer']);
    grunt.registerTask('scripts', ['babel', 'browserify']);

    grunt.registerTask('wait', function () {
      grunt.log.ok('Waiting for server reload...');

      var done = this.async();

      setTimeout(function () {
        grunt.log.writeln('Done waiting!');
        done();
      }, 1500);
    });

    grunt.registerTask('serve', ['express:dev', 'wait']);
    grunt.registerTask('default', ['styles', 'scripts', 'serve', 'watch']);

    grunt.registerTask('build', ['jshint', 'dustjs', 'makara-browserify', 'styles', 'scripts', 'copyto']);
    grunt.registerTask('test', [ 'jshint', 'mochacli' ]);

};
