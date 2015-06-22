<?php defined('ABSPATH') || die(); ?>
<?php /** @var \wpPostAttachments\Plugin $this */ ?>

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
        $post_attachments = array();
        foreach ($this->get_post_attachments() as $attachment) {
            $post_attachments[] = array_merge($attachment->to_array(), array('thumb_url' => $attachment->get_thumb_url('thumbnail')));
        }
    ?>
    window.postAttachments = <?php echo wp_json_encode($post_attachments) ?>;
</script>

<script type="text/html" id="tmpl-wpPostAttachments-main">
    <ul id="wpPostAttachments-list"></ul>
    <div id="wpPostAttachments-buttons">
        <button type="button" data-action="attach-link"><i class="fa fa-lg fa-link"></i> Link</button>
        <button type="button" data-action="attach-file"><i class="fa fa-lg fa-file-text"></i> File</button>
        <button type="button" data-action="attach-audio"><i class="fa fa-lg fa-volume-up"></i> Audio</button>
        <button type="button" data-action="attach-youtube"><i class="fa fa-lg fa-youtube-play"></i> Youtube</button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-link">
    <div>
        {{{ data.renderString('thumb', data) }}}
        <span><i class="fa fa-link"></i> Website link</span>
        <input type="hidden" name="type" value="link" />
        <input type="text" name="url" value="{{ data.url }}" placeholder="http://" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
        <button type="button" data-action="attachment-delete"><i class="fa fa-times"></i></button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-file">
    <div>
        {{{ data.renderString('thumb', data) }}}
        <span><i class="fa fa-file-text"></i> File</span>
        <input type="hidden" name="type" value="file" />
        <input type="hidden" name="file_id" value="{{ data.file_id }}" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
        <button type="button" data-action="attachment-delete"><i class="fa fa-times"></i></button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-audio">
    <div>
        {{{ data.renderString('thumb', data) }}}
        <span><i class="fa fa-volume-up"></i> Audio file</span>
        <input type="hidden" name="type" value="audio" />
        <input type="hidden" name="file_id" value="{{ data.file_id }}" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
        <button type="button" data-action="attachment-delete"><i class="fa fa-times"></i></button>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-youtube">
    <div>
        {{{ data.renderString('thumb', data) }}}
        <span><i class="fa fa-youtube-play"></i> Youtube Video</span>
        <input type="hidden" name="type" value="youtube" />
        <input type="text" name="video_id" value="{{ data.video_id }}" data-model="video_id" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
        <button type="button" data-action="attachment-delete"><i class="fa fa-times"></i></button>
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
    <div class="wppl-thumb" style="border:1px solid #ccc;width:150px;height:150px;text-align:center;overflow:hidden;">
        <div style="width:300px;height:100%;margin-left:-75px">
        <# if (data.thumb_url) { #>
            <img src="{{ data.thumb_url }}" style="height:100%;" />
        <# } else { #>
            <img src="http://placehold.it/150x150" style="height:100%;" />
        <# } #>
        </div>
    </div>
</script>
