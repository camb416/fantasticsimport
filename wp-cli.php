<?php
/**
 * Created by PhpStorm.
 * User: cbrowning
 * Date: 8/12/15
 * Time: 12:34 PM
 */

if (!defined('ABSPATH')) {
    die();
}

// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;

WP_CLI::add_command( 'fmagimport', 'FmagImport_CLI' );

class FmagImport_CLI extends WP_CLI_Command {

    /**
     * Import a Fantasticsmag Post
     *
     * ## EXAMPLES
     *
     * wp fmagimport 5198.inc
     *
     */
    public function stats( ) {
        WP_CLI::success( __( 'Successfully imported.', 'fmagimport' ) );
    }

    public function story( $args, $assoc_args  ) {
        if(isset($args[0])){
           // print_r($args[0]);
            $safe_filename = Helper::sanitizeFileName($args[0], 'linux');
           // print_r($safe_filename);
            $file = file_get_contents($safe_filename);
           // print_r($file);

            eval("\$nodes = $file;");
            print_r(createPostPost($nodes));
            //print_r(file_get_contents( $args[0] ));
        } else {
            WP_CLI::error( sprintf( 'you need to type a filename' ) );
        }

        WP_CLI::success( 'imported a story.' );
    }

    public function cover( $args, $assoc_args  ) {
        if(isset($args[0])){
            // print_r($args[0]);
            $safe_filename = Helper::sanitizeFileName($args[0], 'linux');
            // print_r($safe_filename);
            $file = file_get_contents($safe_filename);
            // print_r($file);

            eval("\$nodes = $file;");
            print_r(createCoverPost($nodes));
            //print_r(file_get_contents( $args[0] ));
        } else {
            WP_CLI::error( sprintf( 'you need to type a filename' ) );
        }

        WP_CLI::success( 'imported a cover.' );
    }

    /**
     * See Log Output
     *
     * ## OPTIONS
     *
     * empty: Leave it empty to output the log
     *
     * reset: Reset the log
     *
     * wp dreamobjects logs
     * wp dreamobjects logs reset
     *
     */

    public function log( $args, $assoc_args  ) {
        if ( isset( $args[0] ) && 'reset' !== $args[0] ) {
            WP_CLI::error( sprintf( __( '%s is not a valid command.', 'dreamobjects' ), $args[0] ) );
        } elseif ( 'reset' == $args[0] ) {
            DHDO::logger('reset');
            WP_CLI::success( 'Backup log reset' );
        } else {
            file_get_contents( './log.txt' );
        }
    }
}


/**
 * Helper holds a collection of static methods, useful for generic purposes
 * https://gist.github.com/noisebleed/940706
 */
class Helper {
    /**
     * Returns a safe filename, for a given platform (OS), by replacing all
     * dangerous characters with an underscore.
     *
     * @param string $dangerous_filename The source filename to be "sanitized"
     * @param string $platform The target OS
     *
     * @return Boolean string A safe version of the input filename
     */
    public static function sanitizeFileName($dangerous_filename, $platform = 'Unix') {
       // if (in_array(strtolower($platform), array('unix', 'linux')) {
            // our list of "dangerous characters", add/remove characters if necessary
        $dangerous_characters = array(" ", '"', "'", "&", "/", "\\", "?", "#");
    //} else {
        // no OS matched? return the original filename then...
        return $dangerous_filename;
    //}

        // every forbidden character is replace by an underscore
        return str_replace($dangerous_characters, '_', $dangerous_filename);
    }
}

// usage:
//$safe_filename = Helper::sanitizeFileName('#my  unsaf&/file\name?"');