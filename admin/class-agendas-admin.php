<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Agendas
 * @subpackage Agendas/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Agendas
 * @subpackage Agendas/admin
 * @author     paul lindquist <paul.lindquist@gmail.com>
 */
class Agendas_Admin {

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
     * @param      string    $agendas       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $agendas, $version ) {

        $this->agendas = $agendas;
        $this->version = $version;
        add_action( 'wp_ajax_delete_agenda', array( $this, 'delete_agenda') );
        add_action( 'wp_ajax_add_agenda', array( $this, 'add_agenda') );

    }

    /**
     *	Adds an agenda
     *
     * @since    1.0.1
     */
    public function add_agenda() {
        global $wpdb;

        $agenda = '';
        $agenda_date = '';
        $minutes = '';

        $wpdb->show_errors     = true;
        $wpdb->suppress_errors = false;


        if(!empty($_POST['agenda_date'])){
            $agenda_date = $_POST['agenda_date'];
        }
        if(!empty($_POST['minutes'])){
            $minutes = $_POST['minutes'];
        }
        if(!empty($_POST['agenda'])){
            $agenda = $_POST['agenda'];
        }

        $insert = array(
            'agenda' => $agenda,
            'agenda_date' => $agenda_date,
            'minutes' => $minutes
        );

        $wpdb->insert( $wpdb->prefix .'agendas', $insert );
        echo '{agenda_date:'. $agenda_date . ', agenda: ' . $agenda . ', minutes: ' . $minutes . '}';
        wp_die();
    }

    /**
     *	Deletes an agenda
     *
     * @since    1.0.1
     */

    public function delete_agenda() {
        global $wpdb;

        $agenda_id = $_POST['agenda_id'];

        $wpdb->delete( $wpdb->prefix .'agendas', array(
            'id' => $agenda_id
        ));
        wp_die();
    }

    /**
     *	Displays admin page
     *
     * @since    1.0.1
     */
    public function output_content() {
        global $wpdb;
        Mustache_Autoloader::register();

        $agendas_sql = 'SELECT ' . $wpdb->prefix . 'agendas.id AS agenda_id, DATE_FORMAT(agenda_date, \'%Y-%m-%d\') as display_date, agenda_postmeta.meta_value AS agenda_file, minutes_postmeta.meta_value AS minutes_file FROM ' . $wpdb->prefix . 'agendas ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'posts AS agenda_posts ON agenda_posts.id = '. $wpdb->prefix . 'agendas.agenda ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'posts AS minutes_posts ON minutes_posts.id = '. $wpdb->prefix . 'agendas.minutes ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'postmeta AS agenda_postmeta ON agenda_postmeta.post_id = agenda_posts.id AND agenda_postmeta.meta_key = "_wp_attached_file" ';
        $agendas_sql .= 'LEFT OUTER JOIN ' . $wpdb->prefix . 'postmeta AS minutes_postmeta ON minutes_postmeta.post_id = minutes_posts.id AND minutes_postmeta.meta_key = "_wp_attached_file" ';

        $result = $wpdb->get_results( $agendas_sql,  OBJECT);
        $wrapped_result = new stdClass();
        $wrapped_result->result = $result;

        $m = new Mustache_Engine( array(
            'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views'),
        ));

        $groups_table_name = $wpdb->prefix . 'groups';
        $html = '<div class="wrap form-horizontal">';
        $html .= $m->render('agendas_settings', $wrapped_result) . "\n";
        $html .= '</div>';
        echo $html;

    }

    /**
     * Adds the admin menu item
     *
     * @since    1.0.1
     */
    public function add_menu_page() {
        add_menu_page( 'Agendas', 'Agendas', 'manage_options', 'agendas_settings', array($this, 'output_content'), 'dashicons-book-alt', 6  );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Agendas_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Agendas_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->agendas, plugin_dir_url( __FILE__ ) . 'css/agendas-admin.css', array(), $this->version, 'all' );
        //wp_enqueue_style( $this->agendas . 'bootstrap', plugin_dir_url( __FILE__ ) . 'lib/bootstrap.min.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Agendas_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Agendas_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_media();
        wp_enqueue_script( $this->agendas, plugin_dir_url( __FILE__ ) . 'js/agendas-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->agendas . 'bootstrap', plugin_dir_url( __FILE__ ) . 'lib/bootstrap.min.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->agendas, 'agendas', array( 'ajaxurl' => admin_url(  'admin-ajax.php' ) ) );

    }

}
