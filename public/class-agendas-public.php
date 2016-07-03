<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Agendas
 * @subpackage Agendas/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Agendas
 * @subpackage Agendas/public
 * @author     paul lindquist <paul.lindquist@gmail.com>
 */
class Agendas_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $agendas    The ID of this plugin.
     */
    private $agendas;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $agendas       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $agendas, $version ) {

        $this->agendas = $agendas;
        $this->version = $version;

    }

    /**
     * Write out shortcode contents
     *
     * @since    1.0.2
     */
    public function do_shortcode() {
        Mustache_Autoloader::register();

        global $wpdb;

        $agendas_sql = 'SELECT ' . $wpdb->prefix .'agendas.id AS agenda_id, DATE_FORMAT(' . $wpdb->prefix . 'agendas.agenda_date, \'%b %d, %Y\') as display_date, agenda_postmeta.meta_value AS agenda_file, minutes_postmeta.meta_value AS minutes_file FROM ' . $wpdb->prefix . 'agendas ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'posts AS agenda_posts ON agenda_posts.id = ' . $wpdb->prefix . 'agendas.agenda ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'posts AS minutes_posts ON minutes_posts.id = ' . $wpdb->prefix . 'agendas.minutes ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'postmeta AS agenda_postmeta ON agenda_postmeta.post_id = agenda_posts.id AND agenda_postmeta.meta_key = "_wp_attached_file" ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'postmeta AS minutes_postmeta ON minutes_postmeta.post_id = minutes_posts.id AND minutes_postmeta.meta_key = "_wp_attached_file" ';
        $agendas_sql .= 'ORDER BY display_date DESC ';


        $result = $wpdb->get_results( $agendas_sql,  OBJECT);
        $wrapped_result = new stdClass();
        $wrapped_result->result = $result;

        $m = new Mustache_Engine( array(
            'loader' => new Mustache_Loader_FilesystemLoader(dirname( __FILE__ ) . '/views'),
        ));

        $html = '<div class="wrap">';
        $html .= $m->render('agendas_display', $wrapped_result) . "\n";
        return $html;
    }

}
