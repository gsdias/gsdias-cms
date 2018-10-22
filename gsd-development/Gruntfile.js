module.exports = function (grunt) {

    'use strict';

    var pkg = require('./package.json');

    grunt.initConfig({
        phpcsfixer: {
            app: {
                dir: ['../gsd-class', '../gsd-admin']
            },
            options: {
                bin: '~/.composer/vendor/bin/php-cs-fixer',
                ignoreExitCode: true,
                level: 'all',
                quiet: true
            }
        }
    });
};
