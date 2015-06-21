<?php defined('ABSPATH') || die(); ?>
<?php /** @var \wpPostAttachments\Plugin $this */ ?>

<link href="<?php echo $this->get_plugin_url('assets/vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css" />

<!--<script>window.angular||document.write('<script src="<?php echo $this->get_plugin_url('assets/vendor/angular/angular.min.js') ?>"><\/script>')</script>-->
<script><?php require $this->get_plugin_path('assets/js/main.js') ?></script>

<h2><?php echo $this->get_plugin_url() ?></h2>

<input type="hidden" name="custom_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />
<input type="text" name="post_attachments[]" />


<div id="post-attachments-metabox">
    Javascript is required for post attachments functionality
</div>

<script>
    window.postAttachments = <?php echo wp_json_encode(array_map(array($this, '_attachment_to_array'), $this->get_post_attachments())) ?>;
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
        <span><i class="fa fa-link"></i> Website link</span>
        <input type="hidden" name="type" value="link" />
        <input type="text" name="url" value="{{ data.url }}" placeholder="http://" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-file">
    <div>
        <span><i class="fa fa-file-text"></i> File</span>
        <input type="hidden" name="type" value="file" />
        <input type="hidden" name="file_id" value="{{ data.file_id }}" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-audio">
    <div>
        <span><i class="fa fa-volume-up"></i> Audio file</span>
        <input type="hidden" name="type" value="audio" />
        <input type="hidden" name="file_id" value="{{ data.file_id }}" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
    </div>
</script>

<script type="text/html" id="tmpl-wpPostAttachments-youtube">
    <div>
        <img src="http://img.youtube.com/vi/{{ data.video_id }}/default.jpg" />
        <span><i class="fa fa-youtube-play"></i> Youtube Video</span>
        <input type="hidden" name="type" value="youtube" />
        <input type="text" name="video_id" value="{{ data.video_id }}" onchange="jQuery(this).parent().find('img').attr('src', 'http://img.youtube.com/vi/' + escape(this.value) + '/default.jpg')" />
        <input type="text" name="title" value="{{ data.title }}" />
        <input type="text" name="date" value="{{ data.value }}" placeholder="YYYY-MM-DD HH:MM" />
        <textarea name="description">{{ data.description }}</textarea>
    </div>
</script>

