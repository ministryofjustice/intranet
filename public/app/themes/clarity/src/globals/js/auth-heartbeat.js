export default (function ($) {
    // Send a request to the heartbeat endpoint, this will refresh the oauth token.
    setInterval(function(){
        $.get( "/auth/heartbeat" )
    }, 10000)
})(jQuery)
