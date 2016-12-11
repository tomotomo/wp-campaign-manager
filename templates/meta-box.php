<?php
/**
 * @var string $field_name
 * @var string $url
 */
?>
<table class="form-table">

    <tr>
        <th><label for="<?= $field_name ?>" class="url">URL</label></th>
        <td>
            <input type="url" id="<?= $field_name ?>" name="<?= $field_name ?>" class="<?= $field_name ?>_field widefat"
                   placeholder="Campaign detail url." value="<?= esc_attr__( $url ) ?>">
            <p>
                <a href="<?= esc_attr__( $url ) ?>" target="_blank" id="url_checker">Check URL</a>
                <span class="notice">Sorry: This link isn't changed real time.</span>
            </p>
        </td>
    </tr>

</table>

