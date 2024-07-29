export default (function ($) {
    // Sent a request to the heartbeat endpoint, this will refresh the oauth token.
    setInterval(function(){
        $.get( "/auth/heartbeat" )
    }, 30000)
})(jQuery)
