/**
 * Main Gulp File
 *
 * Requires all task files
 */

var gulp = require('gulp');

require( './tasks/readme.js' );

require( 'lifterlms-lib-tasks' )( gulp );
