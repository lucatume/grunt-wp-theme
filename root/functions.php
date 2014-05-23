<?php

namespace {%= prefix %};

/**
 * Composer autoloader
 */
require_once 'vendor/autoload.php';

use tad\interfaces\FunctionsAdapter;
use tad\adapterts\Functions;
use tad\utils\Script;

/**
 * {%= title %} functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package {%= title %}
 * @since 0.1.0
 */

 // Useful global constants
define( '{%= prefix_caps %}_VERSION', '0.1.0' );
define( '{%= prefix_caps %}_PATH', dirname(__FILE__) );
define( '{%= prefix_caps %}_URI', get_template_directory_uri() );

/**
 * The main theme class
 */
class Theme
{
    const VERSION = {%= prefix_caps %}_VERSION;
    const PATH = {%= prefix_caps %}_PATH;
    const URI = {%= prefix_caps %}_URI;
    const JS_URI = {%= prefix_caps %}_URI . '/assets/js';
    const CSS_URI = {%= prefix_caps %}_URI . '/assets/css';
    const PREFIX = {%= prefix %};
    /**
     * An instance of the theme class, meant to be singleton.
     *
     * @var {%= prefix %}\Theme
     */
    private static $instance = null;
    /**
     * The global functions adapter used to isolate the class.
     *
     * @var tad\adapters\Functions or a mock object.
     */
    private $f = null;

    public function __construct(\tad\interfaces\FunctionsAdapter $f = null){
        $f || $this->$f = new Functions();
    }

    public static function init(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        $this->hook();
    }

    public function hook()
    {
        $this->f->add_action( 'after_setup_theme', array($this, 'setup') );
        $this->f->add_action( 'wp_enqueue_scripts', array($this, 'enqueueScriptsAndStyles') );
        $this->f->add_action( 'wp_head', array($this, 'filterHeaderMeta') );
    }
     /**
      * Set up theme defaults and register supported WordPress features.
      *
      * @uses load_theme_textdomain() For translation/localization support.
      *
      * @since 0.1.0
      */
     public function setup() {
        /**
         * Makes {%= title %} available for translation.
         *
         * Translations can be added to the /lang directory.
         */
        load_theme_textdomain( self::PREFIX, self::PATH . '/languages' );
    }
     /**
      * Enqueue scripts and styles for front-end.
      *
      * @since 0.1.0
      */
     function enqueueScriptsAndStyles() {
       wp_enqueue_script( self::PREFIX, Script::suffix(self::JS_URI . "/{%= js_safe_name %}.js"), array(), null, true );

       wp_enqueue_style( self::PREFIX, Script::suffix(self::CSS_URI . "/{%= js_safe_name %}.css"), array(), null );
    }

     /**
      * Add humans.txt to the <head> element.
      */
     function filterHeaderMeta() {
       $humans = '<link type="text/plain" rel="author" href="' . self::URI . '/humans.txt" />';

       echo apply_filters( '{%= prefix %}_humans', $humans );
    }
}

/**
 * Kickstart the theme
 */
Theme::init();

