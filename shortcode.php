<?php
function hooramat_sale_shortcode( $atts ) {
  if (empty($atts['sale'])) return 'bad request';
  if (!empty($_GET['Authority']) && !empty($_GET['Status']) ) {
    hooramat_sale_payment_success();
  } else if (!empty($_POST['services']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['mobile']) && !empty($_POST['area']) ) {
    hooramat_sale_preview($atts);
  }else {
    hooramat_sale_show_table($atts);
  }

}
add_shortcode( 'hooramat_sale', 'hooramat_sale_shortcode' );

function hooramat_sale_show_table($atts){
  global $wpdb;
  $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$atts['sale']}", ARRAY_A );
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$atts['sale']}", OBJECT );
  if (empty($group) || empty($services)) return 'bad request';
  date_default_timezone_set("Asia/Tehran");
  $now = date(time());
  $start = strtotime($group['start']);
  $finish = strtotime($group['finish']);
  $time = $start < $now && $now < $finish;


  ?>
  <form class="" method="post">
    <?php if (!$time): ?>
      <?php
        $diff = $start - $now;
        $hour = floor($diff / (60*60));
        $minute = floor(($diff % (60*60)) / (60));
        $second = floor(($diff % 60));
      ?><br>
      <div class="remaining-time">
        <p class="display-3 margin-0" style="text-align: center;">
          <span class="second"><?= $second;?></span><span > : </span>
          <span class="minute"><?= $minute;?></span><span > : </span>
          <span class="hour"><?= $hour ?></span>
          <br>
          مانده به حراج
        </p>
      </div><br><br>
    <?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>عنوان</th>
          <th>شرح</th>
          <th>قیمت</th>
          <th>حراج</th>
          <?php if ($time): ?><th></th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): ?>
          <tr>
            <td><?= $service->name ?></td>
            <td><?= $service->description ?></td>
            <td><?= $service->price ?></td>
            <td><?= $service->sale ?></td>
            <?php if ($time): ?>
              <td>
                <input class="sale-price" sale="<?= $service->sale ?>" type="checkbox" id="service<?= $service->id ?>" name="services[<?= $service->id ?>][count]" value=1 <?= !empty($_POST['services'][$service->id]['count'])? 'checked': '' ?>  />
                <label for="service<?= $service->id ?>"></label>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if ($time): ?>
          <tr>
            <th></th>
            <th></th>
            <th>جمع</th>
            <th class="services-cost">0</th>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <br><br>
    <?php if ($time): ?>
      <div class="row">

        <div class="input-fieldx col s6">
          <label class="title">نام:</label>
          <input name="first_name" type="text" class="validate" value="<?= $_POST['first_name'] ?? '' ?>">
        </div>
        <div class="input-fieldx col s6">
          <label class="title">نام خانوادگی:</label>
          <input name="last_name" type="text" class="validate" value="<?= $_POST['last_name'] ?? '' ?>">
        </div>
        <div class="col s12">
          <br>
        </div>
        <div class="input-fieldx col s6">
          <label class="title">شماره تلفن:</label>
          <input name="mobile" type="text" class="validate" value="<?= $_POST['mobile'] ?? '' ?>">
        </div>
        <div class="input-fieldx col s6">
          <label class="title">محدوده محل سکونت:</label>
          <input name="area" type="text" class="validate" value="<?= $_POST['area'] ?? '' ?>">
        </div>
      </div>
      <input type="submit" name="" value="ثبت درخواست">
    <?php endif; ?>
  </form>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      servicesCost();
      jQuery('.sale-price').change(servicesCost);
      function servicesCost(){
        var c = 0;
        jQuery('.sale-price').each(function(){
          if ( jQuery(this).is(":checked") )
            c += parseInt( jQuery(this).attr('sale') );
        });
        jQuery('.services-cost').html(c);
      }
    });
  </script>
  <?php
}

function hooramat_sale_preview($atts){
  global $wpdb;
  $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$atts['sale']}", ARRAY_A );
  $ids = implode(array_keys($_POST['services']), ', ');
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$atts['sale']} and id IN ({$ids})", OBJECT );
  $cost = 0;
  if (empty($group) || empty($services)) return 'bad request';
  ?>
  <form class="" method="post">
    <input type="hidden" name="group" value="<?= $atts['sale'] ?>">
    <table>
      <thead>
        <tr>
          <th>عنوان</th>
          <th>قیمت</th>
          <th>حراج</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): $cost += $service->sale; ?>
          <tr>
            <td><?= $service->name ?><input type="hidden" name="services[<?= $service->id ?>][count]" value="<?= $_POST['services'][$service->id]['count'] ?>"> </td>
            <td><?= $service->price ?></td>
            <td><?= $service->sale ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <th></th>
          <th>جمع</th>
          <th><?= $cost ?></th>
        </tr>
      </tbody>
    </table>
    <br><br>
    <div class="row">
      <p class="col s6 title">
        نام: <?= $_POST['first_name'] ?> <input type="hidden" name="first_name" value="<?= $_POST['first_name'] ?>">
      </p>
      <p class="col s6 title">
        نام خانوادگی: <?= $_POST['last_name'] ?> <input type="hidden" name="last_name" value="<?= $_POST['last_name'] ?>">
      </p>
      <p class="col s6 title">
        تلفن: <?= $_POST['mobile'] ?> <input type="hidden" name="mobile" value="<?= $_POST['mobile'] ?>">
      </p>
      <p class="col s6 title">
        محدوده محل سکونت: <?= $_POST['area'] ?> <input type="hidden" name="area" value="<?= $_POST['area'] ?>">
      </p>
    </div>
    <br>
    <input type="hidden" name="payment" value="11258">
    <input type="submit" name="" value="پرداخت آنلاین">
  </form>
  <?php
}


add_action('template_redirect', function(){
  if(
    !empty($_POST['group']) &&
    !empty($_POST['services']) &&
    !empty($_POST['first_name']) &&
    !empty($_POST['last_name']) &&
    !empty($_POST['mobile']) &&
    !empty($_POST['area']) &&
    !empty($_POST['payment'])
  ){
    $requested_services = $_POST['services'];
    global $wpdb;
    $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$_POST['group']}", ARRAY_A );
    $ids = implode(array_keys($_POST['services']), ', ');
    $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$_POST['group']} and id IN ({$ids})", OBJECT );
    $cost = 0;

    foreach ($services as $service) {
      $requested_services[$service->id]['sale'] = $service->sale;
      $requested_services[$service->id]['cost'] = $requested_services[$service->id]['count'] * $service->sale;
      $cost += $requested_services[$service->id]['cost'];
    }
    // echo $cost;
    // echo "سفارش {$_POST['first_name']} {$_POST['last_name']} در {$group['name']} به مبلغ {$cost} تومان";
    // print_r($_POST);
    // exit;

    global $wpdb;
    $order = $wpdb->insert(
      $wpdb->prefix . 'hooramat_sale_orders',
      array(
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'mobile' => $_POST['mobile'],
        'area' => $_POST['area'],
        'services' => serialize( $requested_services ),
        'cost' => $cost
      )
    );

    $jsonData = json_encode(array(
      'MerchantID' => '68f32bf2-ee3e-11e8-a3bb-005056a205be',
      'Amount' => $cost,
      'CallbackURL' => get_permalink() . '?order=' . $wpdb->insert_id,
      'Description'  => "سفارش {$_POST['first_name']} {$_POST['last_name']} در {$group['name']} به مبلغ {$cost} تومان"
    ));

    $ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate/PaymentRequest.json');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData)
    ));
    $result = curl_exec($ch);
    $err = curl_error($ch);
    $result = json_decode($result, true);
    curl_close($ch);
    if ($err) {
      echo "cURL Error #:" . $err;
      } else {
      if ($result["Status"] == 100) {
        header('Location: https://www.zarinpal.com/pg/StartPay/' . $result["Authority"]);
      } else {
        echo'ERR: ' . $result["Status"];
      }
    }
  }
});

function hooramat_sale_payment_success(){
  global $wpdb;
  $order = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders where id = {$_GET['order']}", ARRAY_A );
  $jsonData = json_encode(array(
    'MerchantID' => '68f32bf2-ee3e-11e8-a3bb-005056a205be',
    'Authority' => $_GET['Authority'],
    'Amount' => $order['cost']
  ));
  $ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate/PaymentVerification.json');
  curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonData) ));
  $result = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);
  $result = json_decode($result, true);
  if ($err) {
    echo "cURL Error #:" . $err;
  } else {

    if ($result['Status'] == 100) {
      $wpdb->update(
        $wpdb->prefix . 'hooramat_sale_orders',
        array(
          'payment_time' => date('Y-m-d H:i:s'),
          'payment_code' => $result['RefID']
        ),
        array ('id' => $_GET['order'])
      );
      $ida = implode( array_keys( unserialize($order['services']) ), ', ' );
      $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id IN ({$ids})", OBJECT );
      foreach ($services as $service) {
        $wpdb->update(
          $wpdb->prefix . 'hooramat_sale_services',
          array('total' => $service->total - 1),
          array ('id' => $service->id)
        );
      }
      echo 'Transation success. RefID:' . $result['RefID'];
    } else {
      echo 'Transation failed. Status:' . $result['Status'];
    }
  }
  ?>
    پرواخت شما با موفقیت انجام شد
  <?php
}
?>
