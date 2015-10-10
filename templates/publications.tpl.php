

<div id="orcid-module">

<?php 

$module_path = drupal_get_path('module', 'Orcid');

$years=array_keys($message);
foreach ($years as $year) 
{	
	
	?>
	<?php if($message[$year]->rowCount()): ?>
		<div id="group-by-year">
		<div id="year">
			
			<?php echo '<img src="/'.$module_path.'/img/arrow.png"/>'.$year;?>
			
		</div>
		<div id="orcid-module-works-container">
			<?php
			foreach ($message[$year] as $row) 
			{

				$title=$row->title;
				$publication=$row->{"journal-title"};
				$type=$row->type;
				$doi=$row->DOI;
				$eid=$row->EID;
				$issn=$row->ISSN;
				$authors=$row->authors;
				$pages=$row->pages;
				?>
				<div id="orcid-module-work" class=<?php echo '"'.$type.'"'; ?>>
				<?php /*if(!empty($authors)): ?>
					<span id="author" class="info">
						<?php
							$and=strripos($authors,"|");

							$authors=substr_replace($authors,t("and"), $and,1);
							$authors=str_replace(" |", ",",$authors);
							echo $authors.", ";

						?>
				<?php endif; */?>
					</span>
					<span id="title">
						<?php echo '"'.$title.'"'; ?>
					</span>
					
					<span id="journal" class="info">
						<?php 
							if(!empty($publication))
								echo ", ".$publication; 

							if(!empty($pages))
								echo ", ".t("pages")." ".$pages;
						?>
					</span>
					<span class="info">
						<?php echo $year; ?>
						<?php 
						if(!empty($type))
						{
							echo " | ".t($type);
						}
						
						?>
					</span>
					<span class="info">
						<?php
							$identifier=array();
							if(!empty($issn))
							{
								$identifier["ISSN"]=$issn;
							}
							if(!empty($doi))
							{
								$identifier["DOI"]=$doi;
							}
							if(!empty($eid))
							{
								$identifier["EID"]=$eid;
							}
							

							$idType=array_keys($identifier);

							for($i=0;$i<count($idType);$i++)
							{
								$type=$idType[$i];
								$number=$identifier[$type];
								if($type=="DOI")
								{
									$number='<a href="http://dx.doi.org/'.$number.'">'.$number.'</a>';
								}
								if($i==0)
								{
									echo $type.": ".$number;
								}	
								else
								{
									echo ", ".$type.": ".$number;
								}
							}
						?>
					</span>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php endif; ?>
	<?php
}
?>

</div>