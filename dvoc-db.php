<?php
/*
   Plugin Name: DVOC DB
   Plugin URI: http:/dvoc.org
   Version: 0.0.1
   Author: Navin Sasikumar
   Description: Save member info to a database. Shortcodes to post them to pages. Update info.
   Text Domain: dvoc-db
   License: GPL3
  */

function dvoc_init() {

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dvoc_initialize_tables();

    //dvoc_cf7_integrate();
}

function dvoc_initialize_tables() {
    dvoc_create_members_table();
    dvoc_create_officers_table();
    dvoc_create_awards_table();
    dvoc_create_committees_table();
    dvoc_create_committee_members_table();
}

function dvoc_create_members_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";

    $charset_collate = $wpdb->get_charset_collate();

    //Status can be active, inactive, deceased, pending or declined

    $sql = "CREATE TABLE $table_name (
        id mediumint(6) NOT NULL AUTO_INCREMENT,
        first_name tinytext NOT NULL,
        last_name tinytext NOT NULL,
        bio text,
        email tinytext,
        website tinytext,
        headline tinytext,
        start_date datetime,
        end_date datetime,
        status tinytext,
        fellow tinyint(1),
        life tinyint(1),
        honorary tinyint(1),
        UNIQUE KEY id (id)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_create_officers_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_officers";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        year smallint(4),
        president mediumint(6),
        vice_president mediumint(6),
        secretary mediumint(6),
        treasurer mediumint(6),
        editor mediumint(6),
        council mediumint(6)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_create_awards_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_awards";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        year smallint(4),
        devoc mediumint(6),
        stone mediumint(6),
        edge mediumint(6),
        potter mediumint(6)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_create_committees_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committees";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id smallint(4) NOT NULL AUTO_INCREMENT,
        committee tinytext NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_create_committee_members_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committee_members";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        year smallint(4),
        member_id mediumint(6) NOT NULL,
        committee_id smallint(3) NOT NULL,
        chairperson tinyint(1)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_uninstall() {
    global $wpdb;

    //Members Table
    $table_name = $wpdb->prefix . "dvoc_members";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    //Officers Table
    $table_name = $wpdb->prefix . "dvoc_officers";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    //Awards Table
    $table_name = $wpdb->prefix . "dvoc_awards";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    //Committees Table
    $table_name = $wpdb->prefix . "dvoc_committees";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    //Committee Members Table
    $table_name = $wpdb->prefix . "dvoc_committee_members";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function dvoc_add_pages() {
    //add an item to the menu
    add_menu_page (
        'DVOC Database',
        'DVOC Database',
        'manage_options',
        'dvoc-db',
        'dvoc_list_members',
        '',
        '23.56'
    );
    dvoc_add_submenus();
}

function dvoc_add_submenus() {
    add_submenu_page('dvoc-db', 'Add New Member', 'Add New Member', 'manage_options', 'dvoc-add-member', 'dvoc_add_member');
    add_submenu_page('dvoc-db', 'List Members', 'List Members', 'manage_options', 'dvoc-list-members', 'dvoc_list_members');
    add_submenu_page('dvoc-db', 'Add New Committee', 'Add New Committee', 'manage_options', 'dvoc-add-committee', 'dvoc_add_committee');
    add_submenu_page('dvoc-db', 'List Members', 'List Committees', 'manage_options', 'dvoc-list-committees', 'dvoc_list_committees');
}

add_action('admin_menu', 'dvoc_add_pages');

function dvoc_view_tables() {
    global $wpdb;
    $results = $wpdb->get_results('show tables like "wp_dvoc_%"', 'ARRAY_N');
    ?>
    <div class="wrap">
        <h2>DVOC Tables</h2>
        <?php
            foreach($results as $table) {
                echo $table[0] . "<br>";
            }
        ?>
    </div>
    <?php
}

function dvoc_add_member() {
    $success = $_GET['success'];
    if ($success === 'false') {
    ?>
        <div class="notice notice-error is-dismissible">
            Member could not be added
        </div>
    <?php
    }
    ?>
    <div class="wrap">
        <h2>Add New Member</h2>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29">
            <input type="hidden" name="action" value="dvoc_add_member"/>
<p>First Name<br>
    <span class="wpcf7-form-control-wrap firstName"><input type="text" name="firstName" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
<p>Last Name<br>
    <span class="wpcf7-form-control-wrap lastName"><input type="text" name="lastName" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Email<br>
    <span class="wpcf7-form-control-wrap email"><input type="email" name="email" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </p>
<p>Headline<br>
    <span class="wpcf7-form-control-wrap headline"><input type="text" name="headline" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Website<br>
    <span class="wpcf7-form-control-wrap website"><input type="text" name="website" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Start Date<br>
    <span class="wpcf7-form-control-wrap startDate"><input type="text" name="startDate" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>End Date<br>
    <span class="wpcf7-form-control-wrap endDate"><input type="text" name="endDate" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Status<br>
    <span class="wpcf7-form-control-wrap status"><input type="text" name="status" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Bio<br>
    <span class="wpcf7-form-control-wrap bio"><textarea name="bio" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span> </p>
<p><span class="wpcf7-form-control-wrap life"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="life[]" value="Life Member">&nbsp;<span class="wpcf7-list-item-label">Life Member</span></span></span></span></p>
<p><span class="wpcf7-form-control-wrap honorary"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="honorary[]" value="Honorary Member">&nbsp;<span class="wpcf7-list-item-label">Honorary Member</span></span></span></span></p>
<p><span class="wpcf7-form-control-wrap fellow"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="fellow[]" value="Fellow">&nbsp;<span class="wpcf7-list-item-label">Fellow</span></span></span></span></p>
<p><input type="submit" value="Submit" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
</form>
    </div>
    <?php
}
add_action('admin_post_dvoc_add_member', 'dvoc_store_member');

function dvoc_member_array() {
    $arr = array(
        'first_name' => isset($_POST['firstName']) ? $_POST['firstName'] : '',
        'last_name' => isset($_POST['lastName']) ? $_POST['lastName'] : '',
        'email' => isset($_POST['email']) ? $_POST['email'] : '',
        'bio' => isset($_POST['bio']) ? $_POST['bio'] : '',
        'website' => isset($_POST['website']) ? $_POST['website'] : '',
        'headline' => isset($_POST['headline']) ? $_POST['headline'] : '',
        'start_date' => isset($_POST['startDate']) ? $_POST['startDate'] : '',
        'end_date' => isset($_POST['endDate']) ? $_POST['endDate'] : '',
        'status' => isset($_POST['status']) ? $_POST['status'] : 'pending',
        'fellow' => isset($_POST['fellow']) ? 1 : 0,
        'life' => isset($_POST['life']) ? 1 : 0,
        'honorary' => isset($_POST['honorary']) ? 1 : 0
    );
    return $arr;
}

function dvoc_store_member() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";
    $arr = dvoc_member_array();

    $result = $wpdb->insert(
        $table_name,
        $arr
    );
    if ($result === 1) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-members&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-add-member&success=$result", 'http'), 301);
    }
}

function dvoc_member_display($results) {
    ?>
    <div class="wrap">
        <h2>DVOC Members</h2>
        <?php show_search_box('members'); ?>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Bio</th>
                <th>Website</th>
                <th>Headline</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Fellow</th>
                <th>Lifetime</th>
                <th>Honorary</th>
                <th>Edit</th>
            <tr>
        <?php
            foreach($results as $member) {
                $editUrl = admin_url("admin.php?page=dvoc-list-members&action=edit&member_id=" . $member['id'] , 'http');
                echo "<tr>";
                echo "<td>" . $member['first_name'] . "</td>";
                echo "<td>" . $member['last_name'] . "</td>";
                echo "<td>" . $member['email'] . "</td>";
                echo "<td>" . $member['bio'] . "</td>";
                echo "<td>" . $member['website'] . "</td>";
                echo "<td>" . $member['headline'] . "</td>";
                echo "<td>" . $member['start_date'] . "</td>";
                echo "<td>" . $member['end_date'] . "</td>";
                echo "<td>" . $member['status'] . "</td>";
                echo "<td>" . $member['fellow'] . "</td>";
                echo "<td>" . $member['life'] . "</td>";
                echo "<td>" . $member['honorary'] . "</td>";
                echo "<td><a href=\"" . $editUrl . "\">Edit</a></td>";
                echo "</tr>";
            }
        ?>
        </table>
    </div>
    <?php
}

function dvoc_list_members() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $memberId = isset($_GET['member_id']) ? $_GET['member_id'] : '';
    if ($action === 'edit' && $memberId != '') {
        dvoc_edit_member($memberId);
        return;
    }

    $s = isset($_GET['s']) ? $_GET['s'] : '';
    if ($s != '') {
        $results = $wpdb->get_results("SELECT id, first_name, last_name, email, bio, website, headline, start_date, end_date, status, fellow, life, honorary FROM $table_name WHERE first_name LIKE '%$s%' OR last_name like '%$s%'", 'ARRAY_A');
        dvoc_member_display($results);
        return;
    }

    $success = isset($_GET['success']) ? $_GET['success'] : '';
    if ($success === '1') {
    ?>
        <div class="notice notice-success is-dismissible">
            Member added successfully
        </div>
    <?php
    }

    $results = $wpdb->get_results("SELECT id, first_name, last_name, email, bio, website, headline, start_date, end_date, status, fellow, life, honorary FROM $table_name", 'ARRAY_A');
    dvoc_member_display($results);
}

function dvoc_get_member_names($s) {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";

    if ($s != '') {
        $results = $wpdb->get_results("SELECT id, first_name, last_name FROM $table_name WHERE first_name LIKE '%$s%' OR last_name like '%$s%'", 'ARRAY_A');
        return $results;
    }
    return "";

}

function dvoc_edit_member($memberId) {
    global $wpdb;

    $table_name = $wpdb->prefix . "dvoc_members";
    $result = $wpdb->get_row("SELECT id, first_name, last_name, email, bio, website, headline, start_date, end_date, status, fellow, life, honorary FROM $table_name WHERE id = $memberId", 'ARRAY_A');

    ?>
    <div class="wrap">
        <h2>Edit Member</h2>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29">
            <input type="hidden" name="action" value="dvoc_edit_member"/>
            <input type="hidden" name="id" value="<?php echo $result['id']?>"/>
<p>First Name<br>
<span class="wpcf7-form-control-wrap firstName"><input type="text" name="firstName" value="<?php echo $result['first_name']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
<p>Last Name<br>
    <span class="wpcf7-form-control-wrap lastName"><input type="text" name="lastName" value="<?php echo $result['last_name']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Email<br>
    <span class="wpcf7-form-control-wrap email"><input type="email" name="email" value="<?php echo $result['email']?>" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </p>
<p>Headline<br>
    <span class="wpcf7-form-control-wrap headline"><input type="text" name="headline" value="<?php echo $result['headline']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Website<br>
    <span class="wpcf7-form-control-wrap website"><input type="text" name="website" value="<?php echo $result['website']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Start Date<br>
    <span class="wpcf7-form-control-wrap startDate"><input type="text" name="startDate" value="<?php echo $result['start_date']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>End Date<br>
    <span class="wpcf7-form-control-wrap endDate"><input type="text" name="endDate" value="<?php echo $result['end_date']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Status<br>
    <span class="wpcf7-form-control-wrap status"><input type="text" name="status" value="<?php echo $result['status']?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </p>
<p>Bio<br>
    <span class="wpcf7-form-control-wrap bio"><textarea name="bio" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"><?php echo $result['bio']?></textarea></span> </p>
    <p><span class="wpcf7-form-control-wrap life"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="life[]" value="Life Member" <?php if ($result['life'] === '1') { echo "checked=\"checked\""; } ?>>&nbsp;<span class="wpcf7-list-item-label">Life Member</span></span></span></span></p>
<p><span class="wpcf7-form-control-wrap honorary"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="honorary[]" value="Honorary Member" <?php if ($result['honorary'] === '1') { echo "checked=\"checked\""; } ?>>&nbsp;<span class="wpcf7-list-item-label">Honorary Member</span></span></span></span></p>
<p><span class="wpcf7-form-control-wrap fellow"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="fellow[]" value="Fellow" <?php if ($result['fellow'] === '1') { echo "checked=\"checked\""; } ?>>&nbsp;<span class="wpcf7-list-item-label">Fellow</span></span></span></span></p>
<p><input type="submit" value="Update" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
</form>
    </div>
    <?php
}
add_action('admin_post_dvoc_edit_member', 'dvoc_update_member');

function dvoc_update_member() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";

    $arr = dvoc_member_array();
    $arr['id'] = $_POST['id'];

    $result = $wpdb->replace(
        $table_name,
        $arr
    );
    if ($result === 2) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-members&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-add-member&success=$result", 'http'), 301);
    }

}

function dvoc_search_members() {
    global $wpdb;

}

function show_search_box($table) {
    ?>
    <form method="get" action="">
        <input type="hidden" name="page" value="dvoc-list-<?php echo $table; ?>">
        <p class="search-box">
        <label class="screen-reader-text" for="<?php echo $table; ?>_search-search-input">Search <?php echo $table; ?>:</label>
            <input type="search" id="<?php echo $table; ?>_search-search-input" name="s" value="">
            <input type="submit" id="search-submit" class="button" value="Search <?php echo $table; ?>"></p>
    </form>
    <br><br>
    <?php
}
add_action('admin_post_dvoc_list_members', 'dvoc_list_members');

function dvoc_add_committee() {
    $success = $_GET['success'];
    if ($success === 'false') {
    ?>
        <div class="notice notice-error is-dismissible">
            Committee could not be added
        </div>
    <?php
    }
    ?>
    <div class="wrap">
        <h2>Add New Committee</h2>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29">
            <input type="hidden" name="action" value="dvoc_add_committee"/>
            <p>Committee Name<br>
                <span class="wpcf7-form-control-wrap committeeName"><input type="text" name="committeeName" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span>
            </p>
            <p><input type="submit" value="Submit" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
        </form>
    </div>
    <?php
}
add_action('admin_post_dvoc_add_committee', 'dvoc_store_committee');

function dvoc_store_committee() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committees";

    $result = $wpdb->insert(
        $table_name,
        array(
            'committee' => $_POST['committeeName']
        )
    );
    if ($result === 1) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-committees&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-add-committee&success=$result", 'http'), 301);
    }
}

function dvoc_committee_display($results) {
    ?>
    <div class="wrap">
        <h2>DVOC Committees</h2>
        <?php show_search_box('committees'); ?>
        <?php
            foreach($results as $committee) {
                $editUrl = admin_url("admin.php?page=dvoc-list-committees&action=edit&committee_id=" . $committee['id'] , 'http');
                echo "<div>";
                echo "<div><h3>" . $committee['committee'] . "</h3></div>";
                $members = dvoc_list_committee_members($committee['id']);
                echo "<table><tr><th>Member</th><th>Year</th></tr>";
                foreach($members as $member) {
                    echo "<tr><td>" . $member['member_id'] . "</td>";
                    echo "<td>" . $member['year'] . "</td></tr>";
                }
                echo "</table>";
                echo "<div><a href=\"" . $editUrl . "\">Add Member</a></div>";
                echo "</div>";
            }
        ?>
    </div>
    <?php
}

function dvoc_list_committees() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committees";

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $committeeId = isset($_GET['committee_id']) ? $_GET['committee_id'] : '';
    if ($action === 'edit' && $committeeId != '') {
        dvoc_edit_committee($committeeId);
        return;
    }

    $s = isset($_GET['s']) ? $_GET['s'] : '';
    if ($s != '') {
        $results = $wpdb->get_results("SELECT id, committee FROM $table_name WHERE committee LIKE '%$s%'", 'ARRAY_A');
        dvoc_committee_display($results);
        return;
    }

    $success = isset($_GET['success']) ? $_GET['success'] : '';
    if ($success === '1') {
    ?>
        <div class="notice notice-success is-dismissible">
            Committee added successfully
        </div>
    <?php
    }

    $results = $wpdb->get_results("SELECT id, committee FROM $table_name", 'ARRAY_A');
    dvoc_committee_display($results);
}

function dvoc_edit_committee($committeeId) {
    global $wpdb;

    $table_name = $wpdb->prefix . "dvoc_committees";
    $result = $wpdb->get_row("SELECT id, committee FROM $table_name WHERE id = $committeeId", 'ARRAY_A');

    ?>
    <div class="wrap">
        <h2>Add Committee Member</h2>
        <h3><?php echo $result['committee']; ?></h3>
        <p>Member<br>
        <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-member-name" name="name" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
        <div id="dvoc-list-member-names"></div>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29">
            <input type="hidden" name="action" value="dvoc_add_committee_member"/>
            <input type="hidden" name="committeeId" value="<?php echo $result['id']?>"/>
            <input type="hidden" name="memberId" id="dvoc-committe-member" value=""/>
            <span class="wpcf7-form-control-wrap year"><input type="text" name="year" value="<?php echo date("Y"); ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <p><span class="wpcf7-form-control-wrap chairperson"><span class="wpcf7-form-control wpcf7-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="chairperson[]" value="Chairperson"> &nbsp;<span class="wpcf7-list-item-label">Chairperson</span></span></span></span></p>
            <p><input type="submit" value="Add Member" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
        </form>
    </div>
    <?php
}
add_action('admin_post_dvoc_add_committee_member', 'dvoc_add_committee_member');

function dvoc_add_committee_member() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committee_members";

    $result = $wpdb->insert(
        $table_name,
        array(
            'year' => $_POST['year'],
            'committee_id' => $_POST['committeeId'],
            'member_id' => $_POST['memberId'],
            'chairperson' => isset($_POST['chairperson']) ? 1 : 0
        )
    );
    if ($result === 1) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-committees&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-edit-committee&success=$result", 'http'), 301);
    }

}

function dvoc_list_committee_members($committeeId) {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committee_members";

    $results = $wpdb->get_results("SELECT year, committee_id, member_id, chairperson FROM $table_name WHERE committee_id = $committeeId", 'ARRAY_A');
    return $results;
}

add_action('admin_enqueue_scripts', 'dvoc_enqueue');
function dvoc_enqueue($hook) {
    /*if($hook != 'admin.php') {
        // Only applies to dashboard panel
        return;
    }*/

    wp_enqueue_script('ajax-script', plugins_url('/js/functions.js', __FILE__ ), array('jquery'));

    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script('ajax-script', 'ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_ajax_dvoc_search_member', 'dvoc_search_member_callback');
function dvoc_search_member_callback() {
    $results = dvoc_get_member_names($_POST['s']);
    $results = json_encode($results);
    echo $results;
    wp_die();
}

function dvoc_cf7_integrate() {
    require_once('DVOCIntegrationContactForm7.php');
    $integration = new DVOCIntegrationContactForm7();
    $integration->registerHooks();
}

register_activation_hook(__FILE__, 'dvoc_init');
register_deactivation_hook(__FILE__, 'dvoc_uninstall');
