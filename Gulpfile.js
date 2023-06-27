'use strict';

const gulp         = require('gulp')
const debug        = require('gulp-debug')
const plumber      = require('gulp-plumber')
const notify       = require('gulp-notify')
const sourcemaps   = require('gulp-sourcemaps')
const sass         = require('gulp-sass')
const autoprefixer = require('gulp-autoprefixer');
const gcmq         = require('gulp-group-css-media-queries')
const cleanCSS     = require('gulp-clean-css')
const rollup       = require('gulp-better-rollup')
const uglify       = require('gulp-uglify')
const rename       = require('gulp-rename')
const potgen       = require('gulp-wp-pot')
const browserSync  = require('browser-sync').create()
const del          = require('del')
const map          = require('lodash.map')
const pkg          = require('./package.json')
const merge        = require("merge-stream")
const fs           = require('fs')
const dummyDir     = './dummy_data'
const dummyPlgDir  = './dummy_data/'+pkg.name

const rollupConfig = () => {
    const resolve  = require('rollup-plugin-node-resolve')
    const commonjs = require('rollup-plugin-commonjs')
    const babel    = require('rollup-plugin-babel')

    return {
        rollup: require('rollup'),
        plugins: [
            resolve(),
            commonjs(),
            babel({
                babelrc: false,
                runtimeHelpers: true,
                externalHelpers: true,
                presets: ['@babel/preset-env']
            }),
        ]
    }
}

/**
 * Handle errors and alert the user.
 */
const handleErrors = (r) => {
    notify.onError('ERROR: <%= error.message %>\n')(r)
}

gulp.task('scss', () => {
    return gulp.src('src/sass/admin/*.scss')
               .pipe(debug())
               .pipe(plumber(handleErrors))
               .pipe(sourcemaps.init())
               .pipe(sass().on('error', sass.logError))
               .pipe(autoprefixer())
               .pipe(gcmq())
               .pipe(sourcemaps.write('./'))
               .pipe(gulp.dest('assets/css/admin'))
               .pipe(browserSync.stream({ match: '**/*.css' }))
})

gulp.task('babel', () => {
    return gulp.src('src/js/admin/*.js')
               .pipe(debug())
               .pipe(plumber(handleErrors))
               .pipe(sourcemaps.init())
               .pipe(rollup(rollupConfig(), {
                   format: 'iife',
                   globals: pkg.globals || {}
               }))
               .pipe(sourcemaps.write('./'))
               .pipe(gulp.dest('assets/js'))
});

gulp.task('minify:js', () => {
    return gulp.src(['src/js/admin/**/*.js', '!assets/js/**/*.min.js'])
               .pipe(debug())
               .pipe(plumber())
               .pipe(uglify())
               .pipe(rename({ suffix: '.min' }))
               .pipe(gulp.dest('assets/js'))
})

gulp.task('minify:css', () => {
    return gulp.src(['src/sass/admin/*.css', '!assets/css/*.min.css'])
               .pipe(debug())
               .pipe(plumber(handleErrors))
               .pipe(cleanCSS())
               .pipe(rename({ suffix: '.min' }))
               .pipe(gulp.dest('assets/css'))
})

gulp.task('i18n', () => {
    return gulp.src(['*.php', 'inc/**/*.php', 'inc/**/**/*.php', '!vendor/**', '!tests/**'])
               .pipe(plumber(handleErrors))
               .pipe(potgen({ domain: pkg.name, package: 'gg-woo-feed' }))
               .pipe(gulp.dest(`languages/${pkg.name}.pot`))
})

gulp.task('clean', () => {
    return del([
        'assets/js/**/*.{js,map}',
        'assets/css/**/*.{css,map}',
    ])
})

gulp.task('dummy:main', () => {
    return merge([
        gulp.src('*.php')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk')),
        gulp.src('*.txt')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk')),
        gulp.src('*.html')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk'))
    ]);
})

gulp.task('dummy:assets', () => {
    return gulp.src('assets/**/*')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk/assets'));
})

gulp.task('dummy:inc', () => {
    return gulp.src('inc/**/*')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk/inc'));
})

gulp.task('dummy:languages', () => {
    return gulp.src('languages/**/*')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk/languages'));
})

gulp.task('dummy:vendor', () => {
    return gulp.src('vendor/**/*')
            .pipe(debug())
            .pipe(gulp.dest('dummy_data/'+pkg.name+'/trunk/vendor'));
})


gulp.task('watch', () => {
    browserSync.init({
        open: false,
        proxy: 'localhost/wordpress/woocommerce-feed',
    })

    gulp.watch('src/sass/admin/**/*.scss', gulp.series(['scss']))
    gulp.watch('src/sass/admin/components/*.scss', gulp.series(['scss']))
    gulp.watch('src/js/admin/**/*.js', gulp.series(['babel']))
    gulp.watch('src/js/admin/*.js', gulp.series(['babel']))
    
    if (fs.existsSync(dummyPlgDir)) {
        gulp.watch(['*.php', '*.txt', '*.html'], gulp.series(['dummy:main']))
        gulp.watch('assets/', gulp.series(['dummy:assets']))
        gulp.watch('inc/', gulp.series(['dummy:inc']))
        gulp.watch('languages/', gulp.series(['dummy:languages']))
        gulp.watch('vendor/', gulp.series(['dummy:vendor']))
    }

})

gulp.task('js', gulp.series(['babel', 'minify:js']))
gulp.task('css', gulp.series(['scss', 'minify:css']))
gulp.task('default', gulp.series(['clean', 'css', 'js', 'i18n']))

gulp.task('dummy', gulp.series(['dummy:main', 'dummy:assets', 'dummy:inc', 'dummy:languages', 'dummy:vendor']))