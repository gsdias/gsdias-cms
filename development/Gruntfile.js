/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, module: false */

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
            files: ['<%= jshint.files %>'],
            tasks: ['sass']
        },
        compass: {
            config: './config.rb'
        },
        copy: {
            main: {
                src: '../core/js/_scripts.html',
                dest: '../core/tpl/_scripts.html'
            }
        },
        usemin: {
            html: '../core/tpl/_scripts.html'
        },
        useminPrepare: {
            html: '../core/js/_scripts.html',
            options: {
                dest: '../'
            }
        },
        modernizr: {

            dist: {
                // [REQUIRED] Path to the build you're using for development.
                "devFile" : "./js/libs/modernizr.js",

                // [REQUIRED] Path to save out the built file.
                "outputFile" : "../resources/js/libs/modernizr.min.js",

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
                // "files" : {
                // "src": []
                // },

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
                    src: '../core/js/*.html', 
                },{
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

    //grunt.registerTask('default', ['copy', 'jshint', 'compass', 'watch']);
    grunt.registerTask('default', ['compass', 'watch']);
    grunt.registerTask('build', ['copy', 'useminPrepare', 'concat', 'uglify', 'usemin', 'compass', 'modernizr']);
};