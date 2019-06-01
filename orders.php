<?php
add_action('admin_menu', 'hooramat_sale_orders_menu');
function hooramat_sale_orders_menu() {
  add_submenu_page( 'options-general.php', 'حراج های فروش رفته', 'حراج های فروش رفته', 'edit_posts', 'hooramat_sale_orders', 'hooramat_sale_orders' );
}

function hooramat_sale_orders(){
  global $wpdb;
  // $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services", OBJECT_K );
  $orders_per_page = 200;
  $paged = isset($_GET['paged'])? $_GET['paged']: 1;
  $start = ($paged - 1) * $groups_per_page;
  $orders = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders", OBJECT );
  $total_orders = ceil( $wpdb->num_rows / $orders_per_page);
  $orders = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders ORDER BY id DESC", OBJECT );
  $sum = 0; $total = 0;
  ?>
  <div class="wrap">
    <h1 class="wp-heading-inline">حراج ها</h1>
    <div class="tablenav top">
      <div class="tablenav-pages">
        <?php
        if( $total_orders > 1 )  {
          $format = get_option('permalink_structure')? 'page/%#%/': '&paged=%#%';
          echo paginate_links(array(
            'base'          => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'        => $format,
            'current'       => $paged,
            'total'         => $total_orders,
            'mid_size'      => 2,
            'prev_text'     => is_rtl()? '&rarr;': '&larr;',
            'next_text'     => is_rtl()? '&larr;': '&rarr;',
          ));
        }
        ?>
      </div>
    </div>
    <table class="wp-list-table widefat fixed striped ">
      <tr>
        <th>شناسه</th>
        <th>نام</th>
        <th>تلفن</th>
        <th>هزینه</th>
        <th>کد و زمان پرداخت</th>
        <th></th>
      </tr>
      <?php foreach ($orders as $order): $total += $order->cost; $sum += $order->payment_time? $order->cost: 0; ?>
        <tr>
          <td><?= $order->id . ' ' . $order->code ?></td>
          <td><?= $order->first_name . ' ' . $order->last_name ?></td>
          <td><?= $order->mobile ?></td>
          <td><?= $order->cost ?></td>
          <td><?= $order->payment_code ?><br><?= $order->payment_time ?></td>
          <td>
            <?php foreach (unserialize($order->services) as $key => $service): ?>
              <?= ($service['name'] ?? 'تعداد') . ': ' . $service['count'] . ' - هزینه: ' . $service['sale'] ?>
              <br>
            <?php endforeach; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?= $sum ?><br><?= $total ?>
  </div>
  <?php
}
?>