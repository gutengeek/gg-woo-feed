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

gulp.task('watch', () => {
    browserSync.init({
        open: false,
        proxy: 'localhost/wordpress/woocommerce-feed',
    })

    gulp.watch('src/sass/admin/**/*.scss', gulp.series(['scss']))
    gulp.watch('src/sass/admin/components/*.scss', gulp.series(['scss']))
    gulp.watch('src/js/admin/**/*.js', gulp.series(['babel']))
    gulp.watch('src/js/admin/*.js', gulp.series(['babel']))
})

gulp.task('js', gulp.series(['babel', 'minify:js']))
gulp.task('css', gulp.series(['scss', 'minify:css']))
gulp.task('default', gulp.series(['clean', 'css', 'js', 'i18n']))
