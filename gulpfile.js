const gulp = require('gulp');
const less = require('gulp-less');

gulp.task('less', () =>
    gulp
        .src('src/styles/*.less')
        .pipe(less())
        .pipe(gulp.dest('public/css'))
);

gulp.task('css', () =>
  gulp
    .src('src/styles/*.css')
    .pipe(gulp.dest('public/css'))
);

gulp.task('default', ['less', 'css']);
