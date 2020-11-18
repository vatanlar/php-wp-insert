<?php 
define('WP_POST_REVISIONS', 0);
require "wp-load.php";

date_default_timezone_set('Europe/Istanbul');


function wp_post($post_title, $post_content, $permalink, $ping ){

    global $wpdb;

    $new_post = array(
        'post_title' => $post_title,
        'post_content' => $post_content,
        'post_status' => 'publish',
        'post_date' => date('Y-m-d H:i:s') ,
        'post_author' => 1,
        'post_type' => 'page',
        'post_category' => array(0),
        'post_name' => $permalink
    );
    
    $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . '
        WHERE post_title = %s', $post_title);
    
    $wpdb->query($query);
    
    if ($wpdb->num_rows)
    {
    
        $post_id = $wpdb->get_var($query);
        $meta = get_post_meta($post_id, 'times', true);
        $meta++;
        update_post_meta($post_id, 'times', $meta);
    
        $new_post["ID"] = $post_id;
        wp_update_post($new_post);
    
    }
    else
    {
    
        $post_id = wp_insert_post($new_post);
        add_post_meta($post_id, 'times', '1');
    
    }
    
    /* UPLOAD IMAGE */
    if (@file_get_contents($file))
    {
        $filename = basename($file);
    
        $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
        if (!$upload_file['error'])
        {
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_parent' => $parent_post_id,
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename) ,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_type' => 'page'
            );
            $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $parent_post_id);
            if (!is_wp_error($attachment_id))
            {
                require_once (ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
            }
        }
    
        set_post_thumbnail($post_id, $attachment_id);
    
    }
    /* UPLOAD IMAGE */

    if($ping==true){
        file_get_contents('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . get_site_url() . '/sitemap.xml');
    }

}


// $file = 'thumbnail_image.png';
$post_title = "Hava Durumu";
$post_content = $post_content;
$permalink = "hava-durumu";


wp_post($post_title, $post_content, $permalink, false);


