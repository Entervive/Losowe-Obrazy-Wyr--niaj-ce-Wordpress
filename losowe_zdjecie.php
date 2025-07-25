<?php
/**
 * Plugin Name: Losowe Obrazy Wyróżniające
 * Plugin URI: https://entervive.pl
 * Description: Automatycznie ustawia losowe obrazy wyróżniające dla postów, które ich nie posiadają z wybranych obrazów.
 * Version: 1.0.0
 * Author: Aleksander Staszków (Entervive)
 * Author URI: https://entervive.pl
 * Text Domain: random-featured-image
 * Domain Path: /languages
 */

// Zapobieganie bezpośredniemu dostępowi
if (!defined('ABSPATH')) {
    exit;
}

class RandomFeaturedImage {
    
    private $option_name = 'rfi_selected_images';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_rfi_get_images', array($this, 'ajax_get_images'));
        add_action('wp_ajax_rfi_save_images', array($this, 'ajax_save_images'));
        add_action('wp_ajax_rfi_apply_random_images', array($this, 'ajax_apply_random_images'));
        
        // Hook do automatycznego ustawiania obrazów przy publikacji
        add_action('transition_post_status', array($this, 'auto_set_featured_image'), 10, 3);
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Losowe Obrazy Wyróżniające',
            'Losowe Obrazy Wyróżniające',
            'manage_options',
            'random-featured-image',
            array($this, 'admin_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_random-featured-image') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        
        wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            var selectedImages = [];
            
            // Ładowanie zapisanych obrazów
            loadSavedImages();
            
            function loadSavedImages() {
                $.post(ajaxurl, {
                    action: "rfi_get_images"
                }, function(response) {
                    if (response.success) {
                        selectedImages = response.data;
                        displayImages();
                    }
                });
            }
            
            // Przycisk wyboru obrazów
            $("#select-images").click(function(e) {
                e.preventDefault();
                
                var mediaUploader = wp.media({
                    title: "Wybierz obrazy do losowego przypisywania",
                    multiple: true,
                    library: {
                        type: "image"
                    }
                });
                
                mediaUploader.on("select", function() {
                    var attachments = mediaUploader.state().get("selection").toJSON();
                    
                    attachments.forEach(function(attachment) {
                        if (selectedImages.findIndex(img => img.id === attachment.id) === -1) {
                            selectedImages.push({
                                id: attachment.id,
                                url: attachment.url,
                                title: attachment.title
                            });
                        }
                    });
                    
                    displayImages();
                });
                
                mediaUploader.open();
            });
            
            // Wyświetlanie obrazów
            function displayImages() {
                var html = "";
                selectedImages.forEach(function(image, index) {
                    html += `
                        <div class="rfi-image-item" style="display: inline-block; margin: 10px; text-align: center;">
                            <img src="${image.url}" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd;" />
                            <br>
                            <button type="button" class="button remove-image" data-index="${index}" style="margin-top: 5px;">Usuń</button>
                        </div>
                    `;
                });
                $("#selected-images").html(html);
                $("#images-count").text(selectedImages.length);
            }
            
            // Usuwanie obrazu
            $(document).on("click", ".remove-image", function() {
                var index = $(this).data("index");
                selectedImages.splice(index, 1);
                displayImages();
            });
            
            // Zapisywanie obrazów
            $("#save-images").click(function() {
                $.post(ajaxurl, {
                    action: "rfi_save_images",
                    images: selectedImages
                }, function(response) {
                    if (response.success) {
                        alert("Obrazy zostały zapisane!");
                    } else {
                        alert("Błąd podczas zapisywania obrazów.");
                    }
                });
            });
            
            // Zastosuj do postów bez obrazów
            $("#apply-to-posts").click(function() {
                if (!confirm("Czy na pewno chcesz zastosować losowe obrazy do wszystkich postów bez obrazów wyróżniających?")) {
                    return;
                }
                
                var button = $(this);
                button.prop("disabled", true).text("Przetwarzanie...");
                
                $.post(ajaxurl, {
                    action: "rfi_apply_random_images"
                }, function(response) {
                    button.prop("disabled", false).text("Zastosuj do istniejących postów");
                    
                    if (response.success) {
                        alert(`Pomyślnie dodano obrazy wyróżniające do ${response.data.updated} postów.`);
                    } else {
                        alert("Wystąpił błąd podczas przetwarzania.");
                    }
                });
            });
        });
        ');
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Losowe Obrazy Wyróżniające</h1>
            
            <div class="card" style="max-width: 800px;">
                <h2>Konfiguracja</h2>
                <p>Wybierz obrazy, które będą losowo przypisywane jako obrazy wyróżniające do postów, które ich nie posiadają.</p>
                
                <p>
                    <button id="select-images" class="button button-primary">Wybierz obrazy</button>
                    <button id="save-images" class="button">Zapisz obrazy</button>
                </p>
                
                <p><strong>Wybrane obrazy (<span id="images-count">0</span>):</strong></p>
                <div id="selected-images"></div>
                
                <hr>
                
                <h3>Akcje</h3>
                <p>
                    <button id="apply-to-posts" class="button button-secondary">Zastosuj do istniejących postów</button>
                    <br><small>Doda losowe obrazy wyróżniające do wszystkich postów, które obecnie ich nie posiadają.</small>
                </p>
                
                <hr>
                
                <h3>Automatyczne działanie</h3>
                <p>Plugin automatycznie ustawi losowy obraz wyróżniający przy publikacji nowych postów, jeśli nie został jeszcze ustawiony.</p>
            </div>
        </div>
        <?php
    }
    
    public function ajax_get_images() {
        $images = get_option($this->option_name, array());
        wp_send_json_success($images);
    }
    
    public function ajax_save_images() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Brak uprawnień');
        }
        
        $images = isset($_POST['images']) ? $_POST['images'] : array();
        update_option($this->option_name, $images);
        wp_send_json_success();
    }
    
    public function ajax_apply_random_images() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Brak uprawnień');
        }
        
        $images = get_option($this->option_name, array());
        
        if (empty($images)) {
            wp_send_json_error('Brak wybranych obrazów');
        }
        
        // Znajdź posty bez obrazów wyróżniających
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        $updated = 0;
        foreach ($posts as $post) {
            $random_image = $images[array_rand($images)];
            if (set_post_thumbnail($post->ID, $random_image['id'])) {
                $updated++;
            }
        }
        
        wp_send_json_success(array('updated' => $updated));
    }
    
    public function auto_set_featured_image($new_status, $old_status, $post) {
        // Sprawdź czy to publikacja posta
        if ($new_status !== 'publish' || $post->post_type !== 'post') {
            return;
        }
        
        // Sprawdź czy post już ma obraz wyróżniający
        if (has_post_thumbnail($post->ID)) {
            return;
        }
        
        // Pobierz wybrane obrazy
        $images = get_option($this->option_name, array());
        
        if (!empty($images)) {
            $random_image = $images[array_rand($images)];
            set_post_thumbnail($post->ID, $random_image['id']);
        }
    }
}

// Inicjalizacja pluginu
new RandomFeaturedImage();

// Hook aktywacji - można dodać domyślne ustawienia
register_activation_hook(__FILE__, function() {
    // Można tu dodać kod wykonywany przy aktywacji
});

// Hook deaktywacji
register_deactivation_hook(__FILE__, function() {
    // Można tu dodać kod wykonywany przy deaktywacji
});
?>