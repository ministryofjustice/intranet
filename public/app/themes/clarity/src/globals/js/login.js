/**
 * Modify the login page
 */

const wpLink = document.querySelector('a[href="https://en-gb.wordpress.org/"]');
const siteLink = document.querySelector('#backtoblog a');

if (wpLink && siteLink) {
    wpLink.href = siteLink.href;
}
