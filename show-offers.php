<?php
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
        <form  method="post" >
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
            <?php foreach ($services as $service): ?>
                <div class="service-card white padding radius margin-top-double margin-bottom-double" style="box-shadow: 0 2px 10px 0 rgba(0, 0,0, 0.6) !important;border-radius: 20px !important;">
						
						<div style="display: flex; flex-direction: column; background-color: #fff; border-radius: 30px; padding: 10px 10px;">
							<div style="display: flex; align-items: center;">
								<img src="<?= wp_get_attachment_image_src( $service->media)[0] ?>" alt="" style=" width: 60px; border-radius: 50%; border: 3px solid #fadada; margin-left: 10px;">
								<div style="font-size: 1.2rem; font-weight: 900;"><?= $service->name ?></div>
							</div>
							<div style="height: 2px; background-color: rgb(211, 211, 211); margin: 10px 0;"></div>
							<div style="display: flex; align-items: center;">
								<div style="width: 40%; margin-left:7px; text-align: center; font-size: 1rem; text-decoration-line: line-through; "><?= $service->price ?>ریال<br/></div>
								<div style="height: 40px; width: 2px; background-color: #fadada;"></div>
								<div style="width: 60%; background-color: #fadada; margin-right: 7px; text-align: center; font-size: 1.3rem; padding: 5px 0; border-radius: 15px; font-weight: 900;"><?= $service->sale ?> ریال</div>
							</div>
							
						<input class="sale-price" sale="<?= $service->sale ?>" type="checkbox" id="service<?= $service->id ?>" name="services[<?= $service->id ?>][count]" value=1 <?= !empty($_POST['services'][$service->id]['count'])? 'checked': '' ?> style="display: none" />
						<label style="display: none" for="service<?= $service->id ?>"></label>
						</div>
						
						
						
                </div>
            <?php endforeach; ?>
			<div id="offer2" class="modal bottom-sheet padding-0"  >
				<div class="modal-content" style="height:90vh;">
				  <h4>Modal Header</h4>
				  <p>A bunch of text</p>
				</div>
				<div class="modal-footer">
				  <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
				</div>
			</div>
			<div id="sale-actions">
				<span class="services-cost title"></span>
				<span class="offer1 title">offer1</span>
				<span class="offer2 title modal-trigger" href="#offer2">offer2</span>

			</div>
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
				jQuery('.offer1').toggleClass('active', c > 0);
				jQuery('.offer2').toggleClass('active', c > 1000000);
            }
			jQuery('.service-card').click(function(){
				const input = jQuery(this).find('input')
				const check = !input.prop('checked')
				input.prop("checked", check);
				jQuery(this).toggleClass('active', check)
				servicesCost();
			})
        });
    </script>
	<style>
		.service-card.active{
			opacity: .7;
			border: 5px solid red;
		}
		#sale-actions{
			position: fixed;
			bottom: 0;
			right: 0;
			width: 100%;
			height: 50px;
			background-color: red;
			z-index: 1000;
		}
		.offer1.active, .offer2.active{
			opacity: .4;
		}
		.fixed-action-btn, #najva-subscribe-bell{
			display: none !important;
			
		}
	</style>
    <?php
}
