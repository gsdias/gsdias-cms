/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global module: false */

module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        qunit: {
            files: ['tpl/*.html']
        },
        jshint: {
            files: [
                './js/core/*.js',
                './js/core/generic/*.js',
                '!./js/App.js',
                '!./js/libs/*.js'
            ],
            options: {
                // options here to override JSHint defaults
                globals: {
                    jQuery: true,
                    console: true,
                    module: true,
                    document: true
                }
            }
        },
        watch: {
            js: {
                files: ['./js/*.js', './js/*/*.js'],
                tasks: ['jshint']
            },
            css: {
                files: ['./sass/*.scss', './sass/*/*.scss'],
                tasks: ['compass']
            }
        },
        compass: {
            config: './config.rb'
        },
        copy: {
            main: {
                src: './tpl/_scripts.html',
                dest: '../gsd-tpl/_shared/_scripts.html'
            }
        },
        usemin: {
            html: '../gsd-tpl/_shared/_scripts.html'
        },
        useminPrepare: {
            html: './tpl/_scripts.html',
            options: {
                dest: '../'
            }
        },
        modernizr: {

            dist: {
                // [REQUIRED] Path to the build you're using for development.
                "devFile" : "./js/libs/modernizr.js",

                // [REQUIRED] Path to save out the built file.
                "outputFile" : "../gsd-resources/js/libs/modernizr.min.js",

                // Based on default settings on http://modernizr.com/download/
                "extra" : {
                    "shiv" : true,
                    "printshiv" : false,
                    "load" : true,
                    "mq" : false,
                    "cssclasses" : true
                },

                // Based on default settings on http://modernizr.com/download/
                "extensibility" : {
                    "addtest" : false,
                    "prefixed" : false,
                    "teststyles" : false,
                    "testprops" : false,
                    "testallprops" : false,
                    "hasevents" : false,
                    "prefixes" : false,
                    "domprefixes" : false
                },

                // By default, source is uglified before saving
                "uglify" : true,

                // Define any tests you want to implicitly include.
                "tests" : [],

                // By default, this task will crawl your project for references to Modernizr tests.
                // Set to false to disable.
                "parseFiles" : true,

                // When parseFiles = true, this task will crawl all *.js, *.css, *.scss files, except files that are in node_modules/.
                // You can override this by defining a "files" array below.
                "files" : {
                    "src": ['./sass/*.scss', './sass/*/*.scss', './js/*.js', './js/*/*.js']
                },

                // When parseFiles = true, matchCommunityTests = true will attempt to
                // match user-contributed tests.
                "matchCommunityTests" : false,

                // Have custom Modernizr tests? Add paths to their location here.
                "customTests" : []
            }

        },
        htmlmin: {
            dist: {
                options: {
                    collapseWhitespace: true
                },
                files: [{
                    expand: true,
                    dot: false,
                    cwd: './',
                    dest: 'dist',
                    src: '../core/js/*.html'
                }, {
                    expand: true,
                    dot: false,
                    cwd: './',
                    dest: 'dist',
                    src: '../core/tpl/*.html'
                }]
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-qunit');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-usemin');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-modernizr');
    grunt.loadNpmTasks('grunt-contrib-htmlmin');

    grunt.registerTask('default', ['compass', 'jshint', 'copy', 'watch']);
    grunt.registerTask('build', ['copy', 'useminPrepare', 'concat', 'uglify', 'usemin', 'compass', 'modernizr']);
};
