/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global module: false */

module.exports = function (grunt) {

    'use strict';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        filerev: {
            compile: {
                src: ['../gsd-resources/js/built.js', '../gsd-resources/css/screen.css']
            },
            options: {
                algorithm: 'md5',
                length: 10
            }
        },
        clean: {
            options: { force: true },
            js: ['../gsd-resources/js/*.js', '../gsd-resources/js/*.map'],
            css: ['../gsd-resources/css/*.css', '../gsd-resources/css/*.map']
        },
        jshint: {
            files: [
                './js/core/*.js',
                './js/core/generic/*.js',
                '!./js/app.js',
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
        copy: {
            main: {
                src: './tpl/_scripts.html',
                dest: '../gsd-tpl/_shared/_scripts.html'
            }
        },
        usemin: {
            html: '../gsd-tpl/_shared/_scripts.html'
        },
        'string-replace': {
            'js-source-map-fix': {
                files: {
                    '../gsd-resources/js/': '../gsd-resources/js/*'
                },
                options: {
                    replacements: [
                        {
                            pattern: /"..\/..\/..\/..\/js/gi,
                            replacement: '"..\/..\/gsd-development\/js'
                        },
                        {
                            pattern: /"..\/..\/..\/..\/..\/gsd-client/gi,
                            replacement: '"..\/..\/gsd-client'
                        }
                    ]
                }
            }
        },

        useminPrepare: {
            html: './tpl/_scripts.html',
            options: {
                dest: '../',
                flow: {
                    steps: {
                        js: ['concat', 'uglifyjs']
                    },
                    post: {
                        js: [{
                            name: 'concat',
                            createConfig: function (context) {
                                var generated = context.options.generated;
                                generated.options = {
                                    sourceMap: true
                                };
                            }
                        }, {
                            name: 'uglify',
                            createConfig: function (context) {
                                var generated = context.options.generated;
                                generated.options = {
                                    compress: {
                                        'drop_console': true
                                    },
                                    sourceMap: true,
                                    sourceMapIn: '.tmp/concat/gsd-resources/js/built.js.map',
                                    files: {
                                        '../gsd-resources/js/*': '.tmp/concat/gsd-resources/js/*'
                                    }
                                };
                            }
                        }]
                    }
                }
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
        },
        watch: {
            js: {
                files: ['./js/*.js', './js/*/*.js', '../gsd-client/development/js/*.js', '../gsd-client/development/js/*/*.js'],
                tasks: ['jshint', 'useminPrepare', 'concat', 'uglify', 'usemin']
            },
            css: {
                files: ['./sass/*.scss', './sass/*/*.scss', '../gsd-client/development/sass/*.scss', '../gsd-client/development/sass/*/*.scss'],
                tasks: ['compass']
            }
        },
        compass: {
            config: './config.rb',
            clean: true
        },
        modernizr: {

            dist: {
                // [REQUIRED] Path to the build you're using for development.
                devFile: './js/libs/modernizr.js',

                // [REQUIRED] Path to save out the built file.
                outputFile: '../gsd-resources/js/libs/modernizr.min.js',

                // Based on default settings on http://modernizr.com/download/
                extra: {
                    shiv : true,
                    printshiv : false,
                    load : true,
                    mq : false,
                    cssclasses : true
                },

                // Based on default settings on http://modernizr.com/download/
                extensibility: {
                    addtest : false,
                    prefixed : false,
                    teststyles : false,
                    testprops : false,
                    testallprops : false,
                    hasevents : false,
                    prefixes : false,
                    domprefixes : false
                },

                // By default, source is uglified before saving
                uglify : true,

                // Define any tests you want to implicitly include.
                tests : [],

                // By default, this task will crawl your project for references to Modernizr tests.
                // Set to false to disable.
                parseFiles : true,

                // When parseFiles = true, this task will crawl all *.js, *.css, *.scss files, except files that are in node_modules/.
                // You can override this by defining a "files" array below.
                files : {
                    src: ['./sass/*.scss', './sass/*/*.scss', './js/*.js', './js/*/*.js']
                },

                // When parseFiles = true, matchCommunityTests = true will attempt to
                // match user-contributed tests.
                matchCommunityTests : false,

                // Have custom Modernizr tests? Add paths to their location here.
                customTests : []
            }

        }
    });
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-modernizr');
    grunt.loadNpmTasks('grunt-usemin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-htmlmin');
    grunt.loadNpmTasks('grunt-filerev');
    grunt.loadNpmTasks('grunt-contrib-clean');

    grunt.registerTask('default', [
        'clean',
        'copy',
        'jshint',
        'compass',
        'useminPrepare',
        'concat:generated',
        'uglify:generated',
        'usemin',
        'string-replace',
        'watch'
    ]);
    grunt.registerTask('build', [
        'clean',
        'copy',
        'jshint',
        'compass',
        'useminPrepare',
        'concat:generated',
        'uglify:generated',
        'filerev',
        'usemin',
        'string-replace',
        'modernizr'
    ]);
};
