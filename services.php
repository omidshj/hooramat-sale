<?php

add_action('admin_menu', function () {
  add_submenu_page(
    'options-general.php',
    'حراج ها',
    'حراج ها',
    'edit_posts',
    'hooramat_sale_services',
    'hooramat_sale_services'
  );
});

function hooramat_sale_services()
{
  if (!empty($_GET['group'])) {
    return hooramat_sale_group();
  } else {
    return hooramat_sale_groups();
  }
}

function hooramat_sale_groups()
{
  $edit = 0;
  foreach ($_POST as $key => $value) if ($value == 'ویرایش') $edit = $key;
  $stored = hooramat_sale_groups_store();
  $updated = hooramat_sale_groups_update();
  global $wpdb;
  $groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hooramat_sale_groups", OBJECT);
?>
  <div class="wrap">
    <h1 class="wp-heading-inline">حراج ها</h1>
    <form method="POST">
      <table class="wp-list-table widefat fixed striped ">
        <tr>
          <th>شناسه</th>
          <th>نام</th>
          <th>توضیح</th>
          <th>شروع</th>
          <th>پایان</th>
          <th></th>
        </tr>
        <?php foreach ($groups as $group) : ?>
          <tr>
            <?php if ($group->id == $edit) : ?>
              <td><?= $group->id ?><input type="hidden" name="edit" value="<?= $group->id ?>"></td>
              <td><input type="text" name="name" value="<?= $_POST['name'] ?? $group->name ?>"></td>
              <td><input type="text" name="description" value="<?= $_POST['description'] ?? $group->description ?>"></td>
              <td>
                <input type="date" name="startdate" value="<?= $_POST['startdate'] ?? date("Y-m-d", strtotime($group->start)) ?>"><br>
                <input type="time" name="starttime" value="<?= $_POST['starttime'] ?? date("H:i:s", strtotime($group->start)) ?>">
              </td>
              <td>
                <input type="date" name="finishdate" value="<?= $_POST['finishdate'] ?? date("Y-m-d", strtotime($group->finish)) ?>"><br>
                <input type="time" name="finishtime" value="<?= $_POST['finishtime'] ?? date("H:i:s", strtotime($group->finish)) ?>">
              </td>
              <td><input type="Submit" value="ذخیره" name="<?= $group->id ?>" class="button button-primary button-large"></td>
            <?php else : ?>
              <td><?= $group->id ?></td>
              <td><a href="?page=hooramat_sale_services&group=<?= $group->id ?>"><?= $group->name ?></a></td>
              <td><?= $group->description ?></td>
              <td><?= $group->start ?></td>
              <td><?= $group->finish ?></td>
              <td>
                <?php if ($edit == 0) : ?>
                  <input type="Submit" value="ویرایش" name="<?= $group->id ?>" class="button button-primary button-large" ">
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if ($edit == 0) : ?>
          <tr>
            <td></td>
            <td><input type=" text" name="create_name" value="<?= ($stored || empty($_POST['create_name'])) ? '' :  $_POST['create_name'] ?>"></td>
              <td><input type="text" name="create_description" value="<?= ($stored || empty($_POST['create_description'])) ? '' : $_POST['create_description'] ?>"></td>
              <td>
                <input type="date" name="create_startdate" value="<?= date("Y-m-d") ?>"><br>
                <input type="time" name="create_starttime" value="<?= date("H:i:s") ?>">
              </td>
              <td>
                <input type="date" name="create_finishdate" value="<?= date("Y-m-d") ?>"><br>
                <input type="time" name="create_finishtime" value="<?= date("H:i:s") ?>">
              </td>
              <td>
                <input type="Submit" value="افزودن" name="register" class="button button-primary button-large">
              </td>
          </tr>
        <?php endif; ?>
      </table>
    </form>
  </div>
<?php
}

function hooramat_sale_groups_store()
{
  if (!empty($_POST['create_name']) && !empty($_POST['create_description'])) {
    global $wpdb;
    $wpdb->insert(
      $wpdb->prefix . 'hooramat_sale_groups',
      array(
        'name' => $_POST['create_name'],
        'description' => $_POST['create_description'],
        'start' => $_POST['create_startdate'] . ' ' . $_POST['create_starttime'],
        'finish' => $_POST['create_finishdate'] . ' ' . $_POST['create_finishtime'],
      )
    );
    return true;
  }
  return false;
}

function hooramat_sale_groups_update()
{
  if (!empty($_POST['edit']) && !empty($_POST['name']) && !empty($_POST['description'])) {
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'hooramat_sale_groups',
      array(
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
        'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
      ),
      array('id' => $_POST['edit'])
    );
    return true;
  }
  return false;
}





function hooramat_sale_group()
{
  $service_edit = 0;
  foreach ($_POST as $key => $value) if ($value == 'ویرایش خدمت') $service_edit = $key;
  $coupon_edit = 0;
  foreach ($_POST as $key => $value) if ($value == 'ویرایش کوپن') $coupon_edit = $key;
  $service_stored = hooramat_sale_group_service_store();;
  $service_updated = hooramat_sale_group_service_update();
  $coupon_stored = hooramat_sale_group_coupon_store();;
  $coupon_updated = hooramat_sale_group_coupon_update();
  global $wpdb;
  $group = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$_GET['group']}", OBJECT);
  $services = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hooramat_sale_services  where group_id = {$_GET['group']}", OBJECT);
  usort($services, function ($a, $b) {
    return $a->sort > $b->sort;
  });
  $coupons = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hooramat_sale_coupons  where group_id = {$_GET['group']}", OBJECT);
?>
  <div class="wrap">
    <h1 class="wp-heading-inline"><?= $group->name ?></h1>
    <br><br>

    <h1 class="wp-heading-inline">خدمات</h1>
    <form method="POST">
      <table class="wp-list-table widefat fixed striped ">
        <tr>
          <th>ترتیب</th>
          <th>نام</th>
          <th>توضیح</th>
          <th>شناسه مدیا</th>
          <th>تعداد</th>
          <th>قیمت</th>
          <th>حراج</th>
          <th></th>
        </tr>
        <?php foreach ($services as $service) : ?>
          <tr>
            <?php if ($service->id == $service_edit) : ?>
              <td>
                <input type="hidden" name="su" value="<?= $service->id ?>">
                <input type="text" name="su_sort" value="<?= $service->sort ?>">
              </td>
              <td><input type="text" name="su_name" value="<?= $service->name ?>"></td>
              <td><input type="text" name="su_description" value="<?= $service->description ?>"></td>
              <td><input type="text" name="su_media" value="<?= $service->media ?>"></td>
              <td><input type="text" name="su_total" value="<?= $service->total ?>"></td>
              <td><input type="text" name="su_price" value="<?= $service->price ?>"></td>
              <td><input type="text" name="su_sale" value="<?= $service->sale ?>"></td>
              <td>
                <input type="Submit" name="action" value="ذخیره" class="button-primary">
                <input type="Submit" name="action" value="حذف" class="button">
              </td>
            <?php else : ?>
              <td><?= $service->sort ?? 0 ?></td>
              <td><?= $service->name ?></td>
              <td><?= $service->description ?></td>
              <td><?= $service->media ?></td>
              <td><?= $service->total ?></td>
              <td><?= $service->price ?></td>
              <td><?= $service->sale ?></td>
              <td>
                <?php if ($service_edit == 0 && $coupon_edit == 0) : ?>
                  <input type="Submit" value="ویرایش خدمت" name="<?= $service->id ?>" class="button-primary" ">
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if ($service_edit == 0 && $coupon_edit == 0) : ?>
          <tr>
            <td><input type=" text" name="ss_order" value="<?= ($service_stored || empty($_POST['ss_order'])) ? '' :  $_POST['ss_order'] ?>"></td>
              <td><input type="text" name="ss_name" value="<?= ($service_stored || empty($_POST['ss_name'])) ? '' :  $_POST['ss_name'] ?>"></td>
              <td><input type="text" name="ss_description" value="<?= ($service_stored || empty($_POST['ss_description'])) ? '' : $_POST['ss_description'] ?>"></td>
              <td><input type="text" name="ss_media" value="<?= ($service_stored || empty($_POST['ss_media'])) ? '' : $_POST['ss_media'] ?>"></td>
              <td><input type="text" name="ss_total" value="<?= ($service_stored || empty($_POST['ss_total'])) ? '' : $_POST['ss_total'] ?>"></td>
              <td><input type="text" name="ss_price" value="<?= ($service_stored || empty($_POST['ss_price'])) ? '' : $_POST['ss_price'] ?>"></td>
              <td><input type="text" name="ss_sale" value="<?= ($service_stored || empty($_POST['ss_sale'])) ? '' : $_POST['ss_sale'] ?>"></td>
              <td><input type="Submit" value="افزودن" name="register" class="button-primary"></td>
          </tr>
        <?php endif; ?>
      </table>
    </form>

    <br /><br /><br />

    <h1 class="wp-heading-inline">کوپن ها</h1>
    <form method="POST">
      <table class="wp-list-table widefat fixed striped ">
        <tr>
          <th>کد</th>
          <th>شرح</th>
          <th>تعداد</th>
          <th>درصد</th>
          <th>مبلغ</th>
          <th></th>
        </tr>
        <?php foreach ($coupons as $coupon) : ?>
          <tr>
            <?php if ($coupon->id == $coupon_edit) : ?>
              <td>
                <input type="hidden" name="cu" value="<?= $coupon->id ?>">
                <input type="text" name="cu_code" value="<?= $coupon->code ?>">
              </td>
              <td><input type="text" name="cu_description" value="<?= $coupon->description ?>"></td>
              <td><input type="text" name="cu_total" value="<?= $coupon->total ?>"></td>
              <td><input type="text" name="cu_percent" value="<?= $coupon->percent ?>"></td>
              <td><input type="text" name="cu_discount" value="<?= $coupon->discount ?>"></td>
              <td><input type="Submit" value="ذخیره" class="button button-primary"></td>
            <?php else : ?>
              <td><?= $coupon->code ?></td>
              <td><?= $coupon->description ?></td>
              <td><?= $coupon->total ?></td>
              <td><?= $coupon->percent ?></td>
              <td><?= $coupon->discount ?></td>
              <td>
                <?php if ($coupon_edit == 0) : ?>
                  <input type="Submit" value="ویرایش کوپن" name="<?= $coupon->id ?>" class="button-primary" ">
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if ($coupon_edit == 0) : ?>
          <tr>
            <td><input type=" text" name="cs_code" value="<?= ($service_stored || empty($_POST['cs_order'])) ? '' :  $_POST['cs_order'] ?>"></td>
              <td><input type="text" name="cs_description" value="<?= ($service_stored || empty($_POST['cs_description'])) ? '' : $_POST['cs_description'] ?>"></td>
              <td><input type="text" name="cs_total" value="<?= ($service_stored || empty($_POST['cs_total'])) ? '' : $_POST['cs_total'] ?>"></td>
              <td><input type="text" name="cs_percent" value="<?= ($service_stored || empty($_POST['cs_percent'])) ? '' : $_POST['cs_percent'] ?>"></td>
              <td><input type="text" name="cs_discount" value="<?= ($service_stored || empty($_POST['cs_discount'])) ? '' : $_POST['cs_discount'] ?>"></td>
              <td><input type="Submit" value="افزودن" name="register" class="button-primary"></td>
          </tr>
        <?php endif; ?>
      </table>
    </form>
  </div>
<?php
}
function hooramat_sale_group_service_store()
{
  if (!empty($_POST['ss_name']) && !empty($_POST['ss_description'])) {
    global $wpdb;
    $wpdb->insert(
      $wpdb->prefix . 'hooramat_sale_services',
      array(
        'group_id' => $_GET['group'],
        'name' => $_POST['ss_name'],
        'description' => $_POST['ss_description'],
        'total' => $_POST['ss_total'],
        'price' => $_POST['ss_price'],
        'sale' => $_POST['ss_sale'],
        'media' => $_POST['su_media'],
      )
    );
    return true;
  }
  return false;
}
function hooramat_sale_group_service_update()
{
  if (isset($_POST['su'])) {
    global $wpdb;
    if ($_POST['action'] == 'حذف') {
      $wpdb->delete($wpdb->prefix . 'hooramat_sale_services', ['id' => $_POST['su']]);
      return true;
    } elseif ($_POST['action'] == 'ذخیره') {
      $wpdb->update(
        $wpdb->prefix . 'hooramat_sale_services',
        [
          'sort' => $_POST['su_sort'],
          'name' => $_POST['su_name'],
          'description' => $_POST['su_description'],
          'total' => $_POST['su_total'],
          'price' => $_POST['su_price'],
          'sale' => $_POST['su_sale'],
          'media' => $_POST['su_media'],
        ],
        ['id' => $_POST['su']]
      );
      return true;
    }
  }
  return false;
}
function hooramat_sale_group_coupon_store()
{
  if (!empty($_POST['cs_code'])) {
    global $wpdb;
    $wpdb->insert(
      $wpdb->prefix . 'hooramat_sale_coupons',
      array(
        'group_id' => $_GET['group'],
        'code' => $_POST['cs_code'],
        'description' => $_POST['cs_description'],
        'total' => $_POST['cs_total'],
        'percent' => $_POST['cs_percent'],
        'discount' => $_POST['cs_discount'],
      )
    );
    return true;
  }
  return false;
}
function hooramat_sale_group_coupon_update()
{
  if (isset($_POST['cu'])) {
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'hooramat_sale_coupons',
      [
        'code' => $_POST['cu_code'],
        'description' => $_POST['cu_description'],
        'total' => $_POST['cu_total'],
        'percent' => $_POST['cu_percent'],
        'discount' => $_POST['cu_discount'],
      ],
      ['id' => $_POST['cu']]
    );
    return true;
  }
  return false;
}
?>