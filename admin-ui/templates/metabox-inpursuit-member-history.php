<?php /*
<div id="my-content-id" style="display:none;">
 <p>
	 <textarea></textarea>
 </p>
</div>

<label>Additional Notes</label>
<p><textarea rows="5" style="max-width: 500px;width: 100%;padding: 10px;"></textarea></p>
<a href="#TB_inline?width=400&height=350&inlineId=my-content-id" class="thickbox button">Submit</a>
*/ ?>

<?php if( isset( $_GET['post'] ) ):?>
<timeline member_id="<?php echo $_GET['post'];?>" per_page="10"></timeline>
<?php endif; ?>
