<script data-name="comment-form" type="text/x-partial-template">
  <form class="comment-form">
    <div class="form-row">
      <textarea name="comment" placeholder="Enter your comment here..."></textarea>
    </div>
    <div class="form-row misc">
      <input class="cta cta-positive submit" type="submit" value="Post comment" />
      <input class="cta cta-plain cancel" type="reset" value="Cancel" />
      <p class="limit-reached-msg">Character limit reached</p>

      <p class="secondary-action">
        <a href="<?=$commenting_policy_url?>">MoJ commenting policy</a>
      </p>
    </div>
  </form>
</script>
