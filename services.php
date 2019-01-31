<?php
add_action('admin_menu', 'hooramat_sale_services_menu');
function hooramat_sale_services_menu() {
  add_submenu_page( 'options-general.php', 'حراج ها', 'حراج ها', 'edit_posts', 'hooramat_sale_services', 'hooramat_sale_services' );
}

function hooramat_sale_services(){
  if(!empty($_GET['create']) && $_GET['create'] == 'group'){
        // CREATE GROUP **********************************************************************
        // if(!empty($_POST['name']) && !empty($_POST['description']) ){
        //   global $wpdb;
        //   $wpdb->insert(
        //     $wpdb->prefix . 'hooramat_sale_groups',
        //     array(
        //       'name' => $_POST['name'],
        //       'description' => $_POST['description'],
        //       'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
        //       'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
        //     )
        //   );
        //   return wp_redirect( admin_url( '/options-general.php?page=hooramat_sale_services' ), 301 );
        // }
        // return hooramat_sale_group_create([]);
  }else if(!empty($_GET['create']) && $_GET['create'] == 'service'){
        // CREATE SERVICE *******************************************************************
        // if(!empty($_POST['name']) && !empty($_POST['description']) ){
        //   global $wpdb;
        //   $wpdb->insert(
        //     $wpdb->prefix . 'hooramat_sale_services',
        //     array(
        //       'group_id' => $_GET['group'],
        //       'name' => $_POST['name'],
        //       'description' => $_POST['description'],
        //       'total' => $_POST['total'],
        //       'price' => $_POST['price'],
        //       'sale' => $_POST['sale'],
        //     )
        //   );
        //   return wp_redirect( admin_url( "/options-general.php?page=hooramat_sale_services&group={$_GET['group']}" ), 301 );
        // }
        // return hooramat_sale_service_form([]);
  }else if (!empty($_GET['group']) && !empty($_GET['service'])){
        // UPDATE SERVICE ************************************************************************
        // global $wpdb;
        // if(!empty($_POST['name']) && !empty($_POST['description']) ){
        //   $wpdb->update(
        //     $wpdb->prefix . 'hooramat_sale_services',
        //     array(
        //       'name' => $_POST['name'],
        //       'description' => $_POST['description'],
        //       'total' => $_POST['total'],
        //       'price' => $_POST['price'],
        //       'sale' => $_POST['sale'],
        //     ),
        //     array ('id' => $_GET['service'])
        //   );
        //   return wp_redirect( admin_url( "/options-general.php?page=hooramat_sale_services&group={$_GET['group']}" ), 301 );
        // }
        // return hooramat_sale_service_form( $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id = {$_GET['service']}", ARRAY_A ) );
  } else if (!empty($_GET['group'])) {
        // UPDATE GROUP ******************************************************************************
        // if(!empty($_POST['name']) && !empty($_POST['description']) ){
        //   global $wpdb;
        //   $wpdb->update(
        //     $wpdb->prefix . 'hooramat_sale_groups',
        //     array(
        //       'name' => $_POST['name'],
        //       'description' => $_POST['description'],
        //       'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
        //       'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
        //     ),
        //     array ('id' => $_GET['group'])
        //   );
        //   return wp_redirect( admin_url( "/options-general.php?page=hooramat_sale_services&group={$_GET['group']}" ), 301 );
        // }
        return hooramat_sale_group();
  } else {
        return hooramat_sale_groups();
  }
}

function hooramat_sale_groups(){
  $edit = 0; foreach($_POST as $key => $value) if ($value == 'ویرایش') $edit = $key; 
  $stored = hooramat_sale_groups_store();
  $updated = hooramat_sale_groups_update();
  
  global $wpdb;
  $groups = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups", OBJECT );
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
        <?php foreach ($groups as $group): ?>
          <tr>
            <?php if ($group->id == $edit): ?>
              <td><?= $group->id ?><input type="hidden" name="edit" value="<?= $group->id ?>"></td>
              <td><input type="text" name="name" value="<?= $_POST['name'] ?? $group->name ?>"></td>
              <td><input type="text" name="description" value="<?= $_POST['description'] ?? $group->description ?>"></td>
              <td>
                <input type="date" name="startdate" value="<?= $_POST['startdate'] ?? date("Y-m-d", strtotime($group->startdate)) ?>"><br>
                <input type="time" name="starttime" value="<?= $_POST['starttime'] ?? date("H:i:s", strtotime($group->startdate)) ?>">
              </td>
              <td>
                <?= $group->finish ?>
                <input type="date" name="finishdate" value="<?= $_POST['finishdate'] ?? date("Y-m-d", strtotime($group->finishdate)) ?>"><br>
                <input type="time" name="finishtime" value="<?= $_POST['finishtime'] ?? date("H:i:s", strtotime($group->finistime)) ?>">
              </td>
              <td><input type="Submit" value="ذخیره" name="<?= $group->id ?>" class="button button-primary button-large"></td>
            <?php else: ?>
              <td><?= $group->id ?></td>
              <td><a href="?page=hooramat_sale_services&group=<?= $group->id ?>"><?= $group->name ?></a></td>
              <td><?= $group->description ?></td>
              <td><?= $group->start ?></td>
              <td><?= $group->finish ?></td>
              <td>
                <?php if($edit == 0): ?>
                  <input type="Submit" value="ویرایش" name="<?= $group->id ?>" class="button button-primary button-large" ">
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if($edit == 0): ?>
          <tr>
            <td></td>
            <td><input type="text" name="create_name" value="<?= $stored? '':  $_POST['create_name'] ?>"></td>
            <td><input type="text" name="create_description" value="<?= $stored? '': $_POST['create_description'] ?>"></td>
            <td>
              <input type="date" name="create_startdate" value="<?= date("Y-m-d") ?>"><br>
              <input type="time" name="create_starttime" value="<?= date("H:i:s") ?>">
            </td>
            <td>
              <input type="date" name="create_finishdate" value="<?= date("Y-m-d") ?>"><br>
              <input type="time" name="create_finishtime" value="<?= date("H:i:s") ?>">
            </td>
            <td>
              <input type="Submit" value="افزودن" name="register" class="button button-primary button-large" >
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </form>
  </div>
  <?php
}
function hooramat_sale_groups_store(){
  if(!empty($_POST['create_name']) && !empty($_POST['create_description']) ){
    global $wpdb;
    $wpdb->insert(
      $wpdb->prefix . 'hooramat_sale_groups',
      array(
        'name' => $_POST['create_name'],
        'description' => $_POST['create_description'],
        'start' => $_POST['create_startdate'] . ' ' . $_POST['create_starttime'],
        'finish' => $_POST['create_finishdate'] . ' ' . $_POST['create_finishtime'],
        'services' => serialize([]),
        'coupons' => serialize([]),
      )
    );
    return true;
  }
  return false;
}
function hooramat_sale_groups_update(){
  if(!empty($_POST['edit']) && !empty($_POST['name']) && !empty($_POST['description']) ){
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'hooramat_sale_groups',
      array(
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
        'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
      ),
      array ('id' => $_POST['edit'])
    );
    return true;
  }
  return false;
}



















function hooramat_sale_group(){
  global $wpdb;
  $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$_GET['group']}", OBJECT );
  $service_edit = -1; foreach($_POST as $key => $value) if ($value == 'ویرایش خدمت') $service_edit = $key; 
  $service_stored = hooramat_sale_group_service_store($group);;
  $service_updated = hooramat_sale_group_service_update($group);;
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services", OBJECT );
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
          <th>تعداد</th>
          <th>قیمت</th>
          <th>حراج</th>
          <th></th>
        </tr>
        <?php foreach ( unserialize($group->services) as $index => $service): ?>
          <tr>
            <?php if ($index == $service_edit): ?>
              <td>
                <input type="hidden" name="su" value="<?= $index ?>">
                <input type="text" name="su_order" value="<?= $service['order'] ?>">
              </td>
              <td><?= $service['name'] ?></td>
              <td><?= $service['description'] ?></td>
              <td><?= $service['total'] ?></td>
              <td><?= $service['price'] ?></td>
              <td><?= $service['sale'] ?></td>
              <td><input type="Submit" value="ذخیره" class="button button-primary" ></td>
            <?php else: ?>
              <td><?= $service['order'] ?></td>
              <td><?= $service['name'] ?></td>
              <td><?= $service['description'] ?></td>
              <td><?= $service['total'] ?></td>
              <td><?= $service['price'] ?></td>
              <td><?= $service['sale'] ?></td>
              <td>
                <?php if($service_edit == -1): ?>
                  <input type="Submit" value="ویرایش خدمت" name="<?= $index ?>" class="button button-primary button-large" ">
                <?php endif; ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if($service_edit == -1): ?>
          <tr>
            <td><input type="text" name="ss_order" value="<?= ($service_stored || empty($_POST['ss_order']))? '':  $_POST['ss_order'] ?>"></td>
            <td><input type="text" name="ss_name" value="<?= ($service_stored || empty($_POST['ss_name']))? '':  $_POST['ss_name'] ?>"></td>
            <td><input type="text" name="ss_description" value="<?= ($service_stored || empty($_POST['ss_description']))? '': $_POST['ss_description'] ?>"></td>
            <td><input type="text" name="ss_total" value="<?= ($service_stored || empty($_POST['ss_total']))? '': $_POST['ss_total'] ?>"></td>
            <td><input type="text" name="ss_price" value="<?= ($service_stored || empty($_POST['ss_price']))? '': $_POST['ss_price'] ?>"></td>
            <td><input type="text" name="ss_sale" value="<?= ($service_stored || empty($_POST['ss_sale']))? '': $_POST['ss_sale'] ?>"></td>
            <td><input type="Submit" value="افزودن" name="register" class="button button-primary button-large" ></td>
          </tr>
        <?php endif; ?>
      </table>
    </form>

    <br/>
    <br/>
    <br/>

    <?php print_r( unserialize($group->coupons) ); ?>
    <h1 class="wp-heading-inline">کوپن ها</h1>
    <table class="wp-list-table widefat fixed striped ">
      <tr>
        <th>شناسه</th>
        <th>نام</th>
        <th>توضیح</th>
        <th>تعداد</th>
        <th>قیمت</th>
        <th>حراج</th>
      </tr>
      <?php if(!empty(unserialize($group->coupons))): ?>
        <?php foreach ($services as $service): ?>
          <tr>
            <td><?= $service->id ?></td>
            <td><a href="?page=hooramat_sale_services&group=<?= $group['id'] ?>&service=<?= $service->id ?>"><?= $service->name ?></a></td>
            <td><?= $service->description ?></td>
            <td><?= $service->total ?></td>
            <td><?= $service->price ?></td>
            <td><?= $service->sale ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>
  <?php
}
function hooramat_sale_group_service_store($group){
  if(!empty($_POST['ss_name']) && !empty($_POST['ss_description']) ){
    $services = unserialize($group->services);
    $services[] = [
      'order' => 0,
      'name' => $_POST['ss_name'],
      'description' => $_POST['ss_description'],
      'total' => $_POST['ss_total'],
      'price' => $_POST['ss_price'],
      'sale' => $_POST['ss_sale']
    ];
    print_r($services);
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'hooramat_sale_groups',
      array(
        'services' => serialize($services)
      ),
      array ('id' => $group->id)
    );
    return true;
  }
  return false;
}
function hooramat_sale_group_service_update($group){
  if(!empty($_POST['su']) && !empty($_POST['su_name']) && !empty($_POST['su_description']) ){
    $services = unserialize($group->services);
    $services[$_POST['su']]['order'] = 1;
    print_r($services);
    $wpdb->update($wpdb->prefix.'hooramat_sale_groups', ['services' => serialize($services)], ['id' => $group->id]);
    return true;
  }
  return false;
}

function hooramat_sale_service_form($data){
  ?>
  <div class="wrap">
    <h1 class="wp-heading-inline">افزودن حراج</h1>
    <br><br>
    <form method="POST">
      <label for="name">نام: </label>
      <input type="text" id="name" name="name" value="<?= $_POST['name'] ?? $data['name'] ?? '' ?>"><br>

      <label for="description">توضیحات: </label>
      <input type="text" id="description" name="description" value="<?= $_POST['description'] ?? $data['description'] ?? '' ?>"><br>

      <label for="total">تعداد: </label>
      <input type="text" id="total" name="total" value="<?= $_POST['total'] ?? $data['total'] ?? '' ?>"><br>

      <label for="price">قیمت: </label>
      <input type="text" id="price" name="price" value="<?= $_POST['price'] ?? $data['price'] ?? '' ?>"><br>

      <label for="sale">حراج: </label>
      <input type="text" id="sale" name="sale" value="<?= $_POST['sale'] ?? $data['sale'] ?? '' ?>"><br>

      <br>
      <input type="Submit" value="ذخیره" name="register" class="button button-primary button-large" >
    </form>
  </div>
  <?php
}








function hooramat_sale_group_create($data){
  ?>
  <div class="wrap">
    <h1 class="wp-heading-inline">افزودن حراج</h1>
    <br><br>
    <form method="POST">
      <label for="name">نام: </label>
      <input type="text" id="name" name="name" value="<?= $_POST['name'] ?? $data['name'] ?? '' ?>"><br>

      <label for="description">توضیحات: </label>
      <input type="text" id="description" name="description" value="<?= $_POST['description'] ?? $data['description'] ?? '' ?>"><br>

      <label for="startdate">شروع: </label>
      <input type="date" id="startdate" name="startdate" value="<?= $_POST['start'] ?? date("Y-m-d", strtotime($data['start'])) ?? '' ?>">
      <input type="time" id="starttime" name="starttime" value="<?= $_POST['starttime'] ?? date("H:i:s", strtotime($data['start'])) ?? '' ?>"><br>

      <label for="finishdate">پایان: </label>
      <input type="date" id="finishdate" name="finishdate" value="<?= $_POST['finishdate'] ?? date("Y-m-d", strtotime($data['finish'])) ?? '' ?>">
      <input type="time" id="finishtime" name="finishtime" value="<?= $_POST['finishtime'] ?? date("H:i:s", strtotime($data['finish'])) ?? '' ?>"><br>

      <br>
      <input type="Submit" value="ذخیره" name="register" class="button button-primary button-large">
    </form>
  </div>
  <?php
}
?>
