<div class="zf-inner-wrapper">
    <button class="zf-remove zombify_delete_group" data-zf-not-arrange-group="1">
        <i class="zf-icon-delete"></i>
    </button>
    <div class="zf-sort-area">
        <button class="zf-up js-zf-up"><i class="zf-icon zf-icon-arrow_up"></i></button>
        <button class="zf-down js-zf-down"><i class="zf-icon zf-icon-arrow_down"></i></button>
    </div>
    <div class="zf-body">

        <?php
        $zf_config = zombify()->get_config();

        if( !is_null( $this->getAliasGroups() ) ) {

            foreach( $this->getAliasGroups() as $alias_slug=>$alias_group ){

                if( !in_array($alias_slug, $zf_config["post_sub_types"][$this->subtype]["formats"]) && count($zf_config["post_sub_types"][$this->subtype]["formats"]) > 0 )
                    continue;
                ?>
                <div class="zf-<?php echo $alias_slug; ?>_container zf-included-group">
                    <?php echo $this->renderGroups(['story', $alias_slug], $group_name_prefix, $data, $action); ?>
                </div>
            <?php
            }
        } ?>
    </div>
</div>

<div class="zf-components">
    <button class="zf-components_plus zf-js-components_toggle">
        <i class="zf-icon-delete"></i>
        <i class="zf-icon-add"></i>
    </button>
    <div class="zf-components_wrapper">
        <?php
        if( !is_null( $this->getAliasGroups() ) ) {

            foreach( $this->getAliasGroups() as $alias_slug=>$alias_group ){

                if( !in_array($alias_slug, $zf_config["post_sub_types"][$this->subtype]["formats"]) && count($zf_config["post_sub_types"][$this->subtype]["formats"]) > 0 )
                    continue;
                ?>
                <a class="zf-add-component zombify_add_group" href="#" data-zf-not-arrange-group="1" data-zombify-inside="1"
                   data-zombify-group="story" data-zombify-group-path="story___story" data-include-group="<?php echo $alias_slug; ?>">
                    <i class="<?php echo $alias_group["icon"]; ?>"></i><span class="zf-text"><?php echo $alias_group["label"]; ?></span>
                </a>
            <?php
            }
        } ?>
    </div>
</div>