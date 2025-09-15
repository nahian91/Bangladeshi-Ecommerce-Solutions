jQuery(document).ready(function($){
    var besActiveTab = besSettings.activeTab;

    $('.bes-tab-label input[type=checkbox]').on('change', function(){
        var tabKey = $(this).attr('name').match(/\[(.*?)\]/)[1];
        var tabLink = $('.nav-tab-wrapper a[href*="tab='+tabKey+'"]');

        if($(this).is(':checked')){
            if(tabLink.length === 0){
                var tabLabel = $(this).closest('.bes-tab-label').find('.bes-tab-text').text().trim();
                var newTab = $('<a class="nav-tab" href="?page=bes-settings&tab='+tabKey+'">'+tabLabel+'</a>');
                $('.nav-tab-wrapper').append(newTab);
            }
        } else {
            if(tabKey !== besActiveTab){
                tabLink.remove();
            }
        }
    });
});
