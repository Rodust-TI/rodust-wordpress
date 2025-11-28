const gulp = require('gulp');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');

// PostCSS plugins
const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

// Paths
const paths = {
    css: {
        src: './src/style.css',
        dest: './assets/css/',
        watch: ['./src/**/*.css', './**/*.php']
    },
    js: {
        src: './src/**/*.js',
        dest: './assets/js/',
        watch: './src/**/*.js'
    }
};

// CSS task with Tailwind
function css(done) {
    return gulp.src(paths.css.src)
        .pipe(postcss([
            tailwindcss(),
            autoprefixer()
        ]))
        .pipe(rename('style.css'))
        .pipe(gulp.dest(paths.css.dest))
        .on('end', done);
}

// CSS build (with minification)
function cssBuild(done) {
    return gulp.src(paths.css.src)
        .pipe(postcss([
            tailwindcss(),
            autoprefixer(),
            cssnano()
        ]))
        .pipe(rename('style.css'))
        .pipe(gulp.dest(paths.css.dest))
        .on('end', done);
}

// JavaScript task
function js(done) {
    return gulp.src(paths.js.src)
        .pipe(concat('script.js'))
        .pipe(gulp.dest(paths.js.dest))
        .on('end', done);
}

// JavaScript build (with minification)
function jsBuild(done) {
    return gulp.src(paths.js.src)
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.js.dest))
        .on('end', done);
}

// Watch task
function watch() {
    gulp.watch(paths.css.watch, css);
    gulp.watch(paths.js.watch, js);
}

// Default task (desenvolvimento)
const dev = gulp.series(css, js, watch);

// Build task (produção)
const build = gulp.series(cssBuild, jsBuild);

// Export tasks
exports.css = css;
exports.js = js;
exports.watch = watch;
exports.build = build;
exports.default = dev;