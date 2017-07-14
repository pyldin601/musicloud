const gulp = require('gulp');
const less = require('gulp-less');

gulp.task('css', () =>
    gulp
        .src('src/styles/*.less')
        .pipe(less())
        .pipe(gulp.dest('public/css'))
);

gulp.task('default', ['css']);
