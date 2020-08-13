<?php
// require_once(__DIR__ .'/MellatBank.php');

function hooramat_sale_shortcode( $atts ) {
    if (empty($atts['sale'])) return 'bad request';
    if(!empty($_GET['payed_order']) && !empty($_GET['payment_method'])){
        return hooramat_payment_check();
    //   }else if (!empty($_GET['Authority']) && !empty($_GET['Status']) ) {
    //     return hooramat_sale_payment_success();
    //   } else if (!empty($_GET['payir'])) {
    //     payir_verify();
    //   } else if (!empty ($_GET['(ResCode']) && !empty ($_GET['(RefId'])) {
        //mellat_verify();
    } else if (!empty($_POST['services']) &&
        !empty($_POST['first_name']) &&
        !empty($_POST['last_name']) &&
        !empty($_POST['mobile']) &&
        !empty($_POST['area']) ) {
        return hooramat_sale_preview($atts);
    } else {
        return hooramat_sale_show_table($atts);
    }
}
add_shortcode('hooramat_sale', 'hooramat_sale_shortcode');

function hooramat_sale_show_table($atts){
    global $wpdb;
    $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$atts['sale']}", ARRAY_A );
    $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$atts['sale']}", OBJECT );
    usort($services, function($a, $b){return $a->sort > $b->sort;});
    if (empty($group) || empty($services)) return 'bad request';
    date_default_timezone_set("Asia/Tehran");
    $now = date(time());
    $start = strtotime($group['start']);
    $finish = strtotime($group['finish']);
    $time = $start < $now && $now < $finish;
    ?>
    <div class="container medium">
        <form class="white padding radius margin-top-double margin-bottom-double" method="post" style="box-shadow: 0 2px 10px 0 rgba(0, 0,0, 0.6) !important;border-radius: 20px !important;">
            <?php if (!$time):
                $diff = $start - $now;
                $hour = floor($diff / (60*60));
                $minute = floor(($diff % (60*60)) / (60));
                $second = floor(($diff % 60));
            ?>
                <div class="remaining-time "  style="text-align: center;">
                    <p class="headline margin-0" style="text-align: center;">
                        <span class="second"><?= $second;?></span><span > : </span>
                        <span class="minute"><?= $minute;?></span><span > : </span>
                        <span class="hour"><?= $hour ?></span>
                    </p>
                    <p class="title margin-0 " style="text-align: center;">مانده تا آغاز حراج</p>
                </div>
            <?php endif; ?>
            <style>
                th, td {padding: 3px 8px !important; }
                body.rtl [type="radio"]:not(:checked) + label, body.rtl [type="radio"]:checked + label, body.rtl [type="checkbox"] + label{ padding-right: 0 !important; }
            </style>
            <table class="bordered">
                <thead>
                    <tr>
                        <?php if ($time): ?><th style="width: 40px;"></th><?php endif; ?>
                        <th>عنوان</th>
                        <th style="width: 80px;">قیمت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <?php if ($time): ?>
                                <td>
                                    <?php if ($service->total > 0): ?>
                                        <input class="sale-price" sale="<?= $service->sale ?>" type="checkbox" id="service<?= $service->id ?>" name="services[<?= $service->id ?>][count]" value=1 <?= !empty($_POST['services'][$service->id]['count'])? 'checked': '' ?>  />
                                        <label for="service<?= $service->id ?>"></label>
                                    <?php else: ?>
                                        تمام
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="title "><?= $service->name ?></div> <?= $service->description ?>
                            </td>
                            <td style="">
                                <div style="text-decoration: line-through;"><?= number_format($service->price) ?></div>
                                <span class="title" style="font-weight: 600; color: #e22020" ><?= number_format($service->sale) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($time): ?>
                        <tr>
                            <th></th>
                            <th>جمع</th>
                            <th class="services-cost">0</th>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <br><br>
            <?php if ($time): ?>
                <br>
                <div class="row">
                    <div class="input-fieldx col m6 s12 right-align">
                        <label class="title ">نام مادر:</label>
                        <input name="first_name" type="text" class="validate" value="<?= $_POST['first_name'] ?? '' ?>">
                    </div>
                    <div class="input-fieldx col m6 s12 right-align">
                        <label class="title ">نام خانوادگی مادر:</label>
                        <input name="last_name" type="text" class="validate" value="<?= $_POST['last_name'] ?? '' ?>">
                    </div>
                    <div class="col s12"><br></div>
                    <div class="input-fieldx col m6 s12 right-align">
                        <label class="title ">شماره تلفن:</label>
                        <input name="mobile" type="text" class="validate" value="<?= $_POST['mobile'] ?? '' ?>">
                    </div>
                    <div class="input-fieldx col m6 s12 right-align">
                        <label class="title ">محدوده محل سکونت:</label>
                        <input name="area" type="text" class="validate" value="<?= $_POST['area'] ?? '' ?>">
                    </div>
                    <div class="input-fieldx col m6 s12 right-align">
                        <label class="title ">کد تخفیف:</label>
                        <input name="coupon" type="text" class="validate" value="<?= $_POST['coupon'] ?? '' ?>">
                    </div>
                </div>
                <input type="submit" name="" value="ثبت درخواست">
                <br><br><br><br><br><br><br>
            <?php endif; ?>
        </form>
	</div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            servicesCost();
            jQuery('.sale-price').change(servicesCost);
            function servicesCost(){
                var c = 0;
                jQuery('.sale-price').each(function(){
                    if ( jQuery(this).is(":checked") ) c += parseInt( jQuery(this).attr('sale') );
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
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$atts['sale']} and total > 0 and id IN ({$ids})", OBJECT );
  $cost = 0;
  if (empty($group) || empty($services)) return 'bad request';
  if(!empty($_POST['coupon'])){
    $cp = strtolower($_POST['coupon']);
    $coupon = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_coupons where group_id={$atts['sale']} and code = '{$cp}'", OBJECT );
  }
  ?>
  <form class="container white padding-double radius-20 shadow" method="post">
    <input type="hidden" name="group" value="<?= $atts['sale'] ?>">
    <style>
        th, td {padding: 3px 8px !important; }
    </style>
    <table class="bordered">
      <thead>
        <tr>
          <th>عنوان</th>
          <th>قیمت</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): $cost += $service->sale; ?>
          <tr>
            <td>
              <div class="title "><?= $service->name ?></div> <?= $service->description ?>
              <input type="hidden" name="services[<?= $service->id ?>][count]" value="<?= $_POST['services'][$service->id]['count'] ?>">
            </td>
            <td><?= $service->sale ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if(!empty($coupon)):
            if($coupon[0]->percent)
                $discount = $cost / 100 * $coupon[0]->percent;
            else if($coupon[0]->discount)
                $discount = $coupon[0]->discount;
            else
                $discount = 0;
            $cost -= $discount;
        ?>
          <tr>
            <td>
              <div class="title">کد تخفیف: <?= $_POST['coupon'] ?></div>
              <input type="hidden" name="coupon" value="<?= $_POST['coupon'] ?>">
            </td>
            <td>- <?= $discount ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <th>جمع</th>
          <th><?= $cost ?></th>
        </tr>
      </tbody>
    </table>
    <br>
    <div class="row">
      <p class="col m6 s12 title ">
        نام مادر: <?= $_POST['first_name'] ?> <input type="hidden" name="first_name" value="<?= $_POST['first_name'] ?>">
      </p>
      <p class="col m6 s12 title ">
        نام خانوادگی مادر: <?= $_POST['last_name'] ?> <input type="hidden" name="last_name" value="<?= $_POST['last_name'] ?>">
      </p>
      <p class="col m6 s12 title ">
        تلفن: <?= $_POST['mobile'] ?> <input type="hidden" name="mobile" value="<?= $_POST['mobile'] ?>">
      </p>
      <p class="col m6 s12 title ">
        محدوده محل سکونت: <?= $_POST['area'] ?> <input type="hidden" name="area" value="<?= $_POST['area'] ?>">
      </p>
      <?php if(!empty($coupon)): ?>
        <p class="col m6 s12 title ">
          کد تخفیف: <?= $_POST['coupon'] ?> <input type="hidden" name="coupon" value="<?= $_POST['coupon'] ?>">
        </p>
      <?php endif; ?>
      <div class="input-fieldx col m6 s12 right-align">
          <label class="title ">روش پرداخت:</label>
          <div>
            <label class="body-2"><input class="with-gap" name="payment_method" type="radio" value="mellat" checked /><span>بانک ملت</span></label>
            <label class="body-2"><input class="with-gap" name="payment_method" type="radio" value="zarinpal"/><span>زرین پال</span></label>
          </div>
      </div>
    </div>
    <br>
    <input type="submit" name="" value="پرداخت آنلاین">
    <br><br><br><br><br>
  </form>
  <?php
}

add_action('wp_loaded', function(){
  if(!empty($_POST['group']) &&
    !empty($_POST['services']) &&
    !empty($_POST['first_name']) &&
    !empty($_POST['last_name']) &&
    !empty($_POST['mobile']) &&
    !empty($_POST['area']) &&
    !empty($_POST['payment_method'])
  ){
    $order  = hooramat_order_save();
    
    switch ($_POST['payment_method']) {
      case 'mellat':
        hooramat_mellat_redirect($order);
        break;
      case 'zarinpal':
        hooramat_zarinpal_redirect($order);
        break;
      case 'payir':

        $api = 'c390c2d4b2c777318eaca5866dfc748c';
        $amount = 10 * $cost;
        $mobile = $_POST['mobile'];
        $factorNumber = $wpdb->insert_id;
        $description = "سفارش {$_POST['first_name']} {$_POST['last_name']} در {$group['name']} به مبلغ {$cost} تومان";
        $redirect = home_url( $wp->request ) . $_SERVER['REQUEST_URI'] . '?peyed_order=' . $wpdb->insert_id . '&payir=1';
        // print_r($redirect);
        // die;
        $result = payir_send($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
        if($result->status) {
          $go = "https://pay.ir/pg/$result->token";
          header("Location: $go");
        } else {
          echo $result->errorMessage;
        }

        break;
    }
  }
});

function hooramat_order_save(){
    
    $requested_services = [];
    global $wpdb;
    $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$_POST['group']}", ARRAY_A );
    $ids = implode(array_keys($_POST['services']), ', ');
    $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where group_id={$_POST['group']} and id IN ({$ids})", OBJECT );
    $cost = 0;

    if(!empty($_POST['coupon'])){
      $cp = strtolower($_POST['coupon']);
      $coupon = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_coupons where group_id={$_POST['group']} and code = '{$cp}'", OBJECT );
    }
    foreach ($services as $service) {
      // $requested_services[$service->id]['sale'] = $service->sale;
      // $requested_services[$service->id]['cost'] = $requested_services[$service->id]['count'] * $service->sale;
      $requested_services[$service->id] = [
        'sale' => $service->sale,
        'count' => $_POST['services'][$service->id]['count'],
        'name' => $service->name,
        'cost' => $_POST[$service->id]['count'] * $service->sale
      ];
      $cost += intval($_POST['services'][$service->id]['count']) * $service->sale;
    }
    
    if (!empty($coupon)){
      if($coupon[0]->percent)
        $discount = $cost / 100 * $coupon[0]->percent;
      else if($coupon[0]->discount)
        $discount = $coupon[0]->discount;
      else
        $discount = 0;
      $cost -= $discount;
      $requested_services[] = [
        'count' => 1,
        'sale' => 0-$cost/100*5,
        'name' => 'کوپن تخفیف: ' . $_POST['coupon'],
        'cost' => -$discount
      ];
      
    }
    
    $order = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'mobile' => $_POST['mobile'],
        'area' => $_POST['area'],
        'services' => serialize( $requested_services ),
        'cost' => $cost,
    ];
    $wpdb->insert( $wpdb->prefix . 'hooramat_sale_orders', $order );
    $order['id'] = $wpdb->insert_id;
    $order['code'] = 2*7*14*22 * $order['id'];
    $wpdb->update(
      $wpdb->prefix . 'hooramat_sale_orders',
      ['code' => $order['code']],
      ['id' => $order['id']]
    );
    return (object) $order;
}

function hooramat_payment_check(){
    global $wpdb;
    $order = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders where id = {$_GET['payed_order']}");
    switch ($_GET['payment_method']) {
      case 'mellat':
        $verify = hooramat_mellat_verify($order);
        break;
    }
    // print_r($_POST);
    // echo '<br>';
    // print_r($verify);
    if($verify == ''){
        ?>
            <br><br><br>
            <p class="title ">پرداخت با مشکل مواجه شد.</p>
            <br><br><br>
        <?php
    }else{
        $wpdb->update(
          $wpdb->prefix . 'hooramat_sale_orders',
          [
            'payment_time' => date('Y-m-d H:i:s'),
            'payment_code' => $verify
          ],
          ['id' => $order->id]
        );
        $ids = implode( array_keys( unserialize($order->services) ), ', ' );
        $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id IN ({$ids})", OBJECT );
        foreach ($services as $service) {
          $wpdb->update($wpdb->prefix.'hooramat_sale_services', ['total' => $service->total-1], ['id' => $service->id]);
        }
        sendSms($order->code, $order->mobile);
        ?>
            <br><br><br>
            <p class="title ">خرید شما موفق بود. کد پیگیری خود را به خاطر بسپارید و برای رزرو وقت با کلینیک تماس بگیرید.</p>
            <h2 class="display-2">کد پیگیری: <?= $order->code ?></h2>
            <br><br><br>
        <?php
        
    }
}



function hooramat_mellat_redirect($order){
   
    if(!extension_loaded('soap')) {echo "soap extention not available"; die();}
    $client = new SoapClient(
        'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
        [
            'uri'   =>  'http://interfaces.core.sw.bps.com/',
			'trace'      => true,
			'exceptions' => true
		]
    );
     
    $result = $client->bpPayRequest([
        'terminalId' => 4798462,
    	'userName' => 'pkh8176',
    	'userPassword' => '43195682',
    	'orderId' => $order->code,
    	'amount' => 10 * $order->cost,
    	'localDate' => date('ymj'),
    	'localTime' => date('His'),
    	'additionalData' => '',
    	'callBackUrl' => home_url( $wp->request ) . $_SERVER['REQUEST_URI'] . '?payed_order=' . $order->id . '&payment_method=' . $_POST['payment_method'],
        'payerId' => 0
    ]);
    
    
    $res = explode (',', $result->return);
	$code = isset($res[0]) ? $res[0] : NULL;
	$refid = isset($res[1]) ? $res[1] : NULL;
	if($code !== '0') {
		throw new Exception('Mellat error requesting bpPayRequest: '.$code);
	}
	echo '<form name="myform" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
    				    <input type="hidden" id="RefId" name="RefId" value="'. $res[1] .'">
    				  </form><script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>';
    				die();
}

function hooramat_mellat_verify($order){
    if(!extension_loaded('soap')) {echo "soap extention not available"; die();}
    $verify = '';
    $client = new SoapClient(
        'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
        [
            'uri'   =>  'http://interfaces.core.sw.bps.com/',
			'trace'      => true,
			'exceptions' => true
		]
    );
    $params = [
		'terminalId' => 4798462,
		'userName' => 'pkh8176',
		'userPassword' => '43195682',
		'orderId' => $order->code,
		'refid' => $_POST['RefId'],
		'saleOrderId' => $_POST['SaleOrderId'],
		'saleReferenceId' => $_POST['SaleReferenceId']
    ];
    $result = $client->bpVerifyRequest($params);
    if($result->return == 0){
        $verify = $_POST['RefId'];
        $settle = $client->bpSettleRequest($params);
    }
    // print_r($result);
    // echo '<br>';
    // print_r($settle);
    // echo '<br>';
    return $verify;
}



function hooramat_zarinpal_redirect($order){
    $jsonData = json_encode(array(
      'MerchantID' => '68f32bf2-ee3e-11e8-a3bb-005056a205be',
      'Amount' => $order->cost,
      'CallbackURL' => home_url( $wp->request ) . $_SERVER['REQUEST_URI'] . '?payed_order=' . $order->id . '&payment_method=' . $_POST['payment_method'],
      'Description'  => "سفارش {$order->first_name} {$order->last_name} به مبلغ {$order->cost} تومان"
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
      die();
    } else {
      if ($result["Status"] == 100) {
        header('Location: https://www.zarinpal.com/pg/StartPay/' . $result["Authority"]);
      } else {
        echo'ERR: ' . $result["Status"];
        die();
      }
    }
}

function hooramat_zarinpal_verify($order){
    $verify = '';
  global $wpdb;
//   $order = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders where id = {$_GET['order']}", ARRAY_A );
  $jsonData = json_encode(array(
    'MerchantID' => '68f32bf2-ee3e-11e8-a3bb-005056a205be',
    'Authority' => $_GET['Authority'],
    'Amount' => $order->cost
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
    if ($result['Status'] == 100 || $result['Status'] == 101) {
        $verify = $result['RefID'];
    //   if (!$order['payment_code']) {
        // $wpdb->update(
        //   $wpdb->prefix . 'hooramat_sale_orders',
        //   array(
        //     'payment_time' => date('Y-m-d H:i:s'),
        //     'payment_code' => $result['RefID']
        //   ),
        //   array ('id' => $_GET['order'])
        // );
        // $ids = implode( array_keys( unserialize($order['services']) ), ', ' );
        // $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id IN ({$ids})", OBJECT );
        // foreach ($services as $service) {
        //   $wpdb->update(
        //     $wpdb->prefix . 'hooramat_sale_services',
        //     array('total' => $service->total - 1),
        //     array ('id' => $service->id )
        //   );
        // }
        // sendSms($order['code'], $order['mobile']);
    //   }

      // echo 'Transation successssss. RefID:' . $result['RefID'];
    } else {
      echo 'Transation failed. Status:' . $result['Status'];
    }
  }
  ?>

  <?php
  return $verify;
}



function getSmsIrToken(){
	$postData = array(
		'UserApiKey' => 'a85c145c6fe92edbe77fff5d',
		'SecretKey' => 'ref@hFuckingP@33',
		'System' => 'php_rest_v_1_1'
	);
	$postString = json_encode($postData);
	$ch = curl_init("http://RestfulSms.com/api/Token");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POST, count($postString));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
	$result = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($result);
	if(is_object($response)){
		$resultVars = get_object_vars($response);
		if(is_array($resultVars)){
			@$IsSuccessful = $resultVars['IsSuccessful'];
			if($IsSuccessful == true){
				@$TokenKey = $resultVars['TokenKey'];
				$resp = $TokenKey;
			} else {
				$resp = false;
			}
		}
	}
	return $resp;
}
function sendSms($Code, $MobileNumber){
	$token = getSmsIrToken();
	if($token != false){
		$postData = array(
			'Code' => $Code,
			'MobileNumber' => $MobileNumber,
		);
		$url = "http://RestfulSms.com/api/VerificationCode";
		$postString = json_encode($postData);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'x-sms-ir-secure-token: '.$token
		));
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, count($postString));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		$VerificationCode = curl_exec($ch);
		curl_close($ch);
		// return $result;
		// $VerificationCode = $this->execute($postData, $url, $token);
		$object = json_decode($VerificationCode);
		if(is_object($object)){
			$array = get_object_vars($object);
			if(is_array($array)){
				$result = $array['Message'];
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}
	} else {
		$result = false;
	}
	return $result;
}





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
    if ($result['Status'] == 100 || $result['Status'] == 101) {
      if (!$order['payment_code']) {
        $wpdb->update(
          $wpdb->prefix . 'hooramat_sale_orders',
          array(
            'payment_time' => date('Y-m-d H:i:s'),
            'payment_code' => $result['RefID']
          ),
          array ('id' => $_GET['order'])
        );
        $ids = implode( array_keys( unserialize($order['services']) ), ', ' );
        $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id IN ({$ids})", OBJECT );
        foreach ($services as $service) {
          $wpdb->update(
            $wpdb->prefix . 'hooramat_sale_services',
            array('total' => $service->total - 1),
            array ('id' => $service->id )
          );
        }
        sendSms($order['code'], $order['mobile']);
      }
      ?>
        <br><br><br>
        <div class="container medium white padding center radius shadow">
          <p class="title ">خرید شما موفق بود. کد پیگیری خود را به خاطر بسپارید و برای رزرو وقت با کلینیک تماس بگیرید.</p>
          <h2 class="display-2">کد پیگیری: <?= $order['code'] ?></h2>
        </div>
        <br><br><br>
      <?php
      // echo 'Transation successssss. RefID:' . $result['RefID'];
    } else {
      echo 'Transation failed. Status:' . $result['Status'];
    }
  }
  ?>

  <?php
}

function payir_send($api, $amount, $redirect, $mobile = null, $factorNumber = null, $description = null) {
	return payir_curl_post('https://pay.ir/pg/send', [
		'api'          => $api,
		'amount'       => $amount,
		'redirect'     => $redirect,
		'mobile'       => $mobile,
		'factorNumber' => $factorNumber,
		'description'  => $description,
	]);
}
function payir_verify() {
	$result = payir_curl_post('https://pay.ir/pg/verify', [
		'api' 	=> 'c390c2d4b2c777318eaca5866dfc748c',
		'token' => $_GET['token'],
  ]);
  $result = json_decode($result);
  if($result->status){
    if ($result->status == 1) {
      global $wpdb;
      $order = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_orders where id = {$result->factorNumber}", ARRAY_A );
      if (!$order['payment_code']) {
        $wpdb->update(
          $wpdb->prefix . 'hooramat_sale_orders',
          array(
            'payment_time' => date('Y-m-d H:i:s'),
            'payment_code' => $result->transId
          ),
          array ('id' => $result->factorNumber)
        );
        $ids = implode( array_keys( unserialize($order['services']) ), ', ' );
        $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id IN ({$ids})", OBJECT );
        foreach ($services as $service) {
          $wpdb->update(
            $wpdb->prefix . 'hooramat_sale_services',
            array('total' => $service->total - 1),
            array ('id' => $service->id )
          );
        }
        sendSms($order['code'], $order['mobile']);
      }
      ?>
        <br><br><br>
        <div class="container medium white padding center radius shadow">
          <p class="title ">خرید شما موفق بود. کد پیگیری خود را به خاطر بسپارید و برای رزرو وقت با کلینیک تماس بگیرید.</p>
          <h2 class="display-2">کد پیگیری: <?= $order['code'] ?></h2>
        </div>
        <br><br><br>
      <?php
      // echo 'Transation successssss. RefID:' . $result['RefID'];
    } else {
      echo 'Transation failed. Status:' . $result['Status'];
    }
  } else {
    echo $result->errorCode . ' - ' . $result->errorMessage;
  }
}
function payir_curl_post($url, $params){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
	]);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}
?>
