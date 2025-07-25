<?php
/**
 * Plugin Name: Losowe Obrazy Wyróżniające
 * Plugin URI: https://github.com/Entervive/Losowe-Obrazy-Wyr--niaj-ce-Wordpress
 * Description: Automatycznie ustawia losowe obrazy wyróżniające dla postów, które ich nie posiadają z wybranych obrazów.
 * Version: 1.0.1
 * Author: Aleksander Staszków (Entervive)
 * Author URI: https://entervive.pl
 * License: MIT
 * License URI: https://opensource.org/license/mit
 * Text Domain: losowe-obrazy-wyrozniajace
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 */

// Zapobieganie bezpośredniemu dostępowi
if (!defined('ABSPATH')) {
    exit;
}

class LosoweObrazyStyling
{

    private $option_name = 'lows_selected_images';

    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_lows_get_images', array($this, 'ajax_get_images'));
        add_action('wp_ajax_lows_save_images', array($this, 'ajax_save_images'));
        add_action('wp_ajax_lows_apply_random_images', array($this, 'ajax_apply_random_images'));

        // Hook do automatycznego ustawiania obrazów przy publikacji
        add_action('transition_post_status', array($this, 'auto_set_featured_image'), 10, 3);
    }

    public function add_admin_menu()
    {
        add_options_page(
            __('Losowe Obrazy Wyróżniające', 'losowe-obrazy-wyrozniajace'),
            __('Losowe Obrazy Wyróżniające', 'losowe-obrazy-wyrozniajace'),
            'manage_options',
            'losowe-obrazy-wyrozniajace',
            array($this, 'admin_page')
        );
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'settings_page_losowe-obrazy-wyrozniajace') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery');

        wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            var selectedImages = [];
            var nonce = "' . wp_create_nonce('lows_nonce') . '";
            
            // Ładowanie zapisanych obrazów
            loadSavedImages();
            
            function loadSavedImages() {
                $.post(ajaxurl, {
                    action: "lows_get_images",
                    nonce: nonce
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
                    title: "' . esc_js(__('Wybierz obrazy do losowego przypisywania', 'losowe-obrazy-wyrozniajace')) . '",
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
                        <div class="lows-image-item" style="display: inline-block; margin: 10px; text-align: center;">
                            <div style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; overflow: hidden;">
                                <img src="${image.url}" style="width: 100%; height: auto;" alt="${image.title}" />
                            </div>
                            <br>
                            <button type="button" class="button remove-image" data-index="${index}" style="margin-top: 5px;">' . esc_js(__('Usuń', 'losowe-obrazy-wyrozniajace')) . '</button>
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
                    action: "lows_save_images",
                    images: selectedImages,
                    nonce: nonce
                }, function(response) {
                    if (response.success) {
                        alert("' . esc_js(__('Obrazy zostały zapisane!', 'losowe-obrazy-wyrozniajace')) . '");
                    } else {
                        alert("' . esc_js(__('Błąd podczas zapisywania obrazów.', 'losowe-obrazy-wyrozniajace')) . '");
                    }
                });
            });
            
            // Zastosuj do postów bez obrazów
            $("#apply-to-posts").click(function() {
                if (!confirm("' . esc_js(__('Czy na pewno chcesz zastosować losowe obrazy do wszystkich postów bez obrazów wyróżniających?', 'losowe-obrazy-wyrozniajace')) . '")) {
                    return;
                }
                
                var button = $(this);
                var originalText = button.text();
                button.prop("disabled", true).text("' . esc_js(__('Przetwarzanie...', 'losowe-obrazy-wyrozniajace')) . '");
                
                $.post(ajaxurl, {
                    action: "lows_apply_random_images",
                    nonce: nonce
                }, function(response) {
                    button.prop("disabled", false).text(originalText);
                    
                    if (response.success) {
                        alert("' . esc_js(__('Pomyślnie dodano obrazy wyróżniające do', 'losowe-obrazy-wyrozniajace')) . ' " + response.data.updated + " ' . esc_js(__('postów.', 'losowe-obrazy-wyrozniajace')) . '");
                    } else {
                        alert("' . esc_js(__('Wystąpił błąd podczas przetwarzania.', 'losowe-obrazy-wyrozniajace')) . '");
                    }
                });
            });
        });
        ');
    }

    public function admin_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(__('Losowe Obrazy Wyróżniające', 'losowe-obrazy-wyrozniajace')); ?></h1>

            <div class="card" style="max-width: 800px;">
                <h2><?php echo esc_html(__('Konfiguracja', 'losowe-obrazy-wyrozniajace')); ?></h2>
                <p><?php echo esc_html(__('Wybierz obrazy, które będą losowo przypisywane jako obrazy wyróżniające do postów, które ich nie posiadają.', 'losowe-obrazy-wyrozniajace')); ?>
                </p>

                <p>
                    <button id="select-images"
                        class="button button-primary"><?php echo esc_html(__('Wybierz obrazy', 'losowe-obrazy-wyrozniajace')); ?></button>
                    <button id="save-images"
                        class="button"><?php echo esc_html(__('Zapisz obrazy', 'losowe-obrazy-wyrozniajace')); ?></button>
                </p>

                <p><strong><?php echo esc_html(__('Wybrane obrazy', 'losowe-obrazy-wyrozniajace')); ?> (<span
                            id="images-count">0</span>):</strong></p>
                <div id="selected-images"></div>

                <hr>

                <h3><?php echo esc_html(__('Akcje', 'losowe-obrazy-wyrozniajace')); ?></h3>
                <p>
                    <button id="apply-to-posts"
                        class="button button-secondary"><?php echo esc_html(__('Zastosuj do istniejących postów', 'losowe-obrazy-wyrozniajace')); ?></button>
                    <br><small><?php echo esc_html(__('Doda losowe obrazy wyróżniające do wszystkich postów, które obecnie ich nie posiadają.', 'losowe-obrazy-wyrozniajace')); ?></small>
                </p>

                <hr>

                <h3><?php echo esc_html(__('Automatyczne działanie', 'losowe-obrazy-wyrozniajace')); ?></h3>
                <p><?php echo esc_html(__('Plugin automatycznie ustawi losowy obraz wyróżniający przy publikacji nowych postów, jeśli nie został jeszcze ustawiony.', 'losowe-obrazy-wyrozniajace')); ?>
                </p>
            </div>
        </div>
        <?php
    }

    public function ajax_get_images()
    {
        // Weryfikacja nonce
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')), 'lows_nonce')) {
            wp_send_json_error(__('Nieprawidłowy token bezpieczeństwa', 'losowe-obrazy-wyrozniajace'));
        }

        $images = wp_cache_get($this->option_name, 'losowe_obrazy');

        if (false === $images) {
            $images = get_option($this->option_name, array());
            wp_cache_set($this->option_name, $images, 'losowe_obrazy', 12 * HOUR_IN_SECONDS);
        }

        wp_send_json_success($images);
    }

    public function ajax_save_images()
    {
        // Weryfikacja uprawnień
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Brak uprawnień', 'losowe-obrazy-wyrozniajace'));
        }

        // Weryfikacja nonce
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')), 'lows_nonce')) {
            wp_send_json_error(__('Nieprawidłowy token bezpieczeństwa', 'losowe-obrazy-wyrozniajace'));
        }

        $images = sanitize_text_field(isset($_POST['images'])) ? sanitize_text_field(wp_unslash($_POST['images'])) : array();

        // Sanityzacja danych
        $sanitized_images = array();
        if (is_array($images)) {
            foreach ($images as $image) {
                if (is_array($image)) {
                    $sanitized_images[] = array(
                        'id' => absint($image['id'] ?? 0),
                        'url' => esc_url_raw($image['url'] ?? ''),
                        'title' => sanitize_text_field($image['title'] ?? '')
                    );
                }
            }
        }

        update_option($this->option_name, $sanitized_images);
        wp_cache_delete($this->option_name, 'losowe_obrazy');
        wp_send_json_success();
    }

    public function ajax_apply_random_images()
    {
        // Weryfikacja uprawnień
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Brak uprawnień', 'losowe-obrazy-wyrozniajace'));
        }

        // Weryfikacja nonce
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')), 'lows_nonce')) {
            wp_send_json_error(__('Nieprawidłowy token bezpieczeństwa', 'losowe-obrazy-wyrozniajace'));
        }

        $images = get_option($this->option_name, array());

        if (empty($images)) {
            wp_send_json_error(__('Brak wybranych obrazów', 'losowe-obrazy-wyrozniajace'));
        }

        // Użyj bardziej wydajnego zapytania
        global $wpdb;
        $posts_without_thumbnails = $wpdb->get_results(
            "SELECT p.ID FROM {$wpdb->posts} p 
             LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id')
             WHERE p.post_type = 'post' 
             AND p.post_status = 'publish' 
             AND pm.meta_id IS NULL"
        );

        $updated = 0;
        foreach ($posts_without_thumbnails as $post) {
            $random_image = $images[array_rand($images)];
            if (set_post_thumbnail($post->ID, absint($random_image['id']))) {
                $updated++;
            }
        }

        wp_send_json_success(array('updated' => $updated));
    }

    public function auto_set_featured_image($new_status, $old_status, $post)
    {
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
new LosoweObrazyStyling();

// Hook aktywacji - można dodać domyślne ustawienia
register_activation_hook(__FILE__, function () {
    // Można tu dodać kod wykonywany przy aktywacji
});

// Hook deaktywacji
register_deactivation_hook(__FILE__, function () {
    // Można tu dodać kod wykonywany przy deaktywacji
});
?>