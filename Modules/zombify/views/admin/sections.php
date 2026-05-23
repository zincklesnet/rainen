<?php
if( !isset($active_tab) ) $active_tab = 'index';

$main_active_tab_arr = explode("_", $active_tab);
$main_active_tab = $main_active_tab_arr[0];

$tabs = array(
    "index" => esc_html__( "General", 'zombify' ),
    "formats" => esc_html__( "Post formats", 'zombify' ),
    "branding" => esc_html__( "Branding", 'zombify' ),
    "cloudconvert" => esc_html__( "API Connect", 'zombify' ),
);

$formats_sub_tabs = array(
    "formats" => esc_html__( "Global", 'zombify' ),
    "formats_story" => esc_html__( "Story", 'zombify' ),
);
?>
<h2 class="nav-tab-wrapper">
    <?php
    foreach( $tabs as $tab_slug => $tab_title )
    {
        ?>
        <a href="?page=zombify&action=<?php echo $tab_slug; ?>" class="nav-tab <?php echo ( $active_tab == $tab_slug || $main_active_tab == $tab_slug ) ? 'nav-tab-active' : ''; ?>"><?php echo $tab_title; ?></a>
        <?php
    }
    ?>
</h2>
<div>
<?php
if( $main_active_tab == 'formats' ){
    ?>
    <ul class="subsubsub" style="width:100%">
        <?php
        $i=0;
        foreach( $formats_sub_tabs as $tab_slug => $tab_title )
        {
            ?>
            <li>
                <a href="?page=zombify&action=<?php echo $tab_slug; ?>" class=" <?php echo $active_tab == $tab_slug ? 'current' : ''; ?>"><?php echo $tab_title; ?></a>
                <?php $i++; if( $i<count($formats_sub_tabs) ) echo '|'; ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}
?>
</div>
<p>&nbsp;</p>