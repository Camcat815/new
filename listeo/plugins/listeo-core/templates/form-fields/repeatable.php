<!-- Section -->
<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;

$currency_abbr = get_option('listeo_currency');
$currency = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

if (isset($field['value']) && is_array($field['value'])) :
    $i = 0;
?>
    <div class="row">
        <div class="col-md-12">
            <table id="repeatable-list-container">
                <?php
                $i = 0;
                foreach ($field['value'] as $key => $value) {
                ?>
                    <tr class="repeatable-list-item pattern" data-iterator="<?php echo esc_attr($i); ?>">
                        <td>
                            <!-- <div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div> -->
                            <?php if (isset($field['options']) && !empty($field['options'])) {

                                foreach ($field['options'] as $key => $option) {
                                    $maybefields = json_decode($option);
                                    echo  '<div class="fm-input repeatable-' . $key . '">';
                                    if (json_last_error() === 0) {
                                        $type =  (isset($maybefields->type)) ? $maybefields->type : 'text';
                                        $label =  (isset($maybefields->label)) ? $maybefields->label : $key;
                            ?>
                                        <input type="<?php echo $type; ?>" placeholder="<?php echo $label ?>" name="<?php echo $data->key; ?>[<?php echo esc_attr($i); ?>][<?php echo $key; ?>]" value="<?php if (isset($value[$key])) {
                                                                                                                                                                                                            echo $value[$key];
                                                                                                                                                                                                        } ?>" <?php if ($type == 'number') { ?> data-unit="<?php echo esc_attr($currency) ?>" <?php } ?> />
                                    <?php } else { ?>
                                        <input type="text" placeholder="<?php echo $option ?>" name="<?php echo $data->key; ?>[<?php echo esc_attr($i); ?>][<?php echo $key; ?>]" value="<?php if (isset($value[$key])) {
                                                                                                                                                                                                echo $value[$key];
                                                                                                                                                                                            } ?>" />
                                    <?php } ?>
                                    <?php echo '</div>'; ?>
                                <?php } ?>

                            <?php } ?>


                            <div class=" fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
                        </td>
                    </tr>
                <?php
                    $i++;
                } ?>
            </table>
            <a href="#" class="button add-repeatable-list-item"><?php esc_html_e('Add Item', 'listeo_core'); ?></a>

        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-md-12">
            <table id="repeatable-list-container">

                <tr class="repeatable-list-item pattern" data-iterator="0">
                    <td>
                        <!-- <div class="fm-move"><i class="sl sl-icon-cursor-move"></i></div> -->
                        <?php if (isset($field['options']) && !empty($field['options'])) {

                            foreach ($field['options'] as $key => $option) {
                                $maybefields = json_decode($option);
                                echo  '<div class="fm-input repeatable-' . $key . '">';
                                if (json_last_error() === 0) {
                                    $type =  (isset($maybefields->type)) ? $maybefields->type : 'text';
                                    $label =  (isset($maybefields->label)) ? $maybefields->label : $key;
                        ?>
                                    <input type="<?php echo $type; ?>" placeholder="<?php echo $label ?>" name="<?php echo $data->key; ?>[0][<?php echo $key; ?>]" <?php if ($type == 'number') { ?> data-unit="<?php echo esc_attr($currency) ?>" <?php } ?> />
                                <?php } else { ?>
                                    <input type="text" placeholder="<?php echo $option ?>" name="<?php echo $data->key; ?>[0][<?php echo $key; ?>]" />
                                <?php } ?>
                                <?php echo '</div>'; ?>
                            <?php } ?>

                        <?php } ?>
                    
                        <div class=" fm-close"><a class="delete" href="#"><i class="fa fa-remove"></i></a></div>
                    </td>
                </tr>

            </table>
            <a href="#" class="button add-repeatable-list-item"><?php esc_html_e('Add Item', 'listeo_core'); ?></a>

        </div>
    </div>
<?php endif; ?>