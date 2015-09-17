<?php defined('ABSPATH') || die(); ?>
<?php /** @var \wpLinksets\Plugin $this */ ?>

<input type="hidden" name="custom_meta_box_nonce" value="<?php echo $this->get_nonce() ?>" />

<div id="post-attachments-metabox">
    <?php echo __('Javascript is required for post attachments functionality'); ?>
</div>

<?php
    $linkset = array();
    foreach ($this->get_linkset($post) as $link) {
        $linkset[] = array_merge($link->to_array(), array('thumb_url' => $link->get_thumb_url('thumbnail')));
    }
?>
<script>
    window.wpLinksets.POST_THUMBNAIL_URL_STRUCT = <?php echo wp_json_encode(get_site_url() . get_unified_post_thumbnail_url_structure()) ?>;
    window.wpLinksets.linkset = <?php echo wp_json_encode($linkset) ?>;
</script>

<script type="text/html" id="tmpl-wpPostAttachments-find-posts">
    <?php find_posts_div(); ?>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-main">
    <ul id="wpPostAttachments-list" class="linkset-list no-items">
        <li class="linkset-item linkset-item-empty">
            <?php echo __('No links. Add links using the buttons below') ?>
        </li>
    </ul>
    <div id="wpPostAttachments-buttons">
        <button type="button" data-action="attach-link"><i class="fa fa-lg fa-link"></i> Link</button>
        <button type="button" data-action="attach-post"><i class="fa fa-lg fa-file"></i> Post</button>
        <button type="button" data-action="attach-file"><i class="fa fa-lg fa-file-text"></i> File</button>
        <button type="button" data-action="attach-youtube"><i class="fa fa-lg fa-youtube-play"></i> Youtube</button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-link">
    <div>
        <span><i class="fa fa-link"></i> Website link</span>
        <input type="text" name="url" value="{{ data.url }}" placeholder="http://" />
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-post">
    <div>
        <span><i class="fa fa-file-text"></i> Post</span>
        <input type="hidden" name="id" value="{{ data.id }}" />
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-file">
    <div>
        <span><i class="fa fa-file-text"></i> File</span>
        <input type="hidden" name="id" value="{{ data.id }}" />
        <# if (data.file) { #>
            {{ data.file.filename }}
        <# } #>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-youtube">
    <div>
        <span><i class="fa fa-youtube-play"></i> Youtube Video</span>
        <input type="hidden" name="type" value="youtube" />
        <input type="text" name="video_id" value="{{ data.video_id }}" placeholder="Video ID or URL at YouTube"/>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-undo">
    <div class="linkset-item">
        Link removed
        <button type="button" data-action="delete-undo" title="Undo link removal"><i class="fa fa-undo"></i> Undo</button>

        <button class="linkset-item-delete" type="button" data-action="delete-confirm" title="<?php echo __('Dismiss') ?>">
            <i class="dashicons dashicons-no"></i>
        </button>

    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item">
    <# var has_thumb = data.thumb_id ? 'has-thumb' : '' #>

    <div class="linkset-item linkset-item-type-{{ data.type }} {{ has_thumb }}">
        <div class="linkset-item-thumb">
            <div class="">
                <input type="hidden" name="thumb_id" value="{{ data.thumb_id }}" />
                <# if (data.thumb_url) { #>
                    <img src="{{ data.thumb_url }}" alt="" />
                <# } else { #>
                    <img src="<?php echo $this->get_plugin_url('assets/img/blank.png') ?>" alt="" />
                <# } #>
            </div>
            <div class="linkset-item-thumb-actions">
                <div class="linkset-item-thumb-actions-inner">
                    <a class="linkset-item-thumb-action linkset-item-thumb-action-edit" href="#" data-action="thumb-select">
                        <i class="dashicons dashicons-edit"></i> <?php echo __('Edit') ?>
                    </a>
                    <a class="linkset-item-thumb-action linkset-item-thumb-action-delete" href="#" data-action="thumb-delete">
                        <i class="dashicons dashicons-no"></i> <?php echo __('Delete') ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="linkset-item-data">
            {{{ data.renderString('item-' + data.type, data) }}}
            <input type="hidden" name="type" value="{{ data.type }}" />

            <label><?php echo __('Title') ?></label>
            <input type="text" name="title" value="{{ data.title }}" />

            <label><?php echo __('Description') ?></label>
            <textarea name="description">{{ data.description }}</textarea>

            <input type="hidden" name="date" value="{{ data.date }}" placeholder="YYYY-MM-DD HH:MM" />
        </div>
        <button class="linkset-item-delete" type="button" data-action="attachment-delete" title="<?php echo __('Remove') ?>">
            <i class="dashicons dashicons-no"></i>
        </button>
    </div>
</script>
