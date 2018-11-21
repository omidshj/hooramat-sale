<?php
add_action('admin_menu', 'hooramat_sale_services_menu');
function hooramat_sale_services_menu() {
  add_submenu_page( 'options-general.php', 'حراج ها', 'حراج ها', 'edit_posts', 'hooramat_sale_services', 'hooramat_sale_services' );
}

function hooramat_sale_services(){

  if(!empty($_GET['create']) && $_GET['create'] == 'group'){
        if(!empty($_POST['name']) && !empty($_POST['description']) ){
          global $wpdb;
          $wpdb->insert(
            $wpdb->prefix . 'hooramat_sale_groups',
            array(
              'name' => $_POST['name'],
              'description' => $_POST['description'],
              'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
              'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
            )
          );
          return wp_redirect( admin_url( '/options-general.php?page=hooramat_sale_services' ), 301 );
        }
        return hooramat_sale_group_create([]);
  }else if(!empty($_GET['create']) && $_GET['create'] == 'service'){
        if(!empty($_POST['name']) && !empty($_POST['description']) ){
          global $wpdb;
          $wpdb->insert(
            $wpdb->prefix . 'hooramat_sale_services',
            array(
              'group_id' => $_GET['group'],
              'name' => $_POST['name'],
              'description' => $_POST['description'],
              'total' => $_POST['total'],
              'price' => $_POST['price'],
              'sale' => $_POST['sale'],
            )
          );
          return wp_redirect( admin_url( "/options-general.php?page=hooramat_sale_services&group={$_GET['group']}" ), 301 );
        }
        return hooramat_sale_service_form([]);
  }else if (isset($_GET['edit'])){
        global $wpdb;
        if(!empty($_POST['name']) && !empty($_POST['description']) ){
          $wpdb->update(
            $wpdb->prefix . 'hooramat_sale_services',
            array(
              'name' => $_POST['name'],
              'description' => $_POST['description'],
              'total' => $_POST['total'],
              'price' => $_POST['price'],
              'start' => $_POST['startdate'] . ' ' . $_POST['starttime'],
              'finish' => $_POST['finishdate'] . ' ' . $_POST['finishtime']
            ),
            array ('id' => $_GET['edit'])
          );
          return wp_redirect( admin_url( '/options-general.php?page=hooramat_sale_services' ), 301 );
        }
        return hooramat_sale_service_form( $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services where id = {$_GET['edit']}", ARRAY_A ) );
  } else if (isset($_GET['group'])) {
        return hooramat_sale_group();
  } else {
        return hooramat_sale_groups();
  }
}

function hooramat_sale_groups(){
  global $wpdb;
  $groups_per_page = 20;
  $paged = isset($_GET['paged'])? $_GET['paged']: 1;
  $start = ($paged - 1) * $groups_per_page;
  $groups = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups", OBJECT );
  $total_groups = ceil( $wpdb->num_rows / $groups_per_page);
  $groups = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups limit $start, $groups_per_page", OBJECT );
  ?>
  <div class="wrap">
    <h1 class="wp-heading-inline">حراج ها</h1><a class="page-title-action" href="?page=hooramat_sale_services&create=group">افزودن</a>
    <div class="tablenav top">
      <div class="tablenav-pages">
        <?php
        if( $total_groups > 1 )  {
          $format = get_option('permalink_structure')? 'page/%#%/': '&paged=%#%';
          echo paginate_links(array(
            'base'          => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'        => $format,
            'current'       => $paged,
            'total'         => $total_groups,
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
        <th>توضیح</th>
        <th>شروع</th>
        <th>پایان</th>
      </tr>
      <?php foreach ($groups as $group): ?>
        <tr>
          <td><?= $group->id ?></td>
          <td><a href="?page=hooramat_sale_services&group=<?= $group->id ?>"><?= $group->name ?></a></td>
          <td><?= $group->description ?></td>
          <td><?= $group->start ?></td>
          <td><?= $group->finish ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
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
      <input type="Submit" value="ذخیره" name="register" class="button button-primary button-large" time="<?= $t ?>">
    </form>
  </div>
  <?php
}

function hooramat_sale_group(){
  global $wpdb;
  $group = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}hooramat_sale_groups where id = {$_GET['group']}", ARRAY_A );
  $services_per_page = 20;
  $paged = isset($_GET['paged'])? $_GET['paged']: 1;
  $start = ($paged - 1) * $services_per_page;
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services", OBJECT );
  $total_services = ceil( $wpdb->num_rows / $services_per_page);
  $services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}hooramat_sale_services limit $start, $services_per_page", OBJECT );
  ?>
  <div class="wrap">
    <h1 class="wp-heading-inline"><?= $group['name'] ?></h1>
    <br><br>
    <form method="POST">
      <label for="name">نام: </label>
      <input type="text" id="name" name="name" value="<?= $_POST['name'] ?? $group['name'] ?? '' ?>"><br>

      <label for="description">توضیحات: </label>
      <input type="text" id="description" name="description" value="<?= $_POST['description'] ?? $group['description'] ?? '' ?>"><br>

      <label for="startdate">شروع: </label>
      <input type="date" id="startdate" name="startdate" value="<?= $_POST['start'] ?? date("Y-m-d", strtotime($group['start'])) ?? '' ?>">
      <input type="time" id="starttime" name="starttime" value="<?= $_POST['starttime'] ?? date("H:i:s", strtotime($group['start'])) ?? '' ?>"><br>

      <label for="finishdate">پایان: </label>
      <input type="date" id="finishdate" name="finishdate" value="<?= $_POST['finishdate'] ?? date("Y-m-d", strtotime($group['finish'])) ?? '' ?>">
      <input type="time" id="finishtime" name="finishtime" value="<?= $_POST['finishtime'] ?? date("H:i:s", strtotime($group['finish'])) ?? '' ?>"><br>

      <input type="Submit" value="ذخیره" name="register" class="button button-primary button-large" time="<?= $t ?>">
    </form>

    <div class="tablenav top">
      <div class="tablenav-pages">
        <?php
        if( $total_services > 1 )  {
          $format = get_option('permalink_structure')? 'page/%#%/': '&paged=%#%';
          echo paginate_links(array(
            'base'          => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'        => $format,
            'current'       => $paged,
            'total'         => $total_services,
            'mid_size'      => 2,
            'prev_text'     => is_rtl()? '&rarr;': '&larr;',
            'next_text'     => is_rtl()? '&larr;': '&rarr;',
           ));
        }
        ?>
      </div>
    </div>
    <a class="page-title-action" href="?page=hooramat_sale_services&group=<?=$group['id']?>&create=service">افزودن خدمات</a>
    <table class="wp-list-table widefat fixed striped ">
      <tr>
        <th>شناسه</th>
        <th>نام</th>
        <th>توضیح</th>
        <th>تعداد</th>
        <th>قیمت</th>
        <th>حراج</th>
      </tr>
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
    </table>
  </div>
  <?php
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

      <label for="price">قیمت: </label>
      <input type="text" id="price" name="price" value="<?= $_POST['price'] ?? $data['price'] ?? '' ?>"><br>

      <br>
      <input type="Submit" value="ذخیره" name="register" class="button button-primary button-large" time="<?= $t ?>">
    </form>
  </div>
  <?php
}


?>
