$ = jQuery
REQUEST_KEY = 'linkset'
NO_ITEMS = 'no-items'

wpLinksets =
    POST_THUMBNAIL_URL_STRUCT: ''

selectFile = (onSelect, options) ->
    if frame
        frame.open()
        return

    # Sets up the media library frame
    frame = wp.media
         title: options.title or 'Select file'
         button: {text: 'Select'}
         library: {type: options.type}
         multiple : options.multiple

    frame.on 'open', ->
        if options.selected
            selection = frame.state().get('selection')
            file = wp.media.attachment options.selected;
            file.fetch()
            console.log 'fetched: ', file

            if file
                selection.add [file]

        return

    # Runs when an image is selected
    frame.on 'select', ->
        selected = frame.state().get('selection').map (model) -> model.toJSON()

        onSelect selected if typeof onSelect == 'function'
        return

    frame.open()

selectPost = (onSelect, options) ->
    # depends on findPosts from admin/js/media.js
    if $('#find-posts').size() == 0
        $('body').append (render 'find-posts')

    dialog = $('#find-posts')

    dialog.find('#find-posts-submit')
        .unbind 'click'
        .bind 'click', (e) ->
            e.preventDefault()

            input = dialog.find('[name="found_post_id"]:checked')
            if input.size()
                label = dialog.find('label[for="' + input.attr('id') + '"]')
                selected =
                    id: parseInt(input.val(), 10)
                    title: label.text()

                $(this).unbind 'click'
                findPosts.close()

                onSelect selected if typeof onSelect == 'function'
                return

    # findPosts.open() does not setup event handlers, so we have to do it manually
    # (handlers are done in $.ready which will not work well with templated dialog

    dialog.find('#find-posts-close')
        .unbind 'click'
        .bind 'click', (e) ->
            e.preventDefault()
            findPosts.close()
            return

    $('#find-posts-search').click findPosts.send
    $( '#find-posts .find-box-search :input' ).keypress (e) ->
        if e.which == 13
            findPosts.send()
            return false

    $('#find-posts-close').click findPosts.close

    findPosts.open()



# perform additional logic when rendering attachment
renderers =
    youtube: (el, data) ->
        model = el.find('[name*="video_id"]:input')

        getThumbId = ->
            thumbId = 0 + $.trim el.find('[name*="thumb_id"]:input').val()
            console?.log thumbId
            return if thumbId > 0 then thumbId else 0

        # load video thumbnail if no thumb_id is present
        loadDefaultThumb = ->
            src = "http://img.youtube.com/vi/#{ model.val() }/hqdefault.jpg"

            if not getThumbId()
                i = new Image;
                i.onload = ->
                    orientation = if i.width >= i.height then 'landscape' else 'portrait'

                    # show thumbnail only if thumbnail has not been set
                    if not getThumbId()
                        img = el.find('img')

                        cl = img.clone()
                        cl.attr('src', src)
                        img.replaceWith cl

                        cl.closest('.linkset-item-thumb').addClass orientation

                    console.log i.src, i.width, i.height, orientation
                i.src = src

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

    list = $ '#wpPostAttachments-list'

    li = renderRenamed 'item', data
    li.appendTo list

    # create model independent of provided data
    model = $.extend true, {}, data
    li.data 'linksetItem', model

    list.closest('.linkset-container').removeClass NO_ITEMS

    if typeof renderers[type] == 'function'
        renderers[type] li, data

    return li

# type is optional
attachFile = (type) ->
    selectFile (selected) ->
        # create box for each selected file
        _.each selected, (file) ->
            console?.log file
            renderAttachment
               type: type ? 'file'
               id: file.id
               title: file.title
               description: file.description
               thumb_url: if file.sizes then file.sizes.thumbnail.url else file.thumb_src
               file: file
    , {type: type, multiple: yes}

attachPost = ->
    selectPost (post) ->
        thumbUrl = wpLinksets.POST_THUMBNAIL_URL_STRUCT
            .replace /%post_id%/g, post.id
            .replace /%size%/g, 'thumbnail'
        renderAttachment
            type: 'post'
            id: post.id
            title: post.title
            thumb_url: thumbUrl

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
        REQUEST_KEY + "[#{ @_counter }][#{ n }]"
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

    console?.log wpLinksets.linkset

    _.each wpLinksets.linkset, renderAttachment

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

        .on 'click', '[data-action="attach-post"]', ->
            attachPost()
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
                height: 0
                opacity: 0
                paddingTop: 0
                paddingBottom: 0

            , ->
                $(this).remove()

                list = $ '#wpPostAttachments-list'
                if list.children(':not(.linkset-item-empty)').size() == 0
                    list.closest('.linkset-container').addClass NO_ITEMS


            return false

        .on 'click', '[data-action="thumb-select"]', ->
            # detect selected image
            item = $(this).closest('.linkset-item')
            thumb = item.find('[name*="thumb_id"]').val()

            selectFile (selection) =>
                selectedImage = selection[0]
                console?.log selectedImage, this
                # selectedImage.url
                thumb = if selectedImage.sizes.thumbnail then selectedImage.sizes.thumbnail else selectedImage.sizes.full

                img = item.find('img').attr('src', thumb.url)
                img.closest('.linkset-item-thumb').removeClass('landscape portrait')
                img.closest('.linkset-item-thumb').addClass selectedImage.orientation

                # trigger reflow
                img.replaceWith img.clone()

                item.find('[name*="thumb_id"]').val(selectedImage.id)
                item.addClass('has-thumb')

                # update model accordingly
                model = item.data 'linksetItem'
                model.thumb_id = selectedImage.id
                model.thumb_url = thumb.url

                # successful thumbnail selection removes restore data
                item.removeClass('has-thumb-restore')
                item.removeData 'thumb_restore'

            ,
                type: 'image'
                multiple: false
                selected: thumb

            return false

        .on 'click', '[data-action="thumb-delete"]', ->
            item = $(this).closest('.linkset-item')

            data = item.data 'linksetItem'
            if data.thumb_id
                item.data 'thumb_restore',
                    thumb_id: data.thumb_id
                    thumb_url: data.thumb_url
                item.addClass 'has-thumb-restore'

            item.find('img').attr('src', '')
            item.find('[name*="thumb_id"]').val('')
            item.removeClass('has-thumb')

            return false

        .on 'click', '[data-action="thumb-restore"]', ->
            item = $(this).closest('.linkset-item')
            data = item.data 'thumb_restore'
            if data.thumb_id
                item.find('img').attr 'src', data.thumb_url
                item.find('[name*="thumb_id"]').val data.thumb_id
                item.addClass 'has-thumb'

            item.removeClass 'has-thumb-restore'
            item.removeData 'thumb_restore'

            return false


    $('#wpPostAttachments-list').sortable
        items: '.linkset-item:not(.linkset-item-empty)'

    return

wpLinksets.render = render
wpLinksets.selectFile = selectFile
wpLinksets.selectPost = selectPost

@wpLinksets = wpLinksets
