<?php
$tier_1 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1));
$tier_1_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1_title));
$tier_2 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2));
$tier_2_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2_title));
$tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));
$tier_3_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_title));
?>
<div class="progress-bar" data-percent="<?php echo $count * 10; ?>">
    <?php for ($i = 1; $i < 11; $i++) {
        $class = '';
        $text = '';
        if ($count >= $tier_1 && $count < $tier_2) {
            $class = 'active';
        }
        if ($i == $tier_1) {
            $text = $tier_1_title;
        }
        if ($count >= $tier_2 && $count < $tier_3) {
            $class = 'active';
        }
        if ($i == $tier_2) {
            $text = $tier_2_title;
        }
        if ($count >= $tier_3) {
            $class = 'active';
        }
        if ($i == $tier_3) {
            $text = $tier_3_title;
        }
        ?>
        <span class="bar <?php echo $class; ?>" data-percent="<?php echo $i; ?>" data-text="<?php echo $text; ?>"></span>
    <?php } ?>
    <span class="progress-bar__inner"></span>
</div>