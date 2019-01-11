<?php
/**
 * Template for the search form
 *
 * @package SchemataThemeFramework
 * @version 0.1.0
 * @author  Felix Arntz <felix-arntz@leaves-and-love.net>
 */
?>

<form class="search-form" action="<?php echo home_url(); ?>" method="get" role="search">
  <div class="form-group">
    <div class="input-group input-group">
      <input class="form-control" name="s" type="search" value="" placeholder="Suchen...">
      <span class="input-group-btn">
        <button class="search-submit btn btn-default" type="submit"></button>
      </span>
    </div>
  </div>
</form>
