(function($) {
	$(document).ready(function(){	
		
		$("#block-orcid-tipo-publicaciones a").first().addClass("selected");
		$("#group-by-year > #year").on(
			"click",
			function()
			{
				$(this).parent().children("#orcid-module-works-container").slideToggle(600);
				

				$(this).children("#year img").toggleClass("rotate90");				
				
			});

		contentType($);

	});	

})(jQuery);


function contentType($)
{

	$("#block-orcid-tipo-publicaciones a").on("click",function(event)
	{		
		event.preventDefault();

		$("#block-orcid-tipo-publicaciones a").removeClass("selected");
		$(this).addClass("selected");
		var tipo=$(this).attr("rel");
		tipo=tipo.replace(/\s{2,}/,"");
		tipo=tipo.replace(/\s{2,}/,"");
		
		if(tipo!="any")
		{
			$("#orcid-module-works-container #orcid-module-work").not("."+tipo).hide(0);
			$("#orcid-module-works-container #orcid-module-work").filter("."+tipo).show(0);

			//console.log($("#orcid-module #group-by-year"));
			$("#orcid-module #group-by-year").each(function()
			{
				//var hijos=$(this).children("#orcid-module-works-container").children().length;
				var visualizar=$(this).children("#orcid-module-works-container").children().filter("."+tipo).length;
				if(visualizar==0)
				
					$(this).hide();
				
				else
					$(this).show();

			});
		}
		else
		{
			$("#orcid-module-works-container #orcid-module-work").show(0);
			$("#orcid-module #group-by-year").show(0);
		}
	});
}


