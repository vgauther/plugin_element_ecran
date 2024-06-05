<?php
/*
Plugin Name: Mon Plugin Commande
Description: Ajouter des éléments personnalisés à la page de modification des commandes.
Version: 1.0
Author: Votre Nom
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('add_meta_boxes', 'ajouter_metabox_commande');

function ajouter_metabox_commande() {
    add_meta_box(
        'mon_metabox_commande',        // ID de la métabox
        'Informations Supplémentaires', // Titre de la métabox
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
    $valeur_personnalisee = get_post_meta($post->ID, '_mon_valeur_personnalisee', true);
    
    // Afficher le champ de saisie
    echo '<label for="mon_valeur_personnalisee">Ma Valeur Personnalisée:</label>';
    echo '<input type="text" id="mon_valeur_personnalisee" name="mon_valeur_personnalisee" value="' . esc_attr($valeur_personnalisee) . '" />';
}

add_action('save_post', 'sauvegarder_metabox_commande');

function sauvegarder_metabox_commande($post_id) {
    // Vérifier la nonce pour la sécurité
    if (!isset($_POST['mon_metabox_commande_nonce']) || !wp_verify_nonce($_POST['mon_metabox_commande_nonce'], 'mon_metabox_commande_nonce')) {
        return;
    }
    
    // Vérifier les autorisations de l'utilisateur
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'shop_order' === $_POST['post_type']) {
        if (!current_user_can('edit_shop_order', $post_id)) {
            return;
        }
    }

    // Vérifier et sauvegarder la valeur personnalisée
    if (isset($_POST['mon_valeur_personnalisee'])) {
        update_post_meta($post_id, '_mon_valeur_personnalisee', sanitize_text_field($_POST['mon_valeur_personnalisee']));
    }
}
?>
