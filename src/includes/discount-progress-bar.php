<?php
$tier_1 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1));
$tier_1_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1_title));
$tier_2 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2));
$tier_2_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2_title));
$tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));
$tier_3_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_title));
?>
<h3>Multi-Ad discount tracker</h3>
<p>Be encouraged. R199 inc VAT for a single listing for a whole month is already cost effective – but add more SKU’s
    and we will discount you further.</p>
<p>Hit our maximum discount tier of 10+ listings and we will turn all those 30 day ads into 60 day ads for free. Yes,
    you read it correctly – at max discount you will get less 15% on all your ads and we will double the time they will
    stay on the website to increase your opportunity to generate sales.</p>
<div class="progress-bar" data-percent="<?php echo $count * 10; ?>">
    <?php
    $max = 10;

    for ($i = 1; $i <= $max; $i++) {
        $class = array();
        $text = '';
        if ($count >= $tier_1 && $count < $tier_2) {
            $class[] = 'active';
        }
        if ($i == $tier_1) {
            $class[] = 'discount';
            $text = $tier_1_title;
        }
        if ($count >= $tier_2 && $count < $tier_3) {
            $class[] = 'active';
        }
        if ($i == $tier_2) {
            $class[] = 'discount';
            $text = $tier_2_title;
        }
        if ($count >= $tier_3) {
            $class[] = 'active';
        }
        if ($i == $tier_3) {
            $class[] = 'discount';
            $text = $tier_3_title;
        }
        ?>
        <span class="bar <?php echo join(" ", $class); ?>" data-percent="<?php echo $i . ($i === $max ? '+' : ''); ?>"
            data-text="<?php echo $text; ?>"></span>
    <?php } ?>
    <span class="progress-bar__inner"></span>
</div>