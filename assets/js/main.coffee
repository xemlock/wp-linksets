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

createLinkFields = () ->
    $('#wpPostAttachments-list').append(
        $('<li/>').html render('link')
    )
    return

createFileFields = (type, file) ->
    console?.log file
    $('#wpPostAttachments-list').append(
        $('<li/>').html render(type,
            file: file
            file_id: file.id
            title: file.title
            description: file.description
        )
    )
    return

createYoutubeFields = () ->
    $('#wpPostAttachments-list').append(
        $('<li/>').html render('youtube')
    )
    return

# type is optional
attachFile = (type) ->
    selectFile (selected) ->
        if selected.length == 0
            return

        createFileFields type, selected[0]
    , {type: type}

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
        attachments: window.postAttachments
    })

    $('#post-attachments-metabox')
        .on 'click', '[data-action="attach-link"]', ->
            attachLink()

        .on 'click', '[data-action="attach-file"]', ->
            attachFile()

        .on 'click', '[data-action="attach-audio"]', ->
            attachFile 'audio'

        .on 'click', '[data-action="attach-youtube"]', ->
            attachYoutube()

    $('#wpPostAttachments-list').sortable()

    return


@selectFile = selectFile

@wpPostAttachments =
    render: render