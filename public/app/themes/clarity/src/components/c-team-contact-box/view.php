<?php
  $team_title = get_field('team_title');

  $team_email         = get_field('team_email');
  $team_tele          = get_field('team_telephone');
  $team_location      = get_field('team_address__location');
  $team_about_link    = get_field('team_about_us_link');
  $team_about_us_text = get_field('team_about_us_text');

?>


<?php
if ($team_email || $team_tele || $team_location || $team_about_link) {
    ?>
  <!-- c-team-contact-box starts here -->
  <section class="c-team-contact-box">
    <h3 class="o-title o-title--section"><?= $team_title ?></h3>
    <ul>
      <li>
      <?php
        if ($team_email) {
            echo '<h4><strong>Email</strong></h4>';
        }
        ?>
    </li>
      <li>
      <?php
        if ($team_email) {
            echo $team_email;
        }
        ?>
    </li>

      <li>
      <?php
        if ($team_tele) {
            echo '<h4><strong>Telephone</strong></h4>';
        }
        ?>
    </li>
      <li>
      <?php
        if ($team_tele) {
            echo $team_tele;
        }
        ?>
    </li>

      <li>
      <?php
        if ($team_location) {
            echo '<h4><strong>Location</strong></h4>';
        }
        ?>
    </li>
      <li>
      <?php
        if ($team_location) {
            echo $team_location;
        }
        ?>
    </li>

      <li>
      <?php
        if ($team_about_link) {
            echo '<h4><strong>About Us Link</strong></h4>';
        }
        ?>
    </li>
      <li>
      <?php
        if ($team_about_link) {
            echo '<a href="' . $team_about_link . '">' . $team_about_us_text . '</a>';
        }
        ?>
    </li>
    </ul>
  </section>
  <!-- c-team-contact-box ends here -->
    <?php
}
