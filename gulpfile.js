const { src, dest, series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const csso = require('gulp-csso');
const autoprefixer = require('gulp-autoprefixer');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const { watch, reload } = require('browser-sync').create();

function scss() {
    return src('app/source/scss/style.scss')
        .pipe(sass())
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(csso())
        .pipe(dest('backend/view/static/css'))
}

function js() {
    return src(['app/js/vars.js', 'app/js/functions.js', 'app/js/modules/**.js', 'app/js/UI/**.js', 'app/js/util/**', 'app/js/script.js','app/js/main.js', 'app/js/application.js', 'app/js/chat.js', 'app/js/create-2.js', 'app/js/admin/**'])
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(uglify())
        .pipe(concat('all.js'))
        .pipe(dest('backend/view/static/js'))
}

function serve() {
    watch('app/source/scss/**', series(scss)).on('change', reload);
    watch('app/js/**', series(js)).on('change', reload);
}

exports.serve = series(scss, js, serve);