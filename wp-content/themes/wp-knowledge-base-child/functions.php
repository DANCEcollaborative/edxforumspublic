<?php //Opening PHP tag

add_action('after_setup_theme', 'remove_admin_bar');



function remove_admin_bar() {

	if (!current_user_can('administrator') && !is_admin()) {

 	 show_admin_bar(false);

	}

}



// Colin's changes
/*
if( !function_exists('dwqa_wp_knowledge_base_scripts') ){

    function dwqa_wp_knowledge_base_scripts(){

        wp_enqueue_style( 'dw-wp-knowledge-base-qa', get_stylesheet_directory_uri() . '/dwqa-templates/style.css' );

    }

 add_action( 'wp_enqueue_scripts', 'dwqa_wp_knowledge_base_scripts' );

}
*/

// og google analytics
//function og_add_google_analytics(){
//        global $ipt_kb_version;
//        wp_enqueue_script( 'edtechxanalytics.js', get_template_directory_uri() . '/js/edtechxanalytics.js' );
//}
//add_action( 'wp_enqueue_scripts', 'og_add_google_analytics' );
// end og

// og http to https
function og_http_to_https(){
        global $ipt_kb_version;
        wp_enqueue_script( 'edtechxhttphttps.js', get_template_directory_uri() . '/js/edtechxhttphttps.js' );
}
add_action( 'wp_enqueue_scripts', 'og_http_to_https' );
// end og

function kbc_forum_folder_desc() {
  $content = bbp_get_forum_content();
  if($content != '') {
     echo '<div id="desc-box">';
     echo $content;
     echo '</div>';
  }
}
add_action('bbp_template_before_single_forum', 'kbc_forum_folder_desc');
 
function kbc_bp_groups_message() {
  $descTxt = "Join an existing group or create a new group to participate in group discussions. Groups administrators have control over who can join and participate in the Groups forums.";
  echo '<div id="desc-box">' . $descTxt . '</div>';
}
add_action('bp_before_directory_groups_content', 'kbc_bp_groups_message', 1);
 
function kbc_bp_members_message() {
  $descTxt = "Find and connect with members who are using these forums.";
  echo '<div id="desc-box">' . $descTxt . '</div>';
}
add_action('bp_before_directory_members_page', 'kbc_bp_members_message', 1);

function kbc_remove_feature_requests()
{
  remove_action('bbp_template_before_topics_loop', 'dtbaker_vote_bbp_template_before_topics_loop');
}
add_action('after_setup_theme', 'kbc_remove_feature_requests');

function kbc_edx_login_redirect() {
  if(!strpos($_SERVER["REQUEST_URI"], 'admin-login')) {
    //http://stackoverflow.com/questions/7921229/how-do-i-read-values-from-wp-config-php
    // The edX url is set in the config file wp-config-basics.php 
    header("Location: ".MY_EDX_URL);
    exit;
  }
}
add_action('login_enqueue_scripts', 'kbc_edx_login_redirect');

/* Fix issue with Support Forums plugin
 * - can only see subscriptions to topics you start 
 */
function kbc_remove_author_lock() {
  remove_filter('bbp_has_topics_query','bbps_lock_to_author');
}
add_action('bbp_template_before_user_subscriptions', 'kbc_remove_author_lock');

function kbc_re_add_author_lock() {
  add_filter('bbp_has_topics_query','bbps_lock_to_author');
}
add_action('bbp_template_after_user_subscriptions', 'kbc_re_add_author_lock');

/* Remove forced sorting by votes on voting forums, to allow also sorting by date or replies */
remove_filter('bbp_after_has_topics_parse_args','bbps_filter_bbp_after_has_topics_parse_args',10);

/* If the list of topics is sorted, make sure the paginated links keep it sorted */
function kbc_pagination_links ($links) {
  $order_index = strpos($_SERVER['REQUEST_URI'], '?order');
  if($order_index !== false) {
    $uri_query = substr($_SERVER['REQUEST_URI'], $order_index);
    return $links.$uri_query;
  } else {
    return $links;
  }
}
add_filter('paginate_links', 'kbc_pagination_links', 100, 1);

// End Colin's changes


// Taken from http://wordpress.stackexchange.com/questions/74742/how-to-set-different-cookies-for-logged-in-admin-users-and-logge$
function set_admin_specific_cookie($user_login, $user, $userroles=NULL){

        //if(current_user_can('administrator')){
	// I get the role from $user instead of using current_user_can, because current_user_can may not be set yet
	// I actually tried current_user_can and it didn't seem to work here
	error_log(print_r($user,true));
	error_log(print_r($user->roles,true));
	if(!is_null($userroles)){
		error_log(print_r($userroles,true));
		error_log("is moderator". array_key_exists('bbp_moderator',$userroles));
	}
	else{
		error_log("user roles is null");
	}
	//if(!isset($_COOKIE['disable_my_cache'])){
	//}
	// http://wordpress.stackexchange.com/questions/43528/how-to-get-a-buddypress-user-profile-link-and-a-certain-user-profile-field-for-t
	error_log("domain:".bp_core_get_user_domain( $user->ID ));
	//if(!isset($_COOKIE['uname'])){
	setcookie('uname',$user->user_login,0,'/');
	//}
	// $userroles gets passed in from LTI because $user->roles is often not set yet when this function is called from LTI. 
	// $user->roles is properly set when this function is called from admin-login
	$my_uroles = $userroles;
	if(is_null($my_uroles)){
		$my_uroles = $user->roles;
	} 
	if(user_can($user,'administrator')||(array_key_exists('administrator',$my_uroles))||(lti_site_admin())||(array_key_exists('bbp_moderator',$my_uroles))||(array_key_exists('bbp_keymaster',$my_uroles))||(array_key_exists('bbp_blocked',$my_uroles))){
		error_log("oritgigo admin or bbp moderator");
		error_log(var_dump($_COOKIE));
                if(!isset($_COOKIE['disable_my_cache'])){
			error_log("oritgigo empty cookie");
                        setcookie('disable_my_cache',1,0,'/');
                }
        }
	else{
		// If the user is not an admin and the disable_my_cache cookie is there, remove it!
		if(isset($_COOKIE['disable_my_cache'])){
			error_log("The user is not an admin, oritgigo clear existing cookie: disable_my_cache ".is_admin());
			setcookie('disable_my_cache',0, time()-3600,'/');
                	unset($_COOKIE['disable_my_cache']);
		}
	}
}

function clear_admin_specific_cookie(){
        error_log("oritgigo clear cache cookie");
        if(isset($_COOKIE['uname'])){
                setcookie('uname',$user->ID,time()-3600,'/');
		unset($_COOKIE['uname']);
        }
        if(isset($_COOKIE['disable_my_cache'])){
             	error_log("oritgigo clear existing cookie");
		//http://www.w3schools.com/php/php_cookies.asp
		setcookie('disable_my_cache',0, time()-3600,'/');
             	unset($_COOKIE['disable_my_cache']);
        }
}

//function set_bpdomain_cookie(){
//	error_log("domain:".bp_loggedin_user_domain( '/' ));
//}

error_log("oritgigo here");

// see http://codex.wordpress.org/Plugin_API/Action_Reference/wp_login
// the last two arguments (10 and 2) enable set_admin_specific_cookie to get the use arguments
add_action('wp_login', 'set_admin_specific_cookie', 10,2);
add_action('wp_og_LTI_login', 'set_admin_specific_cookie', 10,3);
add_action('wp_logout', 'clear_admin_specific_cookie');
//add_action('bp_loaded', 'set_bpdomain_cookie');

function my_disable_page_cache($mypageurl){
	setrawcookie('disable_my_page_cache',$mypageurl, time()+65, $mypageurl);
}

function my_reply_update_handler(){
	//error_log("og reply id:".$_POST['bbp_reply_id']);
	//error_log("og redirect:".bbp_get_redirect_to());
	error_log("page uri:".$_SERVER['REQUEST_URI']);
	//$myreplyid = (int) $_POST['bbp_reply_id'];
	//$myredirect = bbp_get_redirect_to();
	//$myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
	//$myreplyurlpart=preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl));
	//error_log("og reply url:".home_url());
	//error_log("og reply uri:".$myreplyurlpart);
	//if(!isset($_COOKIE['disable_my_page_cache'])){
	//setrawcookie('disable_my_page_cache',$_SERVER['REQUEST_URI'], time()+180);
	// Temporarily (until the cache times out) disable the cache for this page for this user,
	// since they just poste and we want them to be able to see their posts. 
	my_disable_page_cache($_SERVER['REQUEST_URI']);
	error_log("page uri:".$_SERVER['REQUEST_URI']);
	//}
}

function my_reply_edit_handler(){
	//error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        //error_log("page uri:".$_SERVER['REQUEST_URI']);
	// Edit replies do not have the url that we don't want to cache (unlike new posts). 
	// In order to disable the cache for the editing user for this topic, we need to obtain
	// its url
	// Get the post id
        $myreplyid = (int) $_POST['bbp_reply_id'];
	// Get the full redirect url
        $myredirect = bbp_get_redirect_to();
        $myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
	// Get rid of the extras (such as the domain name and the #post123)
        $myreplyurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl)));
        //error_log("og reply url:".home_url());
        error_log("og reply uri:".$myreplyurlpart);
	my_disable_page_cache($myreplyurlpart);
	error_log("og reply uri:".$myreplyurlpart);
}
function my_new_topic_handler(){
        //error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        error_log("page uri:".$_SERVER['REQUEST_URI']);
        //$myreplyid = (int) $_POST['bbp_reply_id'];
        //$myredirect = bbp_get_redirect_to();
        //$myreplyurl = bbp_get_reply_url($myreplyid,$myredirect);
        //$myreplyurlpart=preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl));
        //error_log("og reply url:".home_url());
        //error_log("og reply uri:".$myreplyurlpart);
        //if(!isset($_COOKIE['disable_my_page_cache'])){
        //setrawcookie('disable_my_page_cache',$_SERVER['REQUEST_URI'], time()+180);
        // Temporarily (until the cache times out) disable the cache for this page for this user,
        // since they just poste and we want them to be able to see their posts. 
        my_disable_page_cache($_SERVER['REQUEST_URI']);
        error_log("page uri:".$_SERVER['REQUEST_URI']);
        //}
}

function my_topic_edit_handler(){
        //error_log("og reply id:".$_POST['bbp_reply_id']);
        //error_log("og redirect:".bbp_get_redirect_to());
        //error_log("page uri:".$_SERVER['REQUEST_URI']);
        // Edit replies do not have the url that we don't want to cache (unlike new posts). 
        // In order to disable the cache for the editing user for this topic, we need to obtain
        // its url
        // Get the post id
        $mytopicid = (int) $_POST['bbp_topic_id'];
        // Get the full redirect url
        $myredirect = bbp_get_redirect_to();
        $myreplyurl = bbp_get_topic_permalink($mytopicid);
	$myforumid = bbp_get_topic_forum_id($mytopicid);
	error_log("topic url: ".$myreplyurl." og topic id: ".$_POST['bbp_topic_id']." og forumid: ".$myforumid);

        // Get rid of the extras (such as the domain name and the #post123)
        $myreplyurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myreplyurl)));
        $myforumurl = bbp_get_forum_permalink($myforumid);
	$myforumurlpart=trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",$myforumurl)));
	//error_log("og reply url:".home_url());
	// Don't cache the topic so that the user can see their updated content and title
        error_log("og topic uri:".$myreplyurlpart);
        my_disable_page_cache($myreplyurlpart);
        error_log("og topic uri:".$myreplyurlpart);
	error_log("og forum uri:".$myforumurlpart);
	// Don't cache the forum so that the user can see their updated title
	my_disable_page_cache($myreplyurlpart);
}

// Create a cookie when the user posts a reply. This is done in order to prevent the cache from getting a cached version of the page for this specific user.
add_action('bbp_edit_reply', 'my_reply_edit_handler');
add_action('bbp_new_reply', 'my_reply_update_handler');
add_action('bbp_new_topic', 'my_new_topic_handler');
add_action('bbp_edit_topic', 'my_topic_edit_handler');

function my_group_creation_handler(){
	// Create a cookie that will tell varnish not to cache the group directory for this user for the next x minutes, so the user can see the group they just created.
	error_log("group url:".bp_get_groups_directory_permalink());
	$mygroupdir = trim(preg_replace("/([^\/]+$)/","",preg_replace("/^https?:\/\/[^\/]+(\/*.+\/).*/", " $1 ",bp_get_groups_directory_permalink())));
	error_log("group uri:".$mygroupdir);
	setrawcookie('disable_my_page_cache',$mygroupdir, time()+65, $mygroupdir);
}
add_action('groups_group_create_complete', 'my_group_creation_handler');


// Parent override
// For more information see http://www.paulund.co.uk/override-parent-theme-functions
function ipt_kb_bbp_forum_freshness_in_list( $forum_id = 0 ) {
        $og_forum_last_topic_id = bbp_get_forum_last_topic_id($forum_id);
        $og_last_topic_id = bbp_get_topic_last_active_id($og_forum_last_topic_id);
        $author_link = bbp_get_author_link( array(
                'post_id' => $og_last_topic_id,
                'type' => 'name'
        ) );
        $freshness = bbp_get_author_link( array( 'post_id' => $og_last_topic_id, 'size' => 32, 'type' => 'avatar' ) );
        ?>
<?php if ( ! empty( $freshness ) ) : ?>
<span class="pull-left thumbnail">
        <?php echo $freshness; ?>
</span>
<?php endif; ?>
<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>
<ul class="list-unstyled ipt_kb_forum_freshness_meta">
        <li class="bbp-topic-freshness-link"><?php echo bbp_topic_freshness_link($og_forum_last_topic_id); ?>  </li>
        <li class="bbp-topic-freshness-author">
                <?php do_action( 'bbp_theme_before_topic_author' ); ?>
                <?php if ( ! empty( $author_link ) ) printf( __( 'by %s', 'ipt_kb' ), $author_link ); ?>
                <?php do_action( 'bbp_theme_after_topic_author' ); ?>
        </li>
</ul>
<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
        <?php
}


// https://buddypress.org/support/topic/removing-menus-not-working-in-buddypress-1-5-help-please/
//function ja_remove_navigation_tabs() {
//	global $bp;
//	//remove_action('groups_custom_create_steps', array( $this, 'maybe_create_screen' ));
// 	$bp->groups->group_creation_steps['forum']=null;
	//bp_core_remove_subnav_item(buddypress()->groups->group_creation_steps->slug,'forum');
//	bp_core_remove_subnav_item( $bp->groups->slug, 'forum' );
//}
//add_action( 'groups_custom_create_steps', 'ja_remove_navigation_tabs', 25 );

// When the group has a forum, set the group page's default tab to 'forum'. For groups that don't have a forum, the 'home' tab will be the default.
// Some of this code was copied from https://buddypress.org/support/topic/bp_groups_default_extension/
function bbg_set_group_default_extension( $ext ) {
	global $bp;
	error_log("og extension".print_r($ext,true));
	error_log("og extension2".print_r($bp->groups->current_group,true));
	error_log("og extension3".print_r($bp->active_components,true)." test");
	//if ( $bp->groups->current_group->enable_forum && bp_is_active( 'forums' ) ){
	// Mystery: based on bp_is_active, forums are disabled, but the group menu is still displayed
	if ( $bp->groups->current_group->enable_forum){
		error_log("og extension forums");
		return 'forum';
	}
	else{
		return $ext;
	}
}
add_filter( 'bp_groups_default_extension', 'bbg_set_group_default_extension' );

# Breadcrumbs
# This function channges the "Forums" breadcrumb link for non admin users in order to avoid confusion. When a non-admin user sees a link named "forums", they assume that it
# points to the root of the forums (the home page). The default "Forums" breadcrumb points to another page that lists all the forums. This page is very useful for admins,
# but it generally confuses non-admin users. As a result, we decided to make this page available for admins only and have non-admin users go to the homepage instead. 
# For more info about editing breadcrubs see https://bbpress.org/forums/topic/how-do-i-remove-first-two-parts-of-breadcrumb/
# and https://bbpress.org/forums/topic/how-do-i-edit-bbpress-breadcrumbs/
function my_filter_breadcrumbs($my_curret_crumbs){
	//error_log("og crumbs ".print_r($my_curret_crumbs,true));
	# Admins get the default forum page when clicking the "forum" breadcrub
	if(isset($_COOKIE['disable_my_cache'])){
		return $my_curret_crumbs;
	} # Non admins get the home page when clicking the "forum" breadcrub
	else{
		$my_breadcrumbhome = "/(.*a href=\")(.*)(\".*class=\"bbp-breadcrumb-home\".*)/";
		$my_breadcrumbroot = "/(.*a href=\")(.*)(\".*class=\"bbp-breadcrumb-root\".*)/";
		$my_breadcrumbhomeurl = "";
		for($i=0;$i<count($my_curret_crumbs);$i++){
			$my_breadcrumbhomematches = array();
			if(preg_match($my_breadcrumbhome,$my_curret_crumbs[$i],$my_breadcrumbhomematches)){
			if(count($my_breadcrumbhomematches>3)){
					$my_breadcrumbhomeurl = $my_breadcrumbhomematches[2];
					//error_log("og breadcrumbs found ".$my_breadcrumbhomeurl);
				}
			}
		}
		for($i=0;$i<count($my_curret_crumbs);$i++){
       	        	$my_breadcrumbrootmatches = array();
       	         	if(preg_match($my_breadcrumbroot,$my_curret_crumbs[$i],$my_breadcrumbrootmatches)){
				if(count($my_breadcrumbhomematches>4)){
       		                 	$my_curret_crumbs[$i] = $my_breadcrumbrootmatches[1].$my_breadcrumbhomeurl.$my_breadcrumbrootmatches[3];
					//error_log("og breadcrumbs ".$my_breadcrumbrootmatches[3].", ".$my_breadcrumbhomeurl.", ".$my_breadcrumbrootmatches[3]);
				}
                	}
        	}
	}

	return $my_curret_crumbs;
}
add_filter('bbp_breadcrumbs', 'my_filter_breadcrumbs');

/////////////// Group sorting
// How to add a sort option:
// 1. Add an option to the group directory's sort drop down. You can use the actions bp_groups_directory_order_options and bp_member_group_order_options to do this. 
// The functions og_num_posts_option and og_rank_option add sort options to the sort drop down. You can use these two examples as a guide.
// 2. Add your sort/metadata type to the $og_my_curr_types list in og_my_order_by_number_of_posts. This will work only if your meta type is numeric. If your meta type is not numeric,
// you'll have to add your own SQL code to og_my_order_by_number_of_posts.
// 3. Update your metadata type when needed, using some action or filter (e.g. in og_groups_update_num_posts_and_rank_options, right before an activity occurs)

// Taken from https://codex.buddypress.org/plugindev/group-meta-queries-usage-example/#filter-bp_ajax_querystring-to-eventually-extend-the-groups-query
// The code below adds the new sort options to the sort box

// Number of posts
// Add the number of posts option to the sort drop down list
function og_num_posts_option() {
    ?>
    <option value="og_num_posts"><?php _e( 'Number of Posts' ); ?></option>
    <?php
}
/* finally you create your options in the different select boxes */
// you need to do it for the Groups directory
add_action( 'bp_groups_directory_order_options', 'og_num_posts_option' );
// and for the groups tab of the user's profile
add_action( 'bp_member_group_order_options', 'og_num_posts_option' );

// Rank
// Add the rank option to the sort drop down list
function og_rank_option() {
    ?>
    <option value="og_rank"><?php _e( 'Rank' ); ?></option>
    <?php
}
/* finally you create your options in the different select boxes */
// you need to do it for the Groups directory
add_action( 'bp_groups_directory_order_options', 'og_rank_option' );
// and for the groups tab of the user's profile
add_action( 'bp_member_group_order_options', 'og_rank_option' );

// Additional sort options
// Currently none

// Code that updates and sorts the rank and number of posts 

// Taken from wp-content/plugins/buddypress/bp-groups/bp-groups-activity.php
// Update the group's rank and number of posts right before a group activity gets recorded
// Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php map_activity_to_group and wp-content/plugins/buddypress/bp-groups/bp-groups-activity.php
function og_groups_update_num_posts_and_rank_options($args = array() ){
        // wp-content/plugins/buddypress/bp-groups/bp-groups-forums.php
        //error_log("og group post count here ".print_r($args,true));
        //echo "og here";
        $group_id = 0;
        $og_my_postcount = 0;
        //error_log("og group post count here");
	$group = groups_get_current_group();

	//Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
        // Not posting from a BuddyPress group? stop now!
        if ( !empty( $group ) ) {
                $group_id = $group->id; //bp_get_current_group_id(); //$bp->groups->current_group->id;
                error_log("og group post count id ".$group_id);
	}
	else{
		return $args;
        }
       
        //Taken from wp-content/plugins/bbpress/includes/extend/buddypress/groups.php
        $my_forum_ids = bbp_get_group_forum_ids( $my_group_id );
        $forum_id = null;
        // Get the first forum ID
        if ( !empty( $my_forum_ids ) ) {
               $forum_id = (int) is_array( $my_forum_ids ) ? $my_forum_ids[0] : $my_forum_ids;
               $og_my_postcount = bbp_show_lead_topic() ? bbp_get_forum_reply_count($forum_id) : bbp_get_forum_post_count($forum_id);
        }
	
	// Update the group's post count
        //error_log("og group post count ".$og_my_postcount);
        groups_update_groupmeta( $group_id, 'og_num_posts', $og_my_postcount );
	// Taken from p-content/plugins/buddypress/bp-groups/bp-groups-forums.php
	// Update the group's rank, based on its previous rank
	$og_rank_arg = 'og_rank';
	// Get the previous rank
	$og_prev_grp_rank = groups_get_groupmeta($group_id, $og_rank_arg);
	//error_log("og group post rank ".empty($og_prev_grp_rank));
	// If the rank doesn't exist yet, make it 0
	if(empty($og_prev_grp_rank==null)){
		$og_prev_grp_rank = 0;
	}
	// Update the rank as follows: rank = .7*prev rank + .3*current unix time
	// groups_update_groupmeta .5*og_rank+.5*lastactivitytimeinunix
	groups_update_groupmeta($group_id, $og_rank_arg, .7*$og_prev_grp_rank + .3*microtime(true));

	return $args;
}
add_filter( 'bbp_before_record_activity_parse_args', 'og_groups_update_num_posts_and_rank_options' );

// Sort by rank or number of posts by modifying the SQL query
// Taken from https://codex.buddypress.org/plugindev/add-custom-filters-to-loops-and-enjoy-them-within-your-plugin/ and wp-content/plugins/buddypress/bp-groups/bp-groups-classes.php and from the actual sort values
function og_my_order_by_number_of_posts( $sql = '', $sql_arr = '',$args){
	//error_log("og og_my_order_by_most_favorited ".$sql.": ".print_r($sql_arr,true).": ".print_r($args,true));
	// If the curret sort type matches one of the items in the list, we sort by this type
	$og_my_curr_types = array("og_num_posts","og_rank"); // You can add your own numeric meta type to this list. If your meta type is not numeric you'll have to add your own SQL code to the code below.
	if(in_array($args["type"],$og_my_curr_types)){
		$og_my_curr_type = "";
		$og_idx = array_search($args["type"],$og_my_curr_types);
		$og_my_curr_type = $og_my_curr_types[$og_idx];

		// We need to change the SQL query to include our new meta types (otherwise it'll only include total_member_count and last_activity)
		// The original SQL query looks like this:
		// SELECT DISTINCT g.id, g.*, gm1.meta_value AS total_member_count, gm2.meta_value AS last_activity
		// FROM wp_bp_groups_groupmeta gm1, wp_bp_groups_groupmeta gm2, wp_bp_groups_members m, wp_bp_groups g 
		// WHERE g.id = m.group_id AND g.id = gm1.group_id AND g.id = gm2.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'
		// AND m.user_id = 90 AND m.is_confirmed = 1 AND m.is_banned = 0
		// ORDER BY last_activity DESC
		// LIMIT 0, 20
		// gm1 is used to obtain the total member count and gm2 is used to get the last activity.
		// Here we add a 3rd gm from table from which we get the meta value of our meta type (rank or number of posts). We also make sure to add it
		// to the where clause, to select it and to order by it.  
		$sql_arr["select"]=$sql_arr["select"].", cast(gm3.meta_value as unsigned) AS ".$og_my_curr_type;
		$sql_arr["from"]=$sql_arr["from"]." wp_bp_groups_groupmeta gm3,";
		$sql_arr["where"]=$sql_arr["where"]." AND gm3.meta_key = '".$og_my_curr_type."'"." AND g.id = gm3.group_id"; 
		$sql_arr[0]="ORDER BY ".$og_my_curr_type." DESC";
		//error_log("og og_my_order_by_most_favorited sql:".join( ' ', (array) $sql_arr ));
		return  join( ' ', (array) $sql_arr );
	}
	else{
		return $sql;
	}
}
add_filter( 'bp_groups_get_paged_groups_sql', 'og_my_order_by_number_of_posts' ,     10, 6 );

?>

