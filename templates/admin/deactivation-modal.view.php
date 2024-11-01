<div id="shop_ct_deactivation_popup" class="shop-ct-deactivation-popup shop-ct-hide">
    <div class="shop-ct-deactivation-header">
        Before You Go<br />
        Please help us improve the plugin!
    </div>
    <div class="shop-ct-deactivation-body">
        <p style="margin-top:0; font-size: 14px;"><strong>We are sorry plugin didn't fit your requirements!<br />
                But if you have a moment, please let us know why you are deactivating:</strong></p>

        <form method="post" action="#" class="shop-ct-deactivation-form">
            <?php wp_nonce_field('shop_ct_deactivation_feedback','shop_ct_deactivation_feedback_nonce'); ?>
            <div>
                <label>
                    <input type="radio" name="selected-reason" value="found_better_plugin" />
                    <span>I found a better plugin</span>
                </label>
                <label class="shop-ct-hide">
                    <span>Kindly tell us more so we can improve.</span>
                    <textarea name="found_better_plugin_details"></textarea>
                </label>
            </div>

            <div>
                <label>
                    <input type="radio" name="selected-reason" value="bug_error" />
                    <span>A bug or error occured</span>
                </label>
                <label class="shop-ct-hide">
                    <span>Kindly tell us more so we can improve.</span>
                    <textarea name="bug_error_details"></textarea>
                </label>
            </div>

            <div>
                <label>
                    <input type="radio" name="selected-reason" value="didnt_work" />
                    <span>The plugin didn't work</span>
                </label>
                <label class="shop-ct-hide">
                    <span>Kindly tell us more so we can improve.</span>
                    <textarea name="didnt_work_details"></textarea>
                </label>
            </div>

            <div>
                <label>
                    <input type="radio" name="selected-reason" value="temporary" />
                    <span>It's a temporary deactivation. I'm just debugging an issue.</span>
                </label>
                <label class="shop-ct-hide">
                    <span>Kindly tell us more so we can improve.</span>
                    <textarea name="temporary_details"></textarea>
                </label>
            </div>

            <div>
                <label>
                    <input type="radio" name="selected-reason" value="other" />
                    <span>Other</span>
                </label>
                <label class="shop-ct-hide">
                    <span>Kindly tell us more so we can improve.</span>
                    <textarea name="other_details"></textarea>
                </label>
            </div>
        </form>
    </div>
    <div class="shop-ct-deactivation-footer">
        <div style="display: inline-block">
            <label class="shop-ct-deactive-feedback-anon shop-ct-hide">
                <input type="checkbox" name="anonymous" />
                <span>Anonymous feedback</span>
            </label>
        </div>

        <div>
            <button class="shop-ct-deactivation-skip button">Skip & Deactivate</button>
            <button class="shop-ct-deactivation-submit button shop-ct-hide">Submit & Deactivate</button>
            <button class="shop-ct-deactivation-cancel button-primary">Cancel</button>
        </div>
    </div>
</div>