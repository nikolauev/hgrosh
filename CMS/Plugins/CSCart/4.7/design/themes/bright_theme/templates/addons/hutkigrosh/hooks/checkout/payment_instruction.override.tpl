{if $order_info.payment_method.instructions && $order_info.order_id > 0}
    <h4 class="ty-subheader">{__("payment_instructions")}</h4>
    <div class="ty-wysiwyg-content">
        {$order_info.payment_method.instructions|replace:"@order_number":$order_info.order_id nofilter}
    </div>
{/if}