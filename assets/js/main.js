// Generated by CoffeeScript 1.9.2
(function() {
  var $, CLASS_HAS_THUMB, CLASS_HAS_THUMB_RESTORE, CLASS_NO_ITEMS, DATA_DELETE_UNDO, DATA_NS, DATA_THUMB_RESTORE, EVENT_THUMB_DELETE, REQUEST_KEY, attachFile, attachPost, dataKey, formatDate, loadDefaultThumbnail, nameGenerator, postHandler, render, renderAttachment, renderRenamed, renderString, renderers, selectFile, selectPost, wpLinksets;

  $ = jQuery;

  REQUEST_KEY = 'linkset';

  CLASS_NO_ITEMS = 'no-items';

  CLASS_HAS_THUMB = 'has-thumb';

  CLASS_HAS_THUMB_RESTORE = 'has-thumb-restore';

  DATA_NS = 'wpLinksets';

  DATA_DELETE_UNDO = 'deleteUndo';

  DATA_THUMB_RESTORE = 'thumbRestore';

  EVENT_THUMB_DELETE = 'thumbdelete';

  wpLinksets = {
    POST_URL_STRUCT: '',
    POST_THUMBNAIL_URL_STRUCT: '',
    messages: {
      selectFile: 'Select file'
    }
  };

  dataKey = function(key) {
    return DATA_NS + '.' + key;
  };

  selectFile = function(onSelect, options) {
    var frame;
    if (frame) {
      frame.open();
      return;
    }
    frame = wp.media({
      title: options.title || wpLinksets.messages.selectFile,
      button: {
        text: 'Select'
      },
      library: {
        type: options.type
      },
      multiple: options.multiple
    });
    frame.on('open', function() {
      var file, selection;
      if (options.selected) {
        selection = frame.state().get('selection');
        file = wp.media.attachment(options.selected);
        file.fetch();
        if (typeof console !== "undefined" && console !== null) {
          console.log('fetched: ', file);
        }
        if (file) {
          selection.add([file]);
        }
      }
    });
    frame.on('select', function() {
      var selected;
      selected = frame.state().get('selection').map(function(model) {
        return model.toJSON();
      });
      if (typeof onSelect === 'function') {
        onSelect(selected);
      }
    });
    return frame.open();
  };

  selectPost = function(onSelect, options) {
    var dialog;
    if ($('#find-posts').size() === 0) {
      $('body').append(render('find-posts'));
    }
    dialog = $('#find-posts');
    dialog.find('#find-posts-submit').unbind('click').bind('click', function(e) {
      var input, label, post_id, selected;
      e.preventDefault();
      input = dialog.find('[name="found_post_id"]:checked');
      if (input.size()) {
        label = dialog.find('label[for="' + input.attr('id') + '"]');
        post_id = parseInt(input.val(), 10);
        selected = {
          id: post_id,
          title: label.text()
        };
        $(this).unbind('click');
        findPosts.close();
        if (typeof onSelect === 'function') {
          onSelect(selected);
        }
      }
    });
    dialog.find('#find-posts-close').unbind('click').bind('click', function(e) {
      e.preventDefault();
      findPosts.close();
    });
    $('#find-posts-search').click(findPosts.send);
    $('#find-posts .find-box-search :input').keypress(function(e) {
      if (e.which === 13) {
        findPosts.send();
        return false;
      }
    });
    $('#find-posts-close').click(findPosts.close);
    return findPosts.open();
  };

  loadDefaultThumbnail = function(el, url) {};

  postHandler = function(el, data) {
    var getThumbId;
    getThumbId = function() {
      var thumbId;
      thumbId = 0 + $.trim(el.find('[name*="thumb_id"]').val());
      if (typeof console !== "undefined" && console !== null) {
        console.log(thumbId);
      }
      if (thumbId > 0) {
        return thumbId;
      } else {
        return 0;
      }
    };
    return el.on(EVENT_THUMB_DELETE, function() {
      var i, img, input, src;
      input = el.find('[name*="id"]:input');
      img = el.find('img');
      src = wpLinksets.POST_THUMBNAIL_URL_STRUCT.replace(/%post_id%/g, data.id).replace(/%size%/g, 'thumbnail');
      i = new Image;
      i.onload = function() {
        var cl, orientation;
        orientation = i.width >= i.height ? 'landscape' : 'portrait';
        if (typeof console !== "undefined" && console !== null) {
          console.log(orientation, i.src, i.width, i.height);
        }
        if (!getThumbId()) {
          img = el.find('img');
          cl = img.clone();
          cl.attr('src', src);
          img.replaceWith(cl);
          cl.closest('.linkset-item-thumb').addClass(orientation);
        }
      };
      i.src = src;
    });
  };

  renderers = {
    post: postHandler,
    file: postHandler,
    youtube: function(el, data) {
      var getThumbId, input, loadDefaultThumb;
      input = el.find('[name*="video_id"]:input');
      getThumbId = function() {
        var thumbId;
        thumbId = 0 + $.trim(el.find('[name*="thumb_id"]').val());
        if (typeof console !== "undefined" && console !== null) {
          console.log(thumbId);
        }
        if (thumbId > 0) {
          return thumbId;
        } else {
          return 0;
        }
      };
      loadDefaultThumb = function() {
        var i, src;
        src = "http://img.youtube.com/vi/" + (input.val()) + "/hqdefault.jpg";
        if (!getThumbId()) {
          i = new Image;
          i.onload = function() {
            var cl, img, orientation;
            orientation = i.width >= i.height ? 'landscape' : 'portrait';
            if (!getThumbId()) {
              img = el.find('img');
              cl = img.clone();
              cl.attr('src', src);
              img.replaceWith(cl);
              cl.closest('.linkset-item-thumb').addClass(orientation);
            }
            return typeof console !== "undefined" && console !== null ? console.log(i.src, i.width, i.height, orientation) : void 0;
          };
          i.src = src;
        }
      };
      input.change(loadDefaultThumb);
      loadDefaultThumb();
      el.on(EVENT_THUMB_DELETE, loadDefaultThumb);
    }
  };

  renderAttachment = function(data) {
    var li, list, model, type;
    if (typeof data === 'string') {
      data = {
        type: data
      };
    }
    type = data.type;
    list = $('#wpPostAttachments-list');
    li = renderRenamed('item', data);
    li.appendTo(list);
    model = $.extend(true, {}, data);
    li.data('linksetItem', model);
    list.closest('.linkset-container').removeClass(CLASS_NO_ITEMS);
    if (typeof renderers[type] === 'function') {
      renderers[type](li, data);
    }
    return li;
  };

  formatDate = function(date) {
    var _f, day, hour, min, month, sec, year;
    _f = function(x) {
      if (x < 10) {
        return '0' + String(x);
      } else {
        return String(x);
      }
    };
    day = _f(date.getDate());
    month = _f(date.getMonth() + 1);
    year = date.getFullYear();
    hour = _f(date.getHours());
    min = _f(date.getMinutes());
    sec = _f(date.getSeconds());
    return year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
  };

  attachFile = function(type) {
    return selectFile(function(selected) {
      return _.each(selected, function(file) {
        if (typeof console !== "undefined" && console !== null) {
          console.log(file);
        }
        return renderAttachment({
          type: type != null ? type : 'file',
          id: file.id,
          title: file.title,
          description: file.description,
          thumb_url: file.sizes ? file.sizes.thumbnail.url : file.thumb_src,
          file: file,
          date: formatDate(file.date)
        });
      });
    }, {
      type: type,
      multiple: true
    });
  };

  attachPost = function() {
    return selectPost(function(post) {
      var postUrl, thumbUrl;
      postUrl = wpLinksets.POST_URL_STRUCT.replace(/%post_id%/g, post.id);
      thumbUrl = wpLinksets.POST_THUMBNAIL_URL_STRUCT.replace(/%post_id%/g, post.id).replace(/%size%/g, 'thumbnail');
      return renderAttachment({
        type: 'post',
        id: post.id,
        title: post.title,
        url: postUrl,
        thumb_url: thumbUrl
      });
    });
  };

  renderRenamed = function(name, data) {
    var el;
    nameGenerator.next();
    if (typeof console !== "undefined" && console !== null) {
      console.log('renderRenamed', name, data);
    }
    el = render(name, data);
    el.find('[name]').each(function() {
      var $this;
      $this = $(this);
      return $this.attr('name', nameGenerator.name($this.attr('name')));
    });
    return el;
  };

  nameGenerator = {
    _counter: 0,
    name: function(n) {
      return REQUEST_KEY + ("[" + this._counter + "][" + n + "]");
    },
    next: function() {
      return ++this._counter;
    }
  };

  render = function(name, data) {
    var el, template;
    template = wp.template("wpPostAttachments-" + name);
    data = $.extend({
      $: $,
      jQuery: $,
      render: render,
      renderString: renderString
    }, data);
    return el = $(template(data));
  };

  renderString = function(name, data) {
    return $('<div/>').append(render(name, data)).html();
  };

  $(function() {
    var template;
    template = wp.template("wpPostAttachments-main");
    $('#post-attachments-metabox').html(template({
      render: render,
      renderString: renderString
    }));
    if (typeof console !== "undefined" && console !== null) {
      console.log(wpLinksets.linkset);
    }
    _.each(wpLinksets.linkset, renderAttachment);
    $('#post-attachments-metabox').on('click', '[data-action="attach-link"]', function() {
      renderAttachment('link');
      return false;
    }).on('click', '[data-action="attach-file"]', function() {
      attachFile();
      return false;
    }).on('click', '[data-action="attach-audio"]', function() {
      attachFile('audio');
      return false;
    }).on('click', '[data-action="attach-youtube"]', function() {
      renderAttachment('youtube');
      return false;
    }).on('click', '[data-action="attach-post"]', function() {
      attachPost();
      return false;
    }).on('click', '[data-action="attachment-delete"]', function() {
      var h, li, li2, tpl;
      li = $(this).closest('li');
      tpl = wp.template("wpPostAttachments-undo");
      li2 = $(tpl());
      li.after(li2);
      h = li2.height();
      li2.data(dataKey(DATA_DELETE_UNDO), {
        el: li,
        animate: {
          height: li.height(),
          opacity: 1
        }
      });
      li2.outerHeight(li.outerHeight());
      li.replaceWith(li2);
      li.height(h);
      li2.animate({
        height: h
      });
      return false;
    }).on('click', '[data-action="delete-undo"]', function() {
      var li, undoData;
      li = $(this).closest('li');
      li.stop();
      undoData = li.data(dataKey(DATA_DELETE_UNDO));
      li.replaceWith(undoData.el);
      li.remove();
      undoData.el.animate(undoData.animate);
      return false;
    }).on('click', '[data-action="delete-confirm"]', function() {
      var li;
      li = $(this).closest('li');
      li.animate({
        outerHeight: 0,
        height: 0,
        opacity: 0,
        paddingTop: 0,
        paddingBottom: 0
      }, function() {
        var list;
        $(this).remove();
        list = $('#wpPostAttachments-list');
        if (list.children().size() === 0) {
          return list.closest('.linkset-container').addClass(CLASS_NO_ITEMS);
        }
      });
      return false;
    }).on('click', '[data-action="thumb-select"]', function() {
      var item, thumb;
      item = $(this).closest('.linkset-item');
      thumb = item.find('[name*="thumb_id"]').val();
      selectFile((function(_this) {
        return function(selection) {
          var img, model, selectedImage;
          selectedImage = selection[0];
          if (typeof console !== "undefined" && console !== null) {
            console.log(selectedImage, _this);
          }
          thumb = selectedImage.sizes.thumbnail ? selectedImage.sizes.thumbnail : selectedImage.sizes.full;
          img = item.find('img').attr('src', thumb.url);
          img.closest('.linkset-item-thumb').removeClass('landscape portrait');
          img.closest('.linkset-item-thumb').addClass(selectedImage.orientation);
          img.replaceWith(img.clone());
          item.find('[name*="thumb_id"]').val(selectedImage.id);
          item.addClass(CLASS_HAS_THUMB);
          model = item.data('linksetItem');
          model.thumb_id = selectedImage.id;
          model.thumb_url = thumb.url;
          item.removeClass('has-thumb-restore');
          return item.removeData(dataKey(DATA_THUMB_RESTORE));
        };
      })(this), {
        type: 'image',
        multiple: false,
        selected: thumb
      });
      return false;
    }).on('click', '[data-action="thumb-delete"]', function() {
      var data, item;
      item = $(this).closest('.linkset-item');
      data = item.data('linksetItem');
      if (data.thumb_id) {
        item.data(dataKey(DATA_THUMB_RESTORE), {
          thumb_id: data.thumb_id,
          thumb_url: data.thumb_url
        });
        item.addClass(CLASS_HAS_THUMB_RESTORE);
      }
      item.find('img').attr('src', '');
      item.find('[name*="thumb_id"]').val('');
      item.removeClass(CLASS_HAS_THUMB);
      item.trigger(EVENT_THUMB_DELETE);
      return false;
    }).on('click', '[data-action="thumb-restore"]', function() {
      var data, item;
      item = $(this).closest('.linkset-item');
      data = item.data(dataKey(DATA_THUMB_RESTORE));
      if (data.thumb_id) {
        item.find('img').attr('src', data.thumb_url);
        item.find('[name*="thumb_id"]').val(data.thumb_id);
        item.addClass(CLASS_HAS_THUMB);
      }
      item.removeClass(CLASS_HAS_THUMB_RESTORE);
      item.removeData(dataKey(DATA_THUMB_RESTORE));
      return false;
    });
    $('#wpPostAttachments-list').sortable({
      items: '.linkset-item:not(.not-draggable)'
    });
  });

  wpLinksets.render = render;

  wpLinksets.selectFile = selectFile;

  wpLinksets.selectPost = selectPost;

  this.wpLinksets = wpLinksets;

}).call(this);

//# sourceMappingURL=main.js.map
