
<div class="mycred_settings tabs-panel">
    <!--<form action="<?php /*home_url() */?>?page=formidable-settings&t=MyCRED_settings" method="post">-->
        <table class="form-table">

            <?php if(isset($validation) and ($validation != 'valid' and $validation != 'none')){ ?>
                <tr>
                    <td width="300px" style="color:red;"><?php echo 'Sorry, your license key is '.$validation.'.'; ?></td>
                </tr>

            <?php }
            if(isset($validation) and $validation == 'none'){ ?>
                <tr>
                    <td width="300px"><?php echo 'Please, enter a valid license key.'; ?></td>
                </tr>

            <?php }
            if(isset($validation) and $validation == 'valid'){ ?>
                <tr>
                    <td width="300px" style="color:green;"><?php echo 'Your license key is now active.'; ?></td>
                </tr>
            <?php }
            if(isset($validation) and $validation == 'deactivated'){ ?>
                <tr>
                    <td width="300px" style="color:yellow;"><?php echo 'Your license key has been deactivated!'; ?></td>
                </tr>
            <?php }?>

            <tr class="form-field" valign="top">
                <td width="200px"><label><?php _e('Add a valid Licence Key', 'formidable') ?></label></td>
                <td>
                    <?php $opt = get_option('frm_mc_edd_licence_key'); ?>
                    <input type="text" name="frm_mc_api_key" id="frm_mc_api_key" value="<?php echo (isset($opt) and $opt)? $opt : ''; ?>" class="frm_long_input" />
                </td>
            </tr>

            <tr>
                <?php if(isset($validation) and $validation == 'valid'){ ?>
                    <td><button type="submit" name="deactivate_mc_licence" id="deactivate_mc_licence">Deauthorize</button></td>

                <?php
                }else{
                    ?>
                    <td><button type="submit" name="activate_mc_licence" id="activate_mc_licence">Authorize</button></td>
                <?php
                }
                ?>
            </tr>
        </table>
    <!--</form>-->

</div>
<?php

?>


