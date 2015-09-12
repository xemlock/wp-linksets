$ = jQuery

selectFile = (onSelect, options) ->
    if frame
        frame.open()
        return

    # Sets up the media library frame
    frame = wp.media
         title: options.title or 'Select file',
         button: {text: 'Select'},
         library: {type: options.type},
         multiple : options.multiple

    frame.on 'open', ->
        selection = frame.state().get('selection')
        # wp.media.attachment input.val()

        #attachment.fetch()
        #selection.add if attachment then [attachment] else []
        return

    # Runs when an image is selected
    frame.on 'select', ->
        selected = frame.state().get('selection').map (model) -> model.toJSON()

        onSelect selected if typeof onSelect == 'function'
        return

    frame.open()


# perform additional logic when rendering attachment
renderers =
    youtube: (el, data) ->
        model = el.find('[name*="video_id"]:input')

        loadDefaultThumb = ->
            src = "http://img.youtube.com/vi/#{ model.val() }/default.jpg"
            thumb_id = el.find('[name*="thumb_id"]:input').val()
            console?.log thumb_id
            if $.trim(thumb_id) == ''
                el.find('img').attr('src', src)
            return

        model.change loadDefaultThumb
        loadDefaultThumb()

        # TODO show default YT thumb when thumbnail is removed
        return

# Render link using given data
renderAttachment = (data) ->
    if typeof data == 'string'
        data = {type: data}

    type = data.type

    li = $ '<li class="wppa-link" />'
    li.appendTo '#wpPostAttachments-list'
    li.append (el = renderRenamed 'item', data)

    if typeof renderers[type] == 'function'
        renderers[type] el, data

    return li

# type is optional
attachFile = (type) ->
    selectFile (selected) ->
        # create box for each selected file
        _.each selected, (file) ->
            console?.log file
            renderAttachment type ? 'file',
               file: file
               file_id: file.id
               title: file.title
               description: file.description
               thumb_url: if file.sizes then file.sizes.thumbnail.url else file.thumb_src
    , {type: type, multiple: yes}

# render link
renderRenamed = (name, data) ->
    nameGenerator.next()

    console?.log 'renderRenamed', name, data

    el = render name, data
    el.find('[name]').each ->
      $this = $ this
      $this.attr 'name', nameGenerator.name($this.attr 'name')

    return el

# name generator for form fields
nameGenerator =
    _counter: 0
    name: (n) ->
        "post_links[#{ @_counter }][#{ n }]"
    next: ->
        ++@_counter

# Renders template using built-in WordPress client-side templating system
# based on Underscore templates
render = (name, data) ->
    template = wp.template "wpPostAttachments-#{ name }"

    data = $.extend
        $: $
        jQuery: $
        render: render
        renderString: renderString
    , data

    el = $(template data)



renderString = (name, data) ->
    return $('<div/>').append(render name, data).html()

$ ->
    template = wp.template "wpPostAttachments-main"
    $('#post-attachments-metabox').html (template {
        render: render
        renderString: renderString
    })

    console?.log window.postAttachments

    _.each window.postAttachments, (attachment) ->
        renderAttachment attachment.type, attachment

    $('#post-attachments-metabox')
        .on 'click', '[data-action="attach-link"]', ->
            renderAttachment 'link'
            return false

        .on 'click', '[data-action="attach-file"]', ->
            attachFile()
            return false

        .on 'click', '[data-action="attach-audio"]', ->
            attachFile 'audio'
            return false

        .on 'click', '[data-action="attach-youtube"]', ->
            renderAttachment 'youtube'
            return false

        .on 'click', '[data-action="attachment-delete"]', ->
            li = $(this).closest('li')
            li2 = $ '<li class="wppa-link" />'

            tpl = wp.template "wpPostAttachments-undo"
            li2.append tpl()

            li2.outerWidth li.outerWidth()
            li2.outerHeight li.outerHeight()
            li.replaceWith li2

            li2.data 'origLI', li

            return false

        .on 'click', '[data-action="delete-undo"]', ->
            li = $(this).closest('li')
            li.replaceWith li.data('origLI')
            li.remove()
            return false

        .on 'click', '[data-action="delete-confirm"]', ->
            li = $(this).closest('li')
            li.animate
                outerHeight: 0
                opacity: 0
            , -> $(this).remove()
            return false

        .on 'click', '[data-action="thumb-select"]', ->
            selectFile (selection) =>
                selectedImage = selection[0]
                console?.log selectedImage, this
                # selectedImage.url
                thumb = if selectedImage.sizes.thumbnail then selectedImage.sizes.thumbnail else selectedImage.sizes.full
                $(this).find('img').attr('src', thumb.url)
                $(this).find('[name*="thumb_id"]').val(selectedImage.id)
            ,
                type: 'image'
                multiple: false




    $('#wpPostAttachments-list').sortable()

    return


@selectFile = selectFile

@wpPostAttachments =
    render: render