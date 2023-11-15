<?php

/*
 * Plugin Name: Open Sea Dragon for Wordpress
 * Description: Permette di inserire tramite shortcode immagini in formato DZI navigabili e ingrandibili con Open Sea Dragon. SI possono inserire punti di interesse con collegamenti a pagine interne del sito
 * Author: Fabio Lelli
 * Plugin URI: https://github.com/FabioLelli/WP-Open_Sea_Dragon
 * Author URI: https://archiviodistatoravenna.cultura.gov.it/
 * Version: 1.0.0
 * License: GPL v3
 */


if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'MY_PLUGIN_VERSION', '1.0.0' );


function dragon_shortcodes_init() {
    add_shortcode( 'dragon', 'dragon_fl_shortcode' );
    add_action('wp_enqueue_scripts', 'dragon_fl_add_assets');
}

add_action( 'init', 'dragon_shortcodes_init' );
add_action('init', 'dragon_fl_custom_post_type');


function dragon_fl_custom_post_type() {
    register_post_type('fl_dragon',
        array(
            'supports'     => array('title'),
            'labels'      => array(
                'name'          => __( 'Immagini dragons', 'textdomain' ),
                'singular_name' => __( 'Immagine dragon', 'textdomain' ),
            ),
            'public'      => true,
            'has_archive' => true,
        )
    );
}

add_action('admin_init', 'fl_dragon_add_meta_boxes');

function fl_dragon_add_meta_boxes() {
    add_meta_box( 'fl_luoghi', 'Luoghi', 'luoghi_fl_meta_box_display', 'fl_dragon', 'normal', 'default');
    add_meta_box( 'fl_dati_immagine', 'Dati immagine', 'dati_immagine_fl_meta_box_display', 'fl_dragon', 'normal', 'default');
}

function dati_immagine_fl_meta_box_display () {
    global $post;
    $dati_immmagine = get_post_meta($post->ID, 'fl_dati_immagine',true);
    wp_nonce_field( 'fl_dati_immagine_meta_box_nonce', 'fl_dati_immagine_meta_box_nonce' );
    if ($dati_immmagine) { ?>
        <table id="dati_immagine" width="100%">
        <tr>
            <td><label>Altezza viewer</label> <input type="number" name="altezza_viewer" placeholder="Altezza" value="<?php if($dati_immmagine['altezza_viewer'] != '') echo esc_attr( $dati_immmagine['altezza_viewer'] ); ?>"></td>
            <td><label>Larghezza viewer</label> <input type="number" name="larghezza_viewer" placeholder="Larghezza" value="<?php if($dati_immmagine['larghezza_viewer'] != '') echo esc_attr( $dati_immmagine['larghezza_viewer'] ); ?>"></td>
            <td><label>Percorso file DZI (rispetto a wp-content) </label><input type="text" name="sorgente_immagini" placeholder="Percorso DZI" value="<?php if($dati_immmagine['sorgente_immagini'] != '') echo esc_attr( $dati_immmagine['sorgente_immagini'] ); ?>"></td>
        </tr>
        </table>
    <?php } else { ?>
            <table id="dati_immagine" width="100%">
        <tr>
            <td><label>Altezza viewer</label> <input type="number" name="altezza" placeholder="Altezza"></td>
            <td><label>Larghezza viewer</label> <input type="number" name="larghezza" placeholder="Larghezza"></td>
            <td><label>Percorso file DZI (rispetto a wp-content) </label><input type="text" name="sorgente_immagini" placeholder="Percorso DZI"></td>
        </tr>
    </table>
    <?php }
}

function luoghi_fl_meta_box_display() {
    global $post;
    $elenco_luoghi = get_post_meta($post->ID, 'fl_luoghi_data', true);
    $codice_ID = $post->ID;
     wp_nonce_field( 'fl_luoghi_meta_box_nonce', 'fl_luoghi_meta_box_nonce' );
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-row' ).on('click', function() {
            var row = $( '.empty-row.screen-reader-text' ).clone(true);
            row.removeClass( 'empty-row screen-reader-text' );
            row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
            return false;
        });

        $( '.remove-row' ).on('click', function() {
            $(this).parents('tr').remove();
            return false;
        });
    });
  </script>

  <table id="repeatable-fieldset-one" width="100%">
  <tbody>
    <?php
     if ( $elenco_luoghi ) :
      foreach ($elenco_luoghi as $field ) {
    ?>
    <tr>
      <td width="15%"><label>Posizione orizzontale</label> <input type="text"  placeholder="X" name="x[]" value="<?php if($field['x'] != '') echo esc_attr( $field['x'] ); ?>" /></td> 
      <td width="15%"><label>Posizione verticale </label><input type="text" placeholder="Y" name="y[]" value="<?php if ($field['y'] != '') echo esc_attr( $field['y'] ); ?>" /></td>
      <td width="15%"><label>Luogo</label> <input type="text" placeholder="Luogo" name="luogo[]" value="<?php if ($field['luogo'] != '') echo esc_attr( $field['luogo'] ); ?>" /></td>
      <td><label>Larghezza etichetta</label><input type="number" placeholder="Larghezza" name="larghezza[]" value="<?php if ($field['larghezza'] != '') echo esc_attr( $field['larghezza'] ); ?>" /></td>
      <td width="25%"><label>Link</label>
          <?php if ($field['link'] != '') {
            $dropdown_args = array('selected' => floatval($field['link']), 'name' => 'link[]');
            wp_dropdown_pages($dropdown_args);
          } else {
            wp_dropdown_pages(array('name' => 'link[]'));
          } ?>
      </td>
      <td width="15%"><a class="button remove-row" href="#1">Elimina</a></td>
    </tr>
    <?php
    }
    else :
    ?>
    <tr>
      <td width="15%"><label>Posizione orizzontale</label> <input type="text"  placeholder="X" name="x[]" value="" /></td>
      <td width="15%"><label>Posizione verticale </label><input type="text" placeholder="Y" name="y[]" value=""/></td>
      <td width="15%"><label>Luogo</label> <input type="text" placeholder="Luogo" name="luogo[]" value=""/></td>
      <td width="15%"><label>Larghezza etichetta</label><input type="number" placeholder="Larghezza" name="larghezza[]" value=""/></td>
          <td width="25%"><label>Link</label><br><?php wp_dropdown_pages(array('name' => 'link[]')); ?></td>
      <td width="25%"><a class="button remove-row" href="#1">Elimina</a></td>
    </tr>
    <?php endif; ?>

    <!-- empty hidden one for jQuery -->
    <tr class="empty-row screen-reader-text">
      <td width="15%"><label>Posizione orizzontale</label> <input type="text"  placeholder="X" name="x[]" value="" /></td>
      <td width="15%"><label>Posizione verticale </label><input type="text" placeholder="Y" name="y[]" value=""/></td>
      <td width="15%"><label>Luogo</label> <input type="text" placeholder="Nome" name="luogo[]" value=""/></td>
      <td width="15%"><label>Larghezza etichetta</label><input type="number" placeholder="Larghezza" name="larghezza[]" value=""/></td>
          <td width="25%"><label>Link</label><br><?php wp_dropdown_pages(array('name' => 'link[]')); ?></td>
      <td width="25%"><a class="button remove-row" href="#1">Elimina</a></td>
    </tr>
  </tbody>
</table>
<p><a id="add-row" class="button" href="#">Aggiungi</a></p>

  <?php

  if ($codice_ID && get_post_status($codice_ID) == 'publish') {
    echo "<table><tr><td>Utilizza questo codice:<code>[dragon id='" . $codice_ID . "'][/dragon]</code></tr></td></table>";
  } 
}

add_action('save_post', 'fl_repeatable_meta_box_save');
add_action('save_post', 'fl_dati_immagine_meta_box_save');

function fl_dati_immagine_meta_box_save($post_id) {
    if ( ! isset( $_POST['fl_dati_immagine_meta_box_nonce'] ) ||
    ! wp_verify_nonce( $_POST['fl_dati_immagine_meta_box_nonce'], 'fl_dati_immagine_meta_box_nonce' ) )
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'fl_dati_immagine', true); //C'è un problema di sanitizzazione?

    $new = array(
        'altezza_viewer' => $_POST['altezza_viewer'],
        'larghezza_viewer' => $_POST['larghezza_viewer'],
        'sorgente_immagini' => $_POST['sorgente_immagini']
    );

    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'fl_dati_immagine', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'fl_dati_immagine', $old );
}

function fl_repeatable_meta_box_save($post_id) {
    if ( ! isset( $_POST['fl_luoghi_meta_box_nonce'] ) ||
    ! wp_verify_nonce( $_POST['fl_luoghi_meta_box_nonce'], 'fl_luoghi_meta_box_nonce' ) )
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'fl_luoghi_data', true);
    $new = array();
    $x = $_POST['x'];
    $y = $_POST['y'];
    $luogo = $_POST['luogo'];
    $larghezza = $_POST['larghezza'];
    $link = $_POST['link'];
     $count = count( $x );
     for ( $i = 0; $i < $count; $i++ ) {
        if ( $x[$i] != '' ) :
            $new[$i]['x'] = stripslashes( strip_tags( $x[$i] ) );
            $new[$i]['y'] = stripslashes( strip_tags( $y[$i] ) );
            $new[$i]['luogo'] = stripslashes( strip_tags( $luogo[$i] ) );
            $new[$i]['larghezza'] = stripslashes( strip_tags( $larghezza[$i] ) );
            $new[$i]['link'] = stripslashes( strip_tags( $link[$i] ) );
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'fl_luoghi_data', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'fl_luoghi_data', $old );
}


function dragon_fl_add_assets () {
    wp_enqueue_script('openseadragon', plugin_dir_url( __DIR__ ) . 'dragon-fl/public/js/openseadragon.min.js');
    wp_enqueue_style('custom_dragon_fl_css', plugin_dir_url( __DIR__ ) . 'dragon-fl/public/css/custom_fl.css');
    
}


/**
 * Lo shortcode
 *
 * @param array  $atts    Shortcode attributes. Default empty.
 * @param string $content Shortcode content. Default null.
 * @param string $tag     Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 */
function dragon_fl_shortcode( $atts = [], $content = null, $tag = '' ) {
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    $dragon_fl_atts = shortcode_atts(
        array(
            'id' => ""
        ), $atts, $tag
    );

    $elenco_luoghi = get_post_meta($dragon_fl_atts['id'], 'fl_luoghi_data', true);
    $dati_immmagine = get_post_meta($dragon_fl_atts['id'], 'fl_dati_immagine', true);

    //Controllo errori TODO

    wp_enqueue_script('custom_dragon_fl', plugin_dir_url( __DIR__ ) . 'dragon-fl/public/js/custom_dragon_fl.js', 'openseadragonHTMLelements', false, true);
    wp_localize_script('custom_dragon_fl', 'fl_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'content_url' => content_url()));
    wp_localize_script( 'custom_dragon_fl', 'fl_map', array(
                                                    'prefixUrl' => plugin_dir_url( __DIR__ ). 'dragon-fl/public/js/images/',
                                                    'tileSources' => content_url() . "/" . $dati_immmagine['sorgente_immagini'],
                                                    'luoghi' => json_encode($elenco_luoghi)
                                                ) 
    );

    $o = "<div id='dragon_map_fl' style='width: " . $dati_immmagine['larghezza_viewer'] . "px; height: ". $dati_immmagine['altezza_viewer'] ."px; margin: 0;'></div>";
    foreach ($elenco_luoghi as $luogo) {
        $o .= "<div id='overlay-". $luogo['link']  . "' style='width:" . $luogo['larghezza'] . "px;'><a class='fl_segnalibri' href='". get_permalink($luogo['link']) ."'>" . $luogo['luogo'] . "</a></div>";
    }
    
    return $o;
} ?>