<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/*
Plugin Name: WooCommerce Hutkigrosh Gateway Payments
Plugin URI: https://github.com/esasby/hgrosh/tree/master/CMS/Plugins/WordPress
Description: Модуль для выставления счетов в систему ЕРИП черех сервис ХуткiГрош
Version: 1.0.0
Author: ESAS
Author Email: nikita.mekh@gmail.com
Text Domain: woocommerce-hutkigrosh-payments
*/

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'wc_hutkigrosh_gateway_init', 0);
function wc_hutkigrosh_gateway_init()
{
    // If the parent WC_Payment_Gateway class doesn't exist
    // it means WooCommerce is not installed on the site
    // so do nothing
    if (!class_exists('WC_Payment_Gateway')) return;
    // If we made it this far, then include our Gateway Class
    include_once('hg.php');
    //Подключение модели для работы с API
    include_once('hutkigrosh_api.php');
    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter('woocommerce_payment_gateways', 'hutkigrosh_add_payment_gateway' );
//    register_post_status('wc-shipped', array(
//        'label' => "Сгенерировать платежное поручение в системе ЕРИП",
//        'public' => true,
//        'exclude_from_search' => false,
//        'show_in_admin_all_list' => true,
//        'show_in_admin_status_list' => true,
//        'label_count' => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>')
//    ));

    function hutkigrosh_add_payment_gateway($methods)
    {
        $methods[] = 'WC_HUTKIGROSH_GATEWAY';
        return $methods;
    }
}

// Add custom action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'hutkigrosh_gateway_action_links');
function hutkigrosh_gateway_action_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_hutkigrosh_gateway') . '">' . __('settings', 'woocommerce-hutkigrosh-payments') . '</a>',
    );
    // Merge our new link with the default ones
    return array_merge($plugin_links, $links);
}

//Инициализация плагина и загрузка языкового пакета для плагина
add_action("init", "hutkigrosh_init");
function hutkigrosh_init()
{
    $res = load_plugin_textdomain("woocommerce-hutkigrosh-payments", false, basename(dirname(__FILE__)) . '/languages/');
}


/**
 * Custom text on the receipt page.
 */
//add_filter('woocommerce_thankyou_order_received_text', 'hutkigrosh_thankyou_text', 10, 2);
//function hutkigrosh_thankyou_text($text, $order)
//{
//    return WC_HUTKIGROSH_GATEWAY::hutkigrosh_thankyou_text($order);
//}

//add_action('wp_ajax_alfaclick', array(WC_HUTKIGROSH_GATEWAY::instance(), 'alfaclick_callback'));
//add_action('wp_ajax_nopriv_alfaclick', array(ESAS\HootkiGrosh\WC_HUTKIGROSH_GATEWAY::instance(), 'alfaclick_callback'));
add_action('wp_ajax_alfaclick', 'alfaclick_callback');
//add_action('wp_ajax_nopriv_alfaclick', 'alfaclick_callback');
//function alfaclick_callback()
//{
//    return WC_HUTKIGROSH_GATEWAY::alfaclick_callback();
//}
//add_action('wp_ajax_alfaclick', 'alfaclick_callback');
//add_action('wp_ajax_nopriv_alfaclick', 'alfaclick_callback');
//
function alfaclick_callback()
{
//    $gateway = new WC_HUTKIGROSH_GATEWAY();
    return WC_HUTKIGROSH_GATEWAY::get_instance()->alfaclick_callback();
}
//

////Add callback if Shipped action called
//add_filter('woocommerce_order_action_wdm_shipped', 'wdm_order_shipped_callback', 10, 1);
//function wdm_order_shipped_callback($order)
//{
//    $plugin = new WC_HUTKIGROSH_GATEWAY;
//    $order->update_status('status_pending', __('order_status_shipped', 'woocommerce-hutkigrosh-payments'));
//    return $plugin->add_bill($order);
//}