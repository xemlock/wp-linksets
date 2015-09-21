<?php defined('ABSPATH') || die(); ?>
<?php /** @var \wpLinksets\Plugin $this */ ?>

<div class="linksets-metabox">
    <input type="hidden" name="custom_meta_box_nonce" value="<?php echo $this->get_nonce() ?>" />
    <div id="post-attachments-metabox">
        <p class="linksets-initial">
            <?php echo __('Starting widget, please wait...') ?>
        </p>
    </div>
</div>

<script>
    wpLinksets.POST_URL_STRUCT = <?php echo wp_json_encode(get_site_url() . '/?p=%post_id%') ?>;
    wpLinksets.POST_THUMBNAIL_URL_STRUCT = <?php echo wp_json_encode(get_site_url() . get_unified_post_thumbnail_url_structure()) ?>;
    wpLinksets.messages.selectFile = <?php echo wp_json_encode(__('Select file')) ?>;
    wpLinksets.linkset = <?php echo wp_json_encode($this->get_linkset($post)->get_js_data()) ?>;
</script>

<script type="text/html" id="tmpl-wpPostAttachments-find-posts">
    <?php find_posts_div(); ?>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-main">
    <div class="linkset-container no-items">
        <ul id="wpPostAttachments-list" class="linkset-list">
        </ul>
        <p class="linkset-empty">
            <?php echo __('No links. Add links using the buttons below') ?>
        </p>
    </div>
    <div class="linkset-menubar">
        <p class="linkset-menubar-label">Add link</p>
        <button type="button" class="linkset-menubar-btn" data-action="attach-link">
            <i class="dashicons dashicons-admin-site"></i>
            <?php echo __('Website') ?>
        </button>
        <button type="button" class="linkset-menubar-btn" data-action="attach-post">
            <i class="dashicons dashicons-admin-post"></i>
            <?php echo __('Post') ?>
        </button>
        <button type="button" class="linkset-menubar-btn" data-action="attach-file">
            <i class="dashicons dashicons-media-default"></i>
            <?php echo __('File') ?>
        </button>
        <button type="button" class="linkset-menubar-btn" data-action="attach-youtube">
            <i class="dashicons dashicons-video-alt3"></i>
            <?php echo __('YouTube Video') ?>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-url">
    <div class="linkset-item-type">
        <i class="dashicons dashicons-admin-site"></i>
        <?php echo __('Website') ?>
    </div>

    <input type="text" name="url" value="{{ data.url }}" placeholder="http://" />
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-post">
    <div class="linkset-item-type">
        <i class="dashicons dashicons-admin-post"></i>
        <?php echo __('Post') ?>
    </div>

    <div class="linkset-item-post-info">
        <a href="{{ data.url }}" target="_blank">{{ data.url }}</a>
    </div>

    <input type="hidden" name="id" value="{{ data.id }}" />
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-file">
    <div class="linkset-item-type">
        <i class="dashicons dashicons-media-default"></i>
        <?php echo __('File') ?>
    </div>

    <# if (data.file) { #>
        <div class="linkset-item-file-info">
            <a href="{{ data.file.url }}" target="_blank">{{ data.file.filename }}</a>
        </div>
    <# } #>

    <input type="hidden" name="id" value="{{ data.id }}" />
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-youtube">
    <div class="linkset-item-type">
        <i class="dashicons dashicons-video-alt3"></i>
        <?php echo __('YouTube Video') ?>
    </div>

    <input type="text" name="video_id" value="{{ data.video_id }}" placeholder="<?php echo __('Video ID or URL at YouTube') ?>"/>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-undo">
    <li class="linkset-item not-draggable linkset-item-deleted">
        <p>
            <?php echo __('Link has been removed.') ?>
            <a href="#" data-action="delete-undo" title="Undo link removal"><?php echo __('Undo') ?></a>
        </p>
        <button class="linkset-item-delete" type="button" data-action="delete-confirm" title="<?php echo __('Dismiss') ?>">
            <i class="dashicons dashicons-no"></i>
        </button>
    </li>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item">
    <# var has_thumb = data.thumb_id ? 'has-thumb' : '' #>
    <li class="linkset-item linkset-item-type-{{ data.type }} {{ has_thumb }}">
        <div class="linkset-item-thumb">
            <div class="thumb-inner">
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
                    <a class="linkset-item-thumb-action linkset-item-thumb-action-restore" href="#" data-action="thumb-restore">
                        <i class="dashicons dashicons-undo"></i> <?php echo __('Restore') ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="linkset-item-data">
            <input type="hidden" name="type" value="{{ data.type }}" />
            <input type="hidden" name="date" value="{{ data.date }}" placeholder="YYYY-MM-DD HH:MM" />

            {{{ data.renderString('item-' + data.type, data) }}}

            <div>
                <label><?php echo __('Title') ?></label>
                <input type="text" name="title" value="{{ data.title }}" />
            </div>

            <div>
                <label><?php echo __('Description') ?></label>
                <textarea name="description">{{ data.description }}</textarea>
            </div>
        </div>
        <button class="linkset-item-delete" type="button" data-action="attachment-delete" title="<?php echo __('Remove') ?>">
            <i class="dashicons dashicons-no"></i>
        </button>
    </li>
</script>
