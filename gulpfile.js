var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifycss = require('gulp-minify-css'),
    del = require('del');

var destination = 'public/styles',
    source = 'resources/assets/sass/**/*.scss';

gulp.task('clean', function(){
  return del(destination);
});

gulp.task('sass', function(){
  return gulp.src(source)
    .pipe(sass({
        includePaths: ['bower_components/foundation/scss'],
        outputStyle: 'expanded',
        errorLogToConsole:false,
        onError:function(error){
          console.log(error);
        }
    }))
    .pipe(minifycss({keepBreaks:true}))
    .pipe(gulp.dest(destination));
});

gulp.task('default', ['clean'], function() {
    gulp.start('sass');
});
