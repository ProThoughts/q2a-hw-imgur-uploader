/**
 * check wether ajax working that fire by button toggle
 * @param options plugin options
 */
jQuery.fn.hw_is_ajax_working = function(options) {
    if(this.data('hw_ajax_working')) return true;
    this.data( "hw_ajax_working", 1 );  //set event status

    var data = {};

    //prepare data that i will used to save some info on element
    if(!this.data('hw-data')) {
        this.data('hw-data', data);
    }
    else data = this.data('hw-data');

    //get current content on element
    if(!data.text) data.text = this.text();
    if(!data.html) data.html = this.html();

    // This is the easiest way to have default options.
    var settings = jQuery.extend({
        // These are the defaults.
        color: "#556b2f",
        backgroundColor: "white"
    }, options );

    //change object text
    if(settings.loadingText) this.text(settings.loadingText);

    //update data on element
    this.data('hw-data', data);
    return false;
};
/**
 * reset ajax to work
 * @param resume_content
 */
jQuery.fn.hw_reset_ajax_state = function(resume_content){
    this.data("hw_ajax_working", 0);    //allow make to call ajax
    var data = this.data('hw-data');
    //resume element content
    if(data.html && resume_content) this.html(data.html);
};
/**
 * add loading image to container
 * @param options
 */
jQuery.fn.hw_set_loadingImage = function(options) {
    //prepare loading img
    if(! this.data('hw_loading_img') ) {
        var img = jQuery('<img/>', {src : __hw_global_object.loading_image});
        this.data('hw_loading_img', img);
    }
    // This is the easiest way to have default options.
    var settings = jQuery.extend({
        // These are the defaults.
        target: "",
        place: "after"
    }, options );
    var img = jQuery(this.data('hw_loading_img')).show();  //get loading element, and make it visible

    //valid
    if(!settings.target) settings.target = this;    //target object to render loading image

    //append loading image to container
    if(settings.place == 'replace') jQuery(settings.target).empty().append(img);
    else if(settings.place == 'after') jQuery(settings.target).append(img);
    else if(settings.place == 'before') jQuery(settings.target).prepend(img);
};
/**
 * remove loading image from container get from current element
 * @param target (optional) ->seem no longer use
 */
jQuery.fn.hw_remove_loadingImage = function(target) {
    if(! this.data('hw_loading_img') ) return ;
    if(!target) target = this;  //target is current element

    var img = this.data('hw_loading_img');
    //jQuery(target).remove(img);
    jQuery(img).hide(); //best way
};