<?php
add_filter('plugin_action_links', 'sbm_action_links', 10, 2);
add_filter('plugin_row_meta', 'donate_link', 10, 2);

function sbm_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == 'simple-business-manager/simple-business-manager.php') {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=sbm_settings">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function donate_link($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == 'simple-business-manager/simple-business-manager.php') {

        $donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LVN2Z9HGWTBYA" target="_blank">Donate</a>';
        $links[] = $donate_link;
    }
    return $links;
}

?>