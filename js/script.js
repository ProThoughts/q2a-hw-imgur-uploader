if(typeof hw_imgur == 'undefined') var hw_imgur = {};
function HW_MediaBrowser() {
    var p= this,
        doc = document,
        last_selected;  //last select item

    var container = jQuery(doc.createElement('div'));
    container.addClass('album-images');

    //selected state element
    var selection = jQuery(doc.createElement('div'));
    selection.addClass('selection');

    /**
     * events
     */
    p.selected_item_event;

    /**
     * add image to list
     * @param image
     */
    function add_item(image) {
        //image tag
        var img = jQuery('<img/>', {src: image.link});
        img.addClass('thumb');
        img.attr({
            'title': image.title,
            'data-id' : image.id,
            'alt' : image.title
        });

        var img_div = jQuery(doc.createElement('div'));
        img_div.addClass('image-item').attr({id : image.id});
        img_div.bind('click', function(e) {
            set_selected_state(this);
            if(typeof p.selected_item_event == 'function') {
                p.selected_item_event(this, img);
            }
        });

        img_div.append(img);
        return img_div;
    }

    /**
     * set selected state to image
     * @param item
     */
    function set_selected_state(item) {
        //uncheck last item , move to new selected element
        if(last_selected) un_selected(last_selected);

        jQuery(item).addClass('hw-selected').append(selection);
        last_selected = item;   //save last checked item
    }

    /**
     * clear format checked from item
     * @param item
     */
    function un_selected(item) {
        jQuery(item).removeClass('hw-selected');//.remove(selection);
    }
    /**
     * load images
     * @param data
     */
    p.load = function(data) {
        for(var i in data) {
            container.append(add_item(data[i]));
        }
    }
    /**
     * return object
     * @returns {*}
     */
    p.get_object = function() {
        return container;
    }
}
/**
 * get imgur album
 * @param target
 * @param callback
 */
hw_imgur.get_session = function(target, callback) {
    if(target && jQuery(target).hw_is_ajax_working()) return;

    jQuery(target).addClass('hw-loading');
    jQuery.ajax({
        url : hw_imgur.ajaxHandler + '?action=valid_session' ,
        method : 'post',
        data : {'_nonce': hw_imgur.session_token },
        success: function(data) {
            if(typeof callback == 'function') callback(data);
            jQuery(target).removeClass('hw-loading');
            jQuery(target).hw_reset_ajax_state();
        }
    });
}
hw_imgur.init = function() {
    hw_imgur.get_session();
}
/**
 * upload image
 * @param holder element to place output HTML
 * @param options
 */
hw_imgur.load_images = function(holder, options) {
    var $media = new HW_MediaBrowser(),
        holder = jQuery(holder);

    if(jQuery(holder).hw_is_ajax_working()) return;

    //selected image callback event
    if(options && typeof options.selected_item_event == 'function') {
        $media.selected_item_event = options.selected_item_event;
    }
    holder.addClass('hw-loading');
    holder.empty();

    $.ajax({
        url: hw_imgur.ajaxHandler + '?action=load_images',
        dataType: 'json',
        method: 'post',
        data: {
            'al' : 'bld18',
            '_nonce' : hw_imgur.gallery_token
        },
        success: function(resp) {
            console.log('result', resp);
            jQuery(holder).hw_reset_ajax_state();

            holder.removeClass('hw-loading');
            if(resp.result == 'success') {
                //$('#newimgurl').html(resp);
                $media.load(resp.data);
                $media.get_object().appendTo(holder);
                //callback
                if(options && typeof options.success == 'function') {
                    options.success(resp);
                }
            }
            else {
                if(resp.message) holder.append(resp.message);
            }

        }
    });
}