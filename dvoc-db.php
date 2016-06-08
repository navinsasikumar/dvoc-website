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
        id mediumint(6) NOT NULL AUTO_INCREMENT,
        year smallint(4)
        president mediumint(6),
        vice_president mediumint(6),
        secretary mediumint(6),
        treasurer mediumint(6),
        editor mediumint(6),
        council1 mediumint(6),
        council2 mediumint(6),
        council3 mediumint(6),
        council4 mediumint(6),
        council5 mediumint(6),
        council6 mediumint(6),
        UNIQUE KEY id (id)
    ) $charset_collate;";

    dbDelta($sql);
}

function dvoc_create_awards_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_awards";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(6) NOT NULL AUTO_INCREMENT,
        year smallint(4),
        devoc mediumint(6),
        stone mediumint(6),
        edge mediumint(6),
        potter mediumint(6),
        UNIQUE KEY id (id)
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
        id mediumint(6) NOT NULL AUTO_INCREMENT,
	start_date datetime,
	end_date datetime,
        member_id mediumint(6) NOT NULL,
        committee_id smallint(3) NOT NULL,
        chairperson tinyint(1),
        UNIQUE KEY id (id)
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
    add_submenu_page('dvoc-db', 'Officers', 'Officers', 'manage_options', 'dvoc-list-officers', 'dvoc_list_officers');
    add_submenu_page('dvoc-db', 'Add Officers', 'Add Officers', 'manage_options', 'dvoc-edit-officers', 'dvoc_edit_officers');
    add_submenu_page('dvoc-db', 'Awards', 'Awards', 'manage_options', 'dvoc-list-awards', 'dvoc_list_awards');
    add_submenu_page('dvoc-db', 'Add Awards', 'Add Awards', 'manage_options', 'dvoc-edit-awards', 'dvoc_edit_awards');
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

function dvoc_assemble_member_name($member) {
    $name = '';
    if (isset($member['first_name'])) {
        $name .= $member['first_name'] . ' ';
    }
    if (isset($member['last_name'])) {
        $name .= $member['last_name'] . ' ';
    }
    return $name;
}

function dvoc_get_member_from_id($memberId) {
    if (!$memberId) {
        return '';
    }

    global $wpdb;

    $table_name = $wpdb->prefix . "dvoc_members";
    $result = $wpdb->get_row("SELECT id, first_name, last_name, email, bio, website, headline, start_date, end_date, status, fellow, life, honorary FROM $table_name WHERE id = $memberId", 'ARRAY_A');
    return $result;
}

function dvoc_edit_member($memberId) {
    $result = dvoc_get_member_from_id($memberId);
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
                echo "<table><tr><th>Member</th><th>Start Date</th><th>End Date</th></tr>";
                foreach($members as $member) {
                    echo "<tr><td>" . $member['first_name'] . " " . $member['last_name']  . "</td>";
                    echo "<td>" . $member['start_date'] . "</td>";
                    echo "<td>" . $member['end_date'] . "</td></tr>";
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
            <input type="hidden" name="memberId" id="dvoc-member-id" value=""/>
            <span class="wpcf7-form-control-wrap startDate"><input type="text" name="startDate" value="<?php echo date("Y-m-d"); ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <span class="wpcf7-form-control-wrap endDate"><input type="text" name="endDate" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
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
            'start_date' => $_POST['startDate'],
            'end_date' => isset($_POST['endDate']) ? $_POST['endDate'] : null,
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
    $table_name_2 = $wpdb->prefix . "dvoc_members";

    $query = "SELECT a.start_date, a.end_date, a.committee_id, a.member_id, a.chairperson, b.first_name, b.last_name FROM $table_name AS a JOIN $table_name_2 AS b ON a.member_id = b.id WHERE a.committee_id = $committeeId";
    $results = $wpdb->get_results($query, 'ARRAY_A');
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

function dvoc_list_member_committees($memberId) {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_committee_members";
    $table_name_2 = $wpdb->prefix . "dvoc_committees";

    $query = "SELECT a.start_date, a.end_date, a.committee_id, a.member_id, a.chairperson, b.committee FROM $table_name AS a JOIN $table_name_2 AS b ON a.committee_id = b.id WHERE a.member_id = $memberId";
    $results = $wpdb->get_results($query, 'ARRAY_A');
    return $results;
}

function dvoc_get_all_member_info($firstName, $lastName) {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_members";

    $result = $wpdb->get_row("SELECT id, first_name, last_name, email, bio, website, headline, start_date, end_date, status, fellow, life, honorary FROM $table_name WHERE first_name = '$firstName' AND last_name = '$lastName'", 'ARRAY_A');
    $committees = dvoc_list_member_committees($result['id']);
    $result['committees'] = $committees;

    return $result;
}

function dvoc_display_member_committees($member) {
    $ret = "";
    foreach($member['committees'] as $committee) {
        $ret .= $committee['committee'] . "<br>";
    }
    return $ret;
}

function dvoc_display_member_since($member) {
    if ($member['status'] === 'active' && $member['start_date'] !== '0000-00-00 00:00:00') {
        $displayDate = date('F d, Y', strtotime($member['start_date']));
        return 'Member since: ' . $displayDate;
    } else {
        return '';
    }
}

function dvoc_shortcode_member_info($atts) {
    $args = shortcode_atts(
        array(
            'name' => 'none',
            'field' => 'none'
        ),
        $atts
    );

    if ($args['name'] === 'none') {
        $page = get_permalink();
        $pos = strpos($page, 'membership/members/');
        if ($pos >= 0) {
            $startPos = $pos + 19;
            $endNamePos = strpos($page, '/', $startPos);
            $name = substr($page, $startPos, $endNamePos - $startPos);
            list($firstName, $lastName) = explode('-', $name);
        } else {
            return '';
        }
    } else {
        list($firstName, $lastName) = explode(' ', $args['name']);
    }

    $result = dvoc_get_all_member_info($firstName, $lastName);

    if ($args['field'] === 'none') {
        return json_encode($result);
    } else if ($args['field'] === 'committees') {
        return dvoc_display_member_committees($result);
    } else if ($args['field'] === 'member-since') {
        return dvoc_display_member_since($result);
    } else if ($args['field'] === 'life' && $result['life']) {
        return 'Life Member';
    } else if ($args['field'] === 'honorary' && $result['honorary']) {
        return 'Honorary Member';
    } else if ($args['field'] === 'fellow' && $result['fellow']) {
        return 'Fellow of the DVOC';
    }

    $ret = nl2br(stripslashes($result[$args['field']]));
    $ret = make_clickable($ret);
    return $ret;
}

add_shortcode('dvoc-member-info', 'dvoc_shortcode_member_info');

function dvoc_officers_display($results) {
    ?>
    <div class="wrap">
        <h2>DVOC Officers</h2>
        <?php show_search_box('officers'); ?>
        <?php
            echo "<table>";
            echo "<tr>";
            echo "<th>Id</th>";
            echo "<th>Year</th>";
            echo "<th>President</th>";
            echo "<th>Vice President</th>";
            echo "<th>Secretary</th>";
            echo "<th>Treasurer</th>";
            echo "<th>Editor</th>";
            echo "<th>Councillor 1</th>";
            echo "<th>Councillor 2</th>";
            echo "<th>Councillor 3</th>";
            echo "<th>Councillor 4</th>";
            echo "<th>Councillor 5</th>";
            echo "<th>Councillor 6</th>";
            echo "<th>Edit</th></tr>";
            foreach($results as $officer) {
                $president = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['president']));
		$vicePresident = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['vice_president']));
		$secretary = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['secretary']));
		$treasurer = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['treasurer']));
		$editor = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['editor']));
		$council1 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council1']));
		$council2 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council2']));
		$council3 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council3']));
		$council4 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council4']));
		$council5 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council5']));
		$council6 = dvoc_assemble_member_name(dvoc_get_member_from_id($officer['council6']));

                $editUrl = admin_url("admin.php?page=dvoc-edit-officers&action=edit&officer_id=" . $officer['id'] , 'http');
                echo "<tr>";
                echo "<td>" . $officer['id'] . "</td>";
                echo "<td>" . $officer['year'] . "</td>";
                echo "<td>" . $president . "</td>";
                echo "<td>" . $vicePresident . "</td>";
                echo "<td>" . $secretary . "</td>";
                echo "<td>" . $treasurer . "</td>";
                echo "<td>" . $editor . "</td>";
                echo "<td>" . $council1 . "</td>";
                echo "<td>" . $council2 . "</td>";
                echo "<td>" . $council3 . "</td>";
                echo "<td>" . $council4 . "</td>";
                echo "<td>" . $council5 . "</td>";
                echo "<td>" . $council6 . "</td>";
                echo "<td><a href=\"" . $editUrl . "\">Edit</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        ?>
    </div>
    <?php
}

function dvoc_list_officers() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_officers";

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $officerId = isset($_GET['officer_id']) ? $_GET['officer_id'] : '';
    if ($action === 'edit' && $committeeId != '') {
        dvoc_edit_officers($officerId);
        return;
    }

    /*$s = isset($_GET['s']) ? $_GET['s'] : '';
    if ($s != '') {
        $results = $wpdb->get_results("SELECT id, committee FROM $table_name WHERE committee LIKE '%$s%'", 'ARRAY_A');
        dvoc_officers_display($results);
        return;
    }*/

    $success = isset($_GET['success']) ? $_GET['success'] : '';
    if ($success === '1') {
    ?>
        <div class="notice notice-success is-dismissible">
            Officer added successfully
        </div>
    <?php
    }

    $results = $wpdb->get_results("SELECT id, year, president, vice_president, secretary, treasurer, editor, council1, council2, council3, council4, council5, council6 FROM $table_name", 'ARRAY_A');
    dvoc_officers_display($results);
}

function dvoc_edit_officers($officerId) {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $officerId = isset($_GET['officer_id']) ? $_GET['officer_id'] : '';

    $memberId = '';
    $name = '';
    $year = date('Y');
    $endDate = ''; $president = 0; $vicePresident = 0; $secretary = 0; $treasurer = 0; $editor = 0; $council = 0;
    if ($action === 'edit' && $officerId !== '') {
        global $wpdb;
        $table_name = $wpdb->prefix . "dvoc_officers";
        $table_name_2 = $wpdb->prefix . "dvoc_members";

        $result = $wpdb->get_row("SELECT a.id, a.year, a.president, a.vice_president, a.secretary, a.treasurer, a.editor, a.council1, a.council2, a.council3, a.council4, a.council5, a.council6, b.id AS member_id, b.first_name, b.last_name FROM $table_name a JOIN $table_name_2 b ON (a.president = b.id OR a.vice_president = b.id OR a.secretary = b.id OR a.treasurer = b.id OR a.editor = b.id OR a.council = b.id) WHERE a.id = $officerId", 'ARRAY_A');

        $year = $result['year'];

        $officers = array(
	    'president' => array(
                'id' => $result['president']
            ),
	    'vicePresident' => array(
                'id' => $result['vice_president']
            ),
	    'secretary' => array(
                'id' => $result['secretary']
            ),
	    'treasurer' => array(
                'id' => $result['treasurer']
            ),
	    'editor' => array(
                'id' => $result['editor']
            ),
	    'council1' => array(
                'id' => $result['council1']
            ),
	    'council2' => array(
                'id' => $result['council2']
            ),
	    'council3' => array(
                'id' => $result['council3']
            ),
	    'council4' => array(
                'id' => $result['council4']
            ),
	    'council5' => array(
                'id' => $result['council5']
            ),
	    'council6' => array(
                'id' => $result['council6']
            )
        );
        foreach ($officers as $key => $value) {
            if (!$officers[$key]['id']) {
                $officers[$key]['name'] = '';
            } else {
                $memberResult = $wpdb->get_row("SELECT first_name, last_name FROM $table_name_2 WHERE id = " . $officers[$key]['id'], 'ARRAY_A');
                $officers[$key]['name'] = $memberResult['first_name'] . ' ' . $memberResult['last_name'];
            }
        }

    }


    ?>
    <div class="wrap">
        <h2>Add / Edit Officer</h2>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29" id="officer-form">
            <input type="hidden" name="action" value="dvoc_add_officer"/>
            <input type="hidden" name="id" id="dvoc-officer-id" value="<?php echo $officerId; ?>"/>

            <p>Year<br>
            <span class="wpcf7-form-control-wrap year"><input type="text" name="year" value="<?php echo $year; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>

	    <p>President<br>
            <input type="hidden" name="presidentId" id="dvoc-president-id" value="<?php echo $officers['president']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-president-name" name="president" value="<?php echo $officers['president']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-president-names"></div>

	    <p>Vice President<br>
            <input type="hidden" name="vicePresidentId" id="dvoc-vice-president-id" value="<?php echo $officers['vicePresident']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-vice-president-name" name="vice-president" value="<?php echo $officers['vicePresident']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-vice-president-names"></div>

            <p>Secretary<br>
            <input type="hidden" name="secretaryId" id="dvoc-secretary-id" value="<?php echo $officers['secretary']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-secretary-name" name="secretary" value="<?php echo $officers['secretary']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-secretary-names"></div>

            <p>Treasurer<br>
            <input type="hidden" name="treasurerId" id="dvoc-treasurer-id" value="<?php echo $officers['treasurer']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-treasurer-name" name="treasurer" value="<?php echo $officers['treasurer']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-treasurer-names"></div>

	    <p>Editor<br>
            <input type="hidden" name="editorId" id="dvoc-editor-id" value="<?php echo $officers['editor']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-editor-name" name="editor" value="<?php echo $officers['editor']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-editor-names"></div>

	    <p>Councillor 1<br>
            <input type="hidden" name="council1Id" id="dvoc-council1-id" value="<?php echo $officers['council1']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council1-name" name="council1" value="<?php echo $officers['council1']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council1-names"></div>

	    <p>Councillor 2<br>
            <input type="hidden" name="council2Id" id="dvoc-council2-id" value="<?php echo $officers['council2']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council2-name" name="council2" value="<?php echo $officers['council2']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council2-names"></div>

	    <p>Councillor 3<br>
            <input type="hidden" name="council3Id" id="dvoc-council3-id" value="<?php echo $officers['council3']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council3-name" name="council3" value="<?php echo $officers['council3']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council3-names"></div>

	    <p>Councillor 4<br>
            <input type="hidden" name="council4Id" id="dvoc-council4-id" value="<?php echo $officers['council4']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council4-name" name="council4" value="<?php echo $officers['council4']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council4-names"></div>

	    <p>Councillor 5<br>
            <input type="hidden" name="council5Id" id="dvoc-council5-id" value="<?php echo $officers['council5']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council5-name" name="council5" value="<?php echo $officers['council5']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council5-names"></div>

	    <p>Councillor 6<br>
            <input type="hidden" name="council6Id" id="dvoc-council6-id" value="<?php echo $officers['council6']['id']; ?>"/>
	    <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-council6-name" name="council6" value="<?php echo $officers['council6']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
	    <div id="dvoc-list-council6-names"></div>

            <p><input type="submit" value="Add Officer" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
        </form>
    </div>
    <?php
}
add_action('admin_post_dvoc_add_officer', 'dvoc_add_officer');

function dvoc_add_officer() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_officers";

    $arr = array(
        'year' => $_POST['year'],
        'president' => isset($_POST['presidentId']) ? $_POST['presidentId'] : null,
        'vice_president' => isset($_POST['vicePresidentId']) ? $_POST['vicePresidentId'] : null,
        'secretary' => isset($_POST['secretaryId']) ? $_POST['secretaryId'] : null,
        'treasurer' => isset($_POST['treasurerId']) ? $_POST['treasurerId'] : null,
        'editor' => isset($_POST['editorId']) ? $_POST['editorId'] : null,
        'council1' => isset($_POST['council1Id']) ? $_POST['council1Id'] : null,
        'council2' => isset($_POST['council2Id']) ? $_POST['council2Id'] : null,
        'council3' => isset($_POST['council3Id']) ? $_POST['council3Id'] : null,
        'council4' => isset($_POST['council4Id']) ? $_POST['council4Id'] : null,
        'council5' => isset($_POST['council5Id']) ? $_POST['council5Id'] : null,
        'council6' => isset($_POST['council6Id']) ? $_POST['council6Id'] : null
    );
    if (isset($_POST['id'])) {
        $arr['id'] = $_POST['id'];
    }

    $result = $wpdb->replace(
        $table_name,
        $arr
    );
    if ($result === 1 || $result === 2) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-officers&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-edit-officers&success=$result", 'http'), 301);
    }

}

function dvoc_awards_display($results) {
    ?>
    <div class="wrap">
        <h2>DVOC Awards</h2>
        <?php show_search_box('awards'); ?>
        <?php
            echo "<table>";
            echo "<tr>";
            echo "<th>Id</th>";
            echo "<th>Year</th>";
            echo "<th>DEVOC</th>";
            echo "<th>Witmer Stone</th>";
            echo "<th>Julian Potter</th>";
            echo "<th>Rosalie Edge</th>";
            echo "<th>Edit</th></tr>";
            foreach($results as $award) {
                $devoc = dvoc_assemble_member_name(dvoc_get_member_from_id($award['devoc']));
                $stone = dvoc_assemble_member_name(dvoc_get_member_from_id($award['stone']));
                $potter = dvoc_assemble_member_name(dvoc_get_member_from_id($award['potter']));
                $edge = dvoc_assemble_member_name(dvoc_get_member_from_id($award['edge']));

                $editUrl = admin_url("admin.php?page=dvoc-edit-awards&action=edit&award_id=" . $award['id'] , 'http');
                echo "<tr>";
                echo "<td>" . $award['id'] . "</td>";
                echo "<td>" . $award['year'] . "</td>";
                echo "<td>" . $devoc . "</td>";
                echo "<td>" . $stone . "</td>";
                echo "<td>" . $potter . "</td>";
                echo "<td>" . $edge. "</td>";
                echo "<td><a href=\"" . $editUrl . "\">Edit</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        ?>
    </div>
    <?php
}

function dvoc_list_awards() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_awards";

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $awardId = isset($_GET['award_id']) ? $_GET['award_id'] : '';
    if ($action === 'edit' && $awardId != '') {
        dvoc_edit_awards($awardId);
        return;
    }

    /*$s = isset($_GET['s']) ? $_GET['s'] : '';
    if ($s != '') {
        $results = $wpdb->get_results("SELECT id, committee FROM $table_name WHERE committee LIKE '%$s%'", 'ARRAY_A');
        dvoc_awards_display($results);
        return;
    }*/

    $success = isset($_GET['success']) ? $_GET['success'] : '';
    if ($success === '1') {
    ?>
        <div class="notice notice-success is-dismissible">
            Award added successfully
        </div>
    <?php
    }

    $results = $wpdb->get_results("SELECT id, year, devoc, stone, potter, edge FROM $table_name", 'ARRAY_A');
    dvoc_awards_display($results);
}

function dvoc_edit_awards($awardId) {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $awardId = isset($_GET['award_id']) ? $_GET['award_id'] : '';

    $memberId = '';
    $name = '';
    $year = date('Y');
    $devoc = 0; $vicePresident = 0; $secretary = 0; $treasurer = 0; $editor = 0; $council = 0;
    if ($action === 'edit' && $awardId !== '') {
        global $wpdb;
        $table_name = $wpdb->prefix . "dvoc_awards";
        $table_name_2 = $wpdb->prefix . "dvoc_members";

        $result = $wpdb->get_row("SELECT id, year, devoc, stone, potter, edge FROM $table_name WHERE id = $awardId", 'ARRAY_A');

        $year = $result['year'];

        $awards = array(
            'devoc' => array(
                'id' => $result['devoc']
            ),
            'stone' => array(
                'id' => $result['stone']
            ),
            'potter' => array(
                'id' => $result['potter']
            ),
            'edge' => array(
                'id' => $result['edge']
            )
        );
        foreach ($awards as $key => $value) {
            if (!$awards[$key]['id']) {
                $awards[$key]['name'] = '';
            } else {
                $memberResult = $wpdb->get_row("SELECT first_name, last_name FROM $table_name_2 WHERE id = " . $awards[$key]['id'], 'ARRAY_A');
                $awards[$key]['name'] = $memberResult['first_name'] . ' ' . $memberResult['last_name'];
            }
        }

    }


    ?>
    <div class="wrap">
        <h2>Add / Edit Award</h2>
        <form action="<?php echo admin_url('admin-post.php');?>" method="post" class="wpcf7-form mailchimp-ext-0.4.29" id="award-form">
            <input type="hidden" name="action" value="dvoc_add_award"/>
            <input type="hidden" name="id" id="dvoc-award-id" value="<?php echo $awardId; ?>"/>

            <p>Year<br>
            <span class="wpcf7-form-control-wrap year"><input type="text" name="year" value="<?php echo $year; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>

            <p>DEVOC<br>
            <input type="hidden" name="devocId" id="dvoc-devoc-id" value="<?php echo $awards['devoc']['id']; ?>"/>
            <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-devoc-name" name="devoc" value="<?php echo $awards['devoc']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <div id="dvoc-list-devoc-names"></div>

            <p>Witmer Stone<br>
            <input type="hidden" name="stoneId" id="dvoc-stone-id" value="<?php echo $awards['stone']['id']; ?>"/>
            <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-stone-name" name="stone" value="<?php echo $awards['stone']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <div id="dvoc-list-stone-names"></div>

            <p>Julian K. Potter<br>
            <input type="hidden" name="potterId" id="dvoc-potter-id" value="<?php echo $awards['potter']['id']; ?>"/>
            <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-potter-name" name="potter" value="<?php echo $awards['potter']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <div id="dvoc-list-potter-names"></div>

            <p>Rosalie Edge<br>
            <input type="hidden" name="edgeId" id="dvoc-edge-id" value="<?php echo $awards['edge']['id']; ?>"/>
            <span class="wpcf7-form-control-wrap name"><input type="text" id="dvoc-edge-name" name="edge" value="<?php echo $awards['edge']['name']; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false" style="cursor: auto; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; background-repeat: no-repeat;"></span> </p>
            <div id="dvoc-list-edge-names"></div>

            <p><input type="submit" value="Add Award" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dvoc.org/wp/wp-content/plugins/contact-form-7-mailchimp-extension/assets/images/fading-squares.gif" alt="Sending ..." style="visibility: hidden;"></p>
        </form>
    </div>
    <?php
}
add_action('admin_post_dvoc_add_award', 'dvoc_add_award');

function dvoc_add_award() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dvoc_awards";

    $arr = array(
        'year' => $_POST['year'],
        'devoc' => isset($_POST['devocId']) ? $_POST['devocId'] : null,
        'stone' => isset($_POST['stoneId']) ? $_POST['stoneId'] : null,
        'potter' => isset($_POST['potterId']) ? $_POST['potterId'] : null,
        'edge' => isset($_POST['edgeId']) ? $_POST['edgeId'] : null,
    );
    if (isset($_POST['id'])) {
        $arr['id'] = $_POST['id'];
    }

    $result = $wpdb->replace(
        $table_name,
        $arr
    );
    if ($result === 1 || $result === 2) {
        wp_redirect(admin_url("admin.php?page=dvoc-list-awards&success=$result", 'http'), 301);
    } else {
        wp_redirect(admin_url("admin.php?page=dvoc-edit-awards&success=$result", 'http'), 301);
    }

}
function dvoc_cf7_integrate() {
    require_once('DVOCIntegrationContactForm7.php');
    $integration = new DVOCIntegrationContactForm7();
    $integration->registerHooks();
}

register_activation_hook(__FILE__, 'dvoc_init');
//register_deactivation_hook(__FILE__, 'dvoc_uninstall');
