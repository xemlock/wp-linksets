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

renderAttachment = (type, data) ->
    li = $ '<li class="wppa-link" />'
    li.append render(type, data)
    li.appendTo '#wpPostAttachments-list'
    return

createLinkFields = () ->
    renderAttachment 'link'
    return

createFileFields = (type, file) ->
    console?.log type, file
    renderAttachment type ? 'file',
        file: file
        file_id: file.id
        title: file.title
        description: file.description
    return

createYoutubeFields = () ->
    renderAttachment 'youtube'
    return

# type is optional
attachFile = (type) ->
    selectFile (selected) ->
        # create box for each selected file
        _.each selected, (file) ->
            createFileFields type, file
    , {type: type, multiple: yes}

attachYoutube = ->
    createYoutubeFields()

attachLink = ->
    createLinkFields()

# name generator for form fields
nameGenerator =
    _counter: 0
    name: (n) ->
        "post_attachments[#{ @_counter }][#{ n }]"
    next: ->
        ++@_counter

# Renders template using built-in WordPress client-side templating system
# based on Underscore templates
render = (name, data) ->
    nameGenerator.next()
    template = wp.template "wpPostAttachments-#{ name }"

    data = $.extend
        $: $
        jQuery: $
        render: render
        renderString: renderString
    , data

    el = $(template data)
    el.find('[name]').each ->
        $this = $ this
        $this.attr 'name', nameGenerator.name($this.attr 'name')

    return el

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
            attachLink()
            return false

        .on 'click', '[data-action="attach-file"]', ->
            attachFile()
            return false

        .on 'click', '[data-action="attach-audio"]', ->
            attachFile 'audio'
            return false

        .on 'click', '[data-action="attach-youtube"]', ->
            attachYoutube()
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
                height: 0
                opacity: 0
            , -> $(this).remove()
            return false



    $('#wpPostAttachments-list').sortable()

    return


@selectFile = selectFile

@wpPostAttachments =
    render: render