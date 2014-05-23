<?php

namespace {%= prefix %};

/**
 * Composer autoloader
 */
require_once 'vendor/autoload.php';

use tad\interfaces\FunctionsAdapter;
use tad\adapters\Functions;
use tad\utils\Script;

/**
 * WP Theme functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WP Theme
 * @since 0.1.0
 */

/**
 * The main theme class
 */
class Theme
{
    private $version = null;
    private $path = null;
    private $uri = null;
    private $prefix = null;
    private $jsAssets = null;
    private $cssAssets = null;
    
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
    
    public function __construct(\tad\interfaces\FunctionsAdapter $f = null)
    {
        if (is_null($f)) {
            $f = new Functions();
        }
        $this->f = $f;
        $this->initVars();
    }
    
    private function initVars()
    {
        $this->version = '0.1.0';
        $this->path = dirname(__FILE__);
        $this->uri = $this->f->get_template_directory_uri();
        $this->prefix = "{%= prefix %}";
        $this->jsAssets = $this->uri . '/assets/js';
        $this->cssAssets = $this->uri . '/assets/css';
    }
    
    public static function get($key)
    {
        if (!is_string($key)) {
            throw new \BadMethodCallException("Key must be a string.", 1);
        };
        return self::$instance->$key or null;
    }
    
    public static function init()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        self::$instance->hook();
    }
    
    public function hook()
    {
        $this->f->add_action('after_setup_theme', array($this, 'setup'));
        $this->f->add_action('wp_enqueue_scripts', array($this, 'enqueueScriptsAndStyles'));
        $this->f->add_action('wp_head', array($this, 'filterHeaderMeta'));
    }
    
    /**
     * Set up theme defaults and register supported WordPress features.
     *
     * @uses load_theme_textdomain() For translation/localization support.
     *
     * @since 0.1.0
     */
    public function setup()
    {
        
        /**
         * Makes WP Theme available for translation.
         *
         * Translations can be added to the /lang directory.
         */
        $this->f->load_theme_textdomain($this->prefix, $this->path . '/languages');
    }
    
    /**
     * Enqueue scripts and styles for front-end.
     *
     * @since 0.1.0
     */
    function enqueueScriptsAndStyles()
    {
        $this->f->wp_enqueue_script($this->prefix, Script::suffix($this->jsAssets . "/{%= js_safe_name %}.js"), array(), null, true);
        
        $this->f->wp_enqueue_style($this->prefix, Script::suffix($this->cssAssets . "/{%= js_safe_name %}.css"), array(), null);
    }
    
    /**
     * Add humans.txt to the <head> element.
     */
    function filterHeaderMeta()
    {
        $humans = '<link type="text/plain" rel="author" href="' . $this->uri . '/humans.txt" />';
        
        echo $this->f->apply_filters('{%= prefix %}_humans', $humans);
    }
}

/**
 * Kickstart the theme
 */
Theme::init();

