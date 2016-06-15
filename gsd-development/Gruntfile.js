/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

module.exports = function (grunt) {

    'use strict';

    var pkg = require('./package.json');

    grunt.initConfig({
        clean: {
            start: {
                options: { force: true },
                src: [pkg.gsdresources + 'js/*.js', pkg.gsdresources + 'js/*.map', pkg.gsdresources + 'css/*.css', pkg.gsdresources + 'css/*.map']
            },
            finish: {
                options: { force: true },
                src: ['.tmp', '.sass-cache']
            }
        },
        jshint: {
            files: [
                './js/core/**/*.js',
                pkg.gsdfrontend + 'development/js/core/**/*.js'
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
                expand: true,
                cwd: './tpl/',
                src: '**',
                dest: '../gsd-tpl/_shared/',
                flatten: true,
                filter: 'isFile'
            }
        },
        usemin: {
            html: '../gsd-tpl/_shared/_scripts.html'
        },
        'string-replace': {
            'js-source-map-fix': {
                files: {
                    '../gsd-resources/js/': pkg.gsdresources + 'js/*'
                },
                options: {
                    replacements: [
                        {
                            pattern: /"..\/..\/..\/..\/js/gi,
                            replacement: '"..\/..\/gsd-development\/js'
                        },
                        {
                            pattern: /"..\/..\/..\/..\/..\/gsd-frontend/gi,
                            replacement: '"..\/..\/gsd-frontend'
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
                                var generated = context.options.generated,
                                    clientfiles = grunt.file.readJSON(pkg.gsdfrontend + 'development/jsclient.json');
                                generated.files[0].src = generated.files[0].src.concat(clientfiles.files);
                                context.inFiles = context.inFiles.concat(clientfiles.files);

                                console.log(generated.files[0].src);
                            }
                        }, {
                            name: 'uglify',
                            createConfig: function (context) {
                                var generated = context.options.generated;
                                generated.options = {
                                    compress: {
                                        //'drop_console': true
                                    },
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
                    dest: '../gsd-tpl/shared',
                    src: './tpl/_scripts.html'
                }, {
                    expand: true,
                    dot: false,
                    cwd: './',
                    dest: '../gsd-tpl/shared',
                    src: '../gsd-tpl/shared/_scripts.html'
                }]
            }
        },
        watch: {
            js: {
                files: ['./js/*.js', './js/*/*.js', pkg.gsdfrontend + 'development/js/*.js', pkg.gsdfrontend + 'development/js/*/*.js'],
                tasks: [/*'jshint', */'useminPrepare', 'concat:generated', 'uglify:generated', 'usemin', 'string-replace']
            },
            css: {
                files: ['./sass/*', './sass/**/*', pkg.gsdfrontend + 'development/sass/*.scss', pkg.gsdfrontend + 'development/sass/*/*.scss'],
                tasks: ['compass']
            },
            html: {
                files: ['./tpl/*'],
                tasks: ['copy', /*'jshint', 'jscs', */'useminPrepare', 'concat:generated','uglify:generated', 'usemin', 'string-replace']
            }
        },
        compass: {
            config: './config.rb'
        },
        jscs: {
            files: [
                'js/**/*.js',
                '!js/libs/*',
                pkg.gsdfrontend + 'development/js/**/*.js',
                '!' + pkg.gsdfrontend + 'development/js/libs/*'
            ],
            options: {
                config: '.jscsrc'
            }
        },
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
        },
        babel: {
            options: {
                sourceMap: false,
                presets:['react']
            },
            dist: {
                files: {
                    "js/react.js": "js/react/react.jsx"
                }
            }
        }
    });

    require('load-grunt-tasks')(grunt);

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);
    grunt.registerTask('build', [
        'clean:start',
        'copy',
        'jshint',
        'jscs',
        'compass',
        'useminPrepare',
        'concat:generated',
        'uglify:generated',
        'usemin',
        'string-replace',
        'clean:finish'
    ]);
};
