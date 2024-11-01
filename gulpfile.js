var gulp = require('gulp'),
    watch = require('gulp-watch'),
    minify = require('gulp-minify'),
    cssmin = require('gulp-cssmin'),
    rename = require('gulp-rename'),
    imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    util = require('gulp-util'),
    sourcemaps = require('gulp-sourcemaps'),
    adminJSFiles = [
        'lib/select2/js/select2.full.min.js',
        'lib/jscolor/jscolor.js',
        'assets/js/js-functions.js',
        'assets/js/shop-ct-popup.js',
        'assets/js/admin/adminbar.js',
        'assets/js/admin/main.js',
        'assets/js/admin/admin.orders.js',
        'assets/js/admin/admin.orders-popup.js',
        'assets/js/admin/admin.checkouts.js',
        'assets/js/admin/admin.reviews.js',
        'assets/js/admin/admin.shipping-zones.js',
        'assets/js/admin/admin.products.js',
        'assets/js/admin/admin.products-popup.js',
        'assets/js/admin/admin-settings-popup.js',
        'assets/js/admin/admin.settings.js',
        'assets/js/admin/admin.terms.js',
        'assets/js/admin/admin.attributes.js',
        'assets/js/admin/display-settings.js',
        'assets/js/admin/admin.terms.js',
        'assets/js/admin/display-settings.js',
    ],
    config = {
            production: !!util.env.production
    };

gulp.task('adminjs', function(){
    return gulp.src(adminJSFiles)
        .pipe(sourcemaps.init())
        .pipe(concat('admin.js'))
        .pipe(sourcemaps.write())
        .pipe(config.production ? uglify() : util.noop())
        .pipe(gulp.dest('assets/js/admin'))
});

gulp.task('watch', function () {
        gulp.watch(adminJSFiles, gulp.series('adminjs'));
});

gulp.task('default', gulp.series('adminjs', 'watch'));
