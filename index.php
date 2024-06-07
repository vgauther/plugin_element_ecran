<?php
/*
Plugin Name: Mon Plugin Commande
Description: Ajouter des éléments personnalisés à la page de modification des commandes.
Version: 1.0
Author: Nivo-Web
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('add_meta_boxes', 'ajouter_metabox_commande');

function ajouter_metabox_commande() {
    add_meta_box(
        'mon_metabox_commande',        // ID de la métabox
        'Commercial', // Titre de la métabox
        'afficher_contenu_metabox',     // Fonction de callback
        'shop_order',                  // Écran où ajouter la métabox
        'side',                        // Contexte (normal, side, etc.)
        'default'                      // Priorité
    );
}

function afficher_contenu_metabox($post) {
    // Utiliser wp_nonce_field pour la sécurité
    wp_nonce_field('mon_metabox_commande_nonce', 'mon_metabox_commande_nonce');
    
    // Récupérer les valeurs actuelles
    $valeur_personnalisee = get_post_meta($post->ID, 'nivo_pcc', true);
    
    // Afficher le champ de saisie
    echo '<p style="text-transform: capitalize;">' . esc_attr($valeur_personnalisee) . ' <p/>';
}

// add_action('save_post', 'sauvegarder_metabox_commande');

// function sauvegarder_metabox_commande($post_id) {
//     // Vérifier la nonce pour la sécurité
//     if (!isset($_POST['mon_metabox_commande_nonce']) || !wp_verify_nonce($_POST['mon_metabox_commande_nonce'], 'mon_metabox_commande_nonce')) {
//         return;
//     }
    
//     // Vérifier les autorisations de l'utilisateur
//     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
//         return;
//     }

//     if (isset($_POST['post_type']) && 'shop_order' === $_POST['post_type']) {
//         if (!current_user_can('edit_shop_order', $post_id)) {
//             return;
//         }
//     }

//     // Vérifier et sauvegarder la valeur personnalisée
//     if (isset($_POST['mon_valeur_personnalisee'])) {
//         update_post_meta($post_id, '_mon_valeur_personnalisee', sanitize_text_field($_POST['mon_valeur_personnalisee']));
//     }
// }

// Ajouter une colonne personnalisée dans le tableau des commandes
add_filter('manage_edit-shop_order_columns', 'ajouter_colonne_commande_personnalisee_a_la_fin');

function ajouter_colonne_commande_personnalisee_a_la_fin($columns) {
    // Ajouter la colonne personnalisée à la fin
    $columns['column_nivopcc'] = __('Commercial', 'textdomain');
    return $columns;
}


// Afficher le contenu de la colonne personnalisée
add_action('manage_shop_order_posts_custom_column', 'afficher_contenu_colonne_commande_personnalisee', 10, 2);

function afficher_contenu_colonne_commande_personnalisee($column, $post_id) {
    if ('column_nivopcc' === $column) {
        $valeur_personnalisee = get_post_meta($post_id, 'nivo_pcc', true);
        if (!empty($valeur_personnalisee)) {
            echo esc_html(ucfirst($valeur_personnalisee));
        } else {
            echo __('N/A', 'textdomain');
        }
    }
}

// Rendre la colonne personnalisée triable
add_filter('manage_edit-shop_order_sortable_columns', 'rendre_colonne_personnalisee_triable');

function rendre_colonne_personnalisee_triable($columns) {
    $columns['column_nivopcc'] = 'column_nivopcc';
    return $columns;
}

add_action('pre_get_posts', 'trier_par_colonne_personnalisee');

function trier_par_colonne_personnalisee($query) {
    if (!is_admin()) {
        return;
    }
    $orderby = $query->get('orderby');
    if ('column_nivopcc' === $orderby) {
        $query->set('meta_key', 'nivo_pcc');
        $query->set('orderby', 'meta_value');
    }
}


?>