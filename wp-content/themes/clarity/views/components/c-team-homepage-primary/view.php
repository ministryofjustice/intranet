<!-- c-team-homepage-primary starts -->
<section class="c-team-homepage-primary l-primary" role="main">

  <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

	<?php
	get_template_part( 'src/components/c-rich-text-block/view' );

	get_template_part( 'src/components/c-news-widget/view', 'team' );

	get_template_part( 'src/components/c-specialist-content/view' );

	echo '<h1 class="o-title o-title--section">News</h1>';
	get_template_part( 'src/components/c-news-list/view', 'team' );
	?>

</section>
<!-- c-team-homepage-primary ends -->
