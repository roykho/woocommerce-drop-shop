/*******************************************************************************
DEPENDENCIES
*******************************************************************************/

var gulp         = require( 'gulp' ),
	sass         = require( 'gulp-sass' ),
	uglify       = require( 'gulp-uglify' ),
	rename       = require( 'gulp-rename' ),
	concat       = require( 'gulp-concat' ),
	notify       = require( 'gulp-notify' ),
	plumber      = require( 'gulp-plumber' ),
	minifycss    = require( 'gulp-clean-css' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	project      = 'woocommerce-drop-shop';


/*******************************************************************************
FILE DESTINATIONS
*******************************************************************************/

var target = {
	sass_src : './plugin-assets/css/*.scss',
	css_dest : './plugin-assets/css',
	js_frontend_src : [
		'./plugin-assets/js/vendor/bootstrap-tooltip.js',
		'./plugin-assets/js/vendor/jquery.carouFredSel.js',
		'./plugin-assets/js/vendor/jquery.images.loaded.js',
		'./plugin-assets/js/drop-shop.src.js'
	],
	js_admin_src : [ './plugin-assets/js/admin.js' ],
	js_frontend_unminified_name : '.drop-shop.js',
	js_dest : './plugin-assets/js'
};


/*******************************************************************************
JS TASKS
*******************************************************************************/

gulp.task( 'js-compile', function() {
	gulp.src( target.js_frontend_src )
		.pipe( plumber() )
		.pipe( concat( 'drop-shop.js' ) )
		.pipe( gulp.dest( target.js_dest ) )
		.pipe( uglify() )
		.pipe( rename( function ( path ) {
			path.dirname += '';
			path.basename += '.min';
			path.extname = '.js'
		}))
		.pipe( gulp.dest( target.js_dest ) )
		.pipe( notify({ message: project + ': JS processed!'}) );
});

/*******************************************************************************
SASS TASKS
*******************************************************************************/

gulp.task( 'sass-compile', function() {
	gulp.src( [ './plugin-assets/css/vendor/bootstrap-tooltip.css', './plugin-assets/css/drop-shop.scss' ] )
		.pipe( plumber() )
		.pipe( concat( 'drop-shop.scss' ) )
		.pipe( sass() )
		.pipe( autoprefixer(
			'last 2 version',
			'> 1%',
			'ie 8',
			'ie 9',
			'ios 6',
			'android 4'
		))
		.pipe( minifycss() )
		.pipe( gulp.dest( target.css_dest ) )
		.pipe( notify( { message: project + ': SASS processed!' } ) );
});

/*******************************************************************************
GULP TASKS
*******************************************************************************/

gulp.task( 'default', ['js-compile', 'sass-compile'], function() {
	gulp.watch( target.js_frontend_src, ['js-compile'] );
	gulp.watch( target.sass_src, ['sass-compile'] );
});