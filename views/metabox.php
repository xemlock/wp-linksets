<?php defined('ABSPATH') || die(); ?>
<?php // ini_set('display_errors', 1);error_reporting(-1); ?>
<?php /** @var \wpLinksets\Plugin $this */ ?>

<style>
.wppa-link {
    padding: 10px;
    background: #fff;
}
.wppa-link + .wppa-link {
    border-top: 1px solid #ccc;
}
.wppa-link.ui-sortable-helper {
    border-top: none;
    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
}
.wppa-url {

}
#wpPostAttachments-buttons {
    background: #eee;
    padding: 10px;
}

.wppl-thumb {
    border:1px solid #ccc;width:150px;height:150px;text-align:center;overflow:hidden;
}
.wppl-thumb-inner {
    width:300px;
    height:100%;
    margin-left:-75px;
    position: relative;
    z-index: 0;
}
.wppl-thumb img {
    max-height: 100%;
    max-width: 100%;
}
.wppl-thumb-inner:hover {
    cursor: pointer;
}
.wppl-thumb-inner:hover:before {
    content: '';
    display: block;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    border: 4px solid red;
    z-index: 2;
}
</style>

<link href="<?php echo $this->get_plugin_url('assets/vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css" />
<script><?php require $this->get_plugin_path('assets/js/main.js') ?></script>
<script>window.moment||document.write('<script src="<?php echo $this->get_plugin_url('assets/vendor/moment/min/moment.min.js') ?>"><\/script>')</script>

<input type="hidden" name="custom_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />

<div id="post-attachments-metabox">
    Javascript is required for post attachments functionality
</div>

<script>
    <?php
        $linkset = array();
        foreach ($this->get_linkset($post) as $link) {
            $linkset[] = array_merge($link->to_array(), array('thumb_url' => $link->get_thumb_url('thumbnail')));
        }
    ?>
    window.postAttachments = <?php echo wp_json_encode($linkset) ?>;
</script>

<script type="text/html" id="tmpl-wpPostAttachments-find-posts">
    <?php find_posts_div(); ?>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-main">
    <ul id="wpPostAttachments-list"></ul>
    <div id="wpPostAttachments-buttons">
        <button type="button" data-action="attach-link"><i class="fa fa-lg fa-link"></i> Link</button>
        <button type="button" data-action="attach-file"><i class="fa fa-lg fa-file-text"></i> File</button>
        <button type="button" data-action="attach-audio"><i class="fa fa-lg fa-volume-up"></i> Audio</button>
        <button type="button" data-action="attach-youtube"><i class="fa fa-lg fa-youtube-play"></i> Youtube</button>
        <button type="button" data-action="attach-post"><i class="fa fa-lg fa-file"></i> Post</button>
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
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-audio">
    <div>
        <span><i class="fa fa-volume-up"></i> Audio file</span>
        <input type="hidden" name="id" value="{{ data.id }}" />
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item-youtube">
    <div>
        <span><i class="fa fa-youtube-play"></i> Youtube Video</span>
        <input type="hidden" name="type" value="youtube" />
        <input type="text" name="video_id" value="{{ data.video_id }}" />
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-undo">
    <div>
        Link removed
        <button type="button" data-action="delete-undo" title="Undo link removal"><i class="fa fa-undo"></i> Undo</button>
        <button type="button" data-action="delete-confirm" title="Dismiss this notification"><i class="fa fa-times"></i></button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-thumb">
    <div class="wppl-thumb">
        <div class="wppl-thumb-inner" data-action="thumb-select">
            <input type="hidden" name="thumb_id" value="{{ data.thumb_id }}" />
            <# if (data.thumb_url) { #>
                <img src="{{ data.thumb_url }}" alt="" />
            <# } else { #>
                <img src="http://placehold.it/150x150" alt="" />
            <# } #>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-item">
    <div>
        {{{ data.renderString('thumb', data) }}}
        {{{ data.renderString('item-' + data.type, data) }}}
        <input type="hidden" name="type" value="{{ data.type }}" />
        <input type="text" name="title" value="{{ data.title }}" />
        <textarea name="description">{{ data.description }}</textarea>
        <input type="hidden" name="date" value="{{ data.date }}" placeholder="YYYY-MM-DD HH:MM" />
        <button type="button" data-action="attachment-delete"><i class="fa fa-times"></i></button>
    </div>
</script>
