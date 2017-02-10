<?php if (!defined('ABSPATH')) die(); ?>

<!DOCTYPE html>
<html>
  <body>
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <div style="background-color: black">
            <!-- Yes, we hotlink to People Finder as our own prod server isn't available from the outside -->
            <p style="color: #fff;">
              <img src="https://peoplefinder.service.gov.uk/assets/moj_logo_horizontal_36x246-90c698afdefe7275f7580065062aebc6.png" alt="Ministry of Justice" height="36px"
                style="padding: 20px 40px" />
            </p>
          </div>
        </td>
      </tr>
      <tr>
        <td>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">Hello,</p>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">To add your comments to the intranet, just click the button below:</p>
          <p>
             <a style="
              position: relative;
              display: inline-block;
              padding: 8px 15px 5px;
              background-color: #00823b;
              box-shadow: 0 2px 0 #003518;
              color: #ffffff;
              cursor: pointer;
              font-size: 19px;
              font-family: Arial, sans-serif;
              line-height: 25px;
              text-decoration: none;
              vertical-align: top;
              "
              href="<?=$activation_url?>">Begin commenting</a>
          </p>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">This will take you back to the intranet page you were on where you can begin commenting.</p>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">For security reasons, this link will be active for 3 hours only. </p>
          <h2>Any problems?</h2>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">If this link has expired, you’ll need to fill in your details again to get another link. If you don’t want to comment on the intranet, ignore this email.</p>
          <p style="padding: 5px 0; font-size: 19px; font-family: Arial, sans-serif;">The Intranet Team</p>
          <p style="padding: 25px 0 5px; font-size: 16px; font-family: Arial, sans-serif; color: #6f777b;">This email is generated automatically. Do not reply.</p>
        </td>
      </tr>
      <tr>
        <td>
          <div style="background-color: #dee0e2">
            <p style="padding: 20px; font-size: 16px; font-family: Arial, sans-serif;">
              If you're unsure an email is from the MoJ, forward it to <a href="mailto:phishing@digital.justice.gov.uk">phishing@digital.justice.gov.uk</a>.
            </p>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
