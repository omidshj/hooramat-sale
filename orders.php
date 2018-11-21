<?php
add_action('admin_menu', 'hooramat_sale_orders_menu');
function hooramat_sale_orders_menu() {
  add_submenu_page( 'options-general.php', 'حراج های فروش رفته', 'حراج های فروش رفته', 'edit_posts', 'hooramat_sale_orders', 'hooramat_sale_orders' );
}

function hooramat_sale_orders(){
  ?>
  salaaaaaaaaaaaaaaaaaaam
  <?php
}
?>
