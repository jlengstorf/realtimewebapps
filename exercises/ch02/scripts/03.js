/*
 * Exercise 02-03, Realtime Web Apps
 *
 * @author Jason Lengstorf <jason@copterlabs.com>
 * @author Phil Leggetter <phil@leggetter.co.uk>
 */

(function($) {

    // Highlights the element contained in the <code> tag
    $('code').hover(
        function() {
            var elem = $(getElementName(this)),
                bg   = elem.css("background");
            elem.data('bg-orig', bg).css({ "background": "yellow" });
        },
        function() {
            var elem = $(getElementName(this)),
                bg   = elem.data('bg-orig');
            $(elem).css({ "background": bg });
        }
    );

    /**
     * Retrieves the element name contained within a code tag
     */
    function getElementName(element) {
        return String($(element).text().match(/\w+/));
    }

})(jQuery);
