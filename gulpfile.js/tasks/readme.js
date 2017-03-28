/**
 * -----------------------------------------------------------
 * readme
 * -----------------------------------------------------------
 *
 * Generate the readme.txt file for WordPress.org
 *
 */

var   gulp  = require( 'gulp' )
	, include = require( 'gulp-include' )
;

gulp.task( 'readme', function() {

	return gulp.src( '_readme/readme.txt' )
		.pipe( include() )
		.pipe( gulp.dest( './' ) );

} );
