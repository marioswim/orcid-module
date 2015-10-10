
<div class="text-center">
	<a href="" class='btn btn-default' rel="any"><?php echo t("Any"); ?></a>
<?php

foreach ($message as $type) 
{
	if(is_string($type))
	{
		$type=str_replace(" ","", $type);
		?>
		<a href="" class='btn btn-default' rel=<?php echo '"'.$type.'"'; ?>> 
		<?php
			echo t($type);
		?>
		</a>
		<?php
	}
}
?>
</div>