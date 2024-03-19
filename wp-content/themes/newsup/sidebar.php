<?php

/**
 * The sidebar containing the main widget area.
 *
 * @package Newsup
 */
if (!is_active_sidebar('sidebar-1')) {
	return;
}

$users = [];

foreach (get_posts([
	'numberposts' => -1,
	'post_type'   => 'cpt_staff_lst_item',
	'fields' 	=> 'ids'
]) as $post) {
	$userInfo = get_post_meta($post);
	if (!isset($userInfo['_txt_F2']) or !$userInfo['_txt_F2']) continue;
	$birthday = [];
	if (preg_match('/[0-9]{1,2}.[0-9]{1,2}.[0-9]{1,4}/', array_pop($userInfo['_txt_F2']), $birthday)) {
		if (!empty($birthday)) {
			$birthday = str_replace([',', '.'], '.', array_pop($birthday));
		}
	}
	$users[] = [
		'post_id' 					=> $post,
		'name' 						=> array_pop($userInfo['_mp1_F1']),
		'work_place'				=> array_pop($userInfo['_txt_F5']),
		'birthday' 					=> $birthday,
		'until_birthday'			=> get_until_birthday($birthday)
	];
}

function get_until_birthday($birthday)
{
	$bd = explode('.', $birthday);
	$bd = mktime(0, 0, 0, $bd[1], $bd[0], date('Y') + ($bd[1] . $bd[0] <= date('md')));
	$days_until = ceil(($bd -  time()) / 86400);
	$days_until = ((int)$days_until === 365) ? (int)0 : (int)$days_until;
	return abs($days_until);
}

function get_birthday_widget($users, $length = 5)
{
	if (empty($users)) return;
	usort($users, function ($user, $nextUser) {
		return ($user['until_birthday'] - $nextUser['until_birthday']);
	});
	$users = array_slice($users, 0, $length);
	$intlFormatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
	$intlFormatter->setPattern('d MMMM');
?>
    <div id="until_the_birthday" class="mg-widget widget_recent_entries">
        <div class="mg-wid-title">
            <h6>Ближайшие дни рождения</h6>
        </div>
        <ul>
            <?php foreach ($users as $user) : ?>
                <?php
                $userLink = "<a href='http://localhost/myKtr/profile/?smid={$user['post_id']}'>{$user['name']}</a>";
                $userWorkPlace = $user['work_place'];
                $short_birthday = new DateTime($user['birthday']);
                $short_birthday = isset($intlFormatter) ? $intlFormatter->format($short_birthday) : '';
                ?>
                <li>
                    <strong><?= $userLink ?></strong>
                    <br>
                    <?= $userWorkPlace ?>
                    <br>
                    <?= $short_birthday ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
}
?>