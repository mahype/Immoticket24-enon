<div class="pricing-table-column<?php echo ($most_popular=='Y' ? ' popular' : '') . ((( ! empty($feature_description)) or ( ! empty($items))) ? ' has-features' : ''); ?>">
    <div class="pricing-table-column-content">

        <div class="price-table">
            <div class="name"><?php echo $title; ?></div>
            <div class="price"><span class="unit"><?php echo $pricing_unit; ?></span><?php echo $price; ?></div>
            <div class="var"><?php echo (!empty($pricing_variable) ? '<span class="variable">'.$pricing_variable.'</span>' : ''); ?></div>
        </div>

        <div class="feature-table">
            <ul class="features"><?php echo $items; ?></ul>
        </div>

        <div class="submit-table">
            <a href="<?php echo $order_button_url; ?>" class="button"><?php echo $order_button_text; ?></a>
        </div>
        
    </div>
</div>
