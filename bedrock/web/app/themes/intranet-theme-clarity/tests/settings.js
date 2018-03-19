var system = require('system')

module.exports = {
  domain: (function () {
    var domain = system.env.TEST_DOMAIN
    if (!domain) {
      domain = 'http://mojintranet.test'
    }
    console.log('Using domain: ' + domain)
    return domain + '/'
  }()),
  // This is the defaults we will use for some tests
  username: 'intranetci@digital.justice.gov.uk',
  userPassword: 'DpN20X5tm2Qa'
}
