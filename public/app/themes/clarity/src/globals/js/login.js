/**
 * Modify the login page
 */

const wpAnchor = document.querySelector('#login h1 > a');
const siteAnchor = document.querySelector('#backtoblog > a');

if (wpAnchor && siteAnchor) {
    wpAnchor.href = siteAnchor.href;
}
