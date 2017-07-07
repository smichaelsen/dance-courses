module.exports = function (grunt) {

    var projectPath = '/var/www/projects/flowfwd/castlerush';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        postcss: {
            options: {
                processors: [
                    require('autoprefixer')({browsers: 'last 2 versions'})
                ]
            },
            dist: {
                src: 'www/build/css/*.css'
            }
        },
        sass: {
            dist: {
                files: {
                    'www/build/css/style.css': 'resources/sass/app.scss'
                }
            }
        },
        shell: {
            bower: {
                command: function () {
                    return './node_modules/bower/bin/bower install';
                }
            },
            dbschema: {
                command: function (environment) {
                    var command = 'php vendor/bin/doctrine orm:schema-tool:update --force';
                    if (environment == 'vm') {
                        return 'vagrant ssh -c "cd ' + projectPath + ' && ' + command + '"';
                    }
                    return command;
                }
            }
        },
        uglify: {
            libs: {
                files: {
                    'www/build/js/libs.js': [
                        'resources/bower_components/jquery/dist/jquery.js',
                        'resources/bower_components/rater/rater.js',
                        'resources/bower_components/foundation/js/foundation/foundation.js',
                        'resources/bower_components/foundation/js/foundation/foundation.reveal.js',
                        'resources/jslibs/*.js'
                    ]
                }
            },
            application: {
                files: {
                    'www/build/js/application.js': [
                        'resources/js/*.js'
                    ]
                }
            },
            adminlibs: {
                files: {
                    'www/build/js/adminlibs.js': [
                        'resources/jsadminlibs/*.js'
                    ]
                }
            },
            adminapplication: {
                files: {
                    'www/build/js/adminapplication.js': [
                        'resources/jsadmin/*.js'
                    ]
                }
            }
        },
        watch: {
            bower: {
                files: ['.bowerrc', 'bower.json'],
                tasks: ['shell:bower']
            },
            copy: {
                files: ['resources/fonts/*'],
                tasks: ['copy:fonts']
            },
            css: {
                files: ['resources/sass/*.scss', 'resources/sass/*/*.scss'],
                tasks: ['sass', 'postcss']
            },
            doctrine: {
                files: ['src/Classes/Entities/*.php'],
                tasks: ['shell:dbschema:vm']
            },
            jslibs: {
                files: ['resources/jslibs/*.js', 'resources/bower_components/*.js', 'resources/bower_components/*/*.js', 'resources/bower_components/*/*/*.js'],
                tasks: ['uglify:libs']
            },
            jsapplication: {
                files: ['resources/js/*.js', 'resources/js/*/*.js'],
                tasks: ['uglify:application']
            },
            jsadminlibs: {
                files: ['resources/jsadminlibs/*.js'],
                tasks: ['uglify:adminlibs']
            },
            jsadminapplication: {
                files: ['resources/jsadmin/*.js'],
                tasks: ['uglify:adminapplication']
            }
        }
    });
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    var buildTasks = ['shell:bower', 'copy', 'sass', 'postcss', 'uglify'];
    grunt.registerTask('default', buildTasks.concat(['shell:dbschema:vm', 'watch']));
    grunt.registerTask('build', buildTasks.concat(['shell:dbschema:local']));
};
