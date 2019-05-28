$(document).ready(function () {
    bindFilterToggle();

    /**
     * Change the icon displayed in the filter link when clicking on it.
     */
    function bindFilterToggle() {
        $("[data-toggle='collapse']").on('click', function () {
            let icon = $(this).find('i.fa');

            if (icon.hasClass('fa-angle-down')) {
                icon.removeClass('fa-angle-down');
                icon.addClass('fa-angle-up');
            }
            else if (icon.hasClass('fa-angle-up')) {
                icon.removeClass('fa-angle-up');
                icon.addClass('fa-angle-down');
            }
        });
    }
});
