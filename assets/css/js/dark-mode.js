jQuery( document ).ready(function( $ ) {
	
	var html = $( "html" );

	// Check stored preference or default mode on load
	var storedMode = $.cookie("reign_dark_mode");
	if (storedMode === "dark" || (!storedMode && defaultMode === "dark")) {
		if (!html.hasClass("dark-mode")) {
			html.addClass("dark-mode");
		}
		// Set dark logo
		if (dark_mode_settings.dark_mod_logo !== '') {
			$('.logo img.custom-logo').attr('src', dark_mode_settings.dark_mod_logo);
			$('.logo img.custom-logo').attr('srcset', dark_mode_settings.dark_mod_logo);
		}
	} else if (storedMode === "light") {
		if (html.hasClass("dark-mode")) {
			html.removeClass("dark-mode");
		}
		// Set light logo
		if (dark_mode_settings.light_mode_logo !== '') {
			$('.logo img.custom-logo').attr('src', dark_mode_settings.light_mode_logo);
			$('.logo img.custom-logo').attr('srcset', dark_mode_settings.light_mode_logo);
		}
	}


	$( ".rg-dark__scheme-toggle" ).on( "click", function () {
		if ( html.hasClass( "dark-mode" ) ) {
			html.removeClass( "dark-mode" );
			$.cookie("reign_dark_mode", "light", { path: '/', expires: 30 });
			if ( dark_mode_settings.light_mode_logo != ''  ) {
				$('.logo img.custom-logo').attr( 'src', dark_mode_settings.light_mode_logo );
				$('.logo img.custom-logo').attr( 'srcset', dark_mode_settings.light_mode_logo );
			}
		} else {
			html.addClass( "dark-mode" );
			$.cookie("reign_dark_mode", "dark", { path: '/', expires: 30 });
			if ( dark_mode_settings.dark_mod_logo != ''  ) {
				$('.logo img.custom-logo').attr( 'src', dark_mode_settings.dark_mod_logo );
				$('.logo img.custom-logo').attr( 'srcset', dark_mode_settings.dark_mod_logo );
			}

		}
		reign_replaceImages();
		reign_replaceBgImages();

	} );
	
	reign_replaceImages();
	reign_replaceBgImages();
	setTimeout(reign_replaceImages, 1000);
    setTimeout(reign_replaceBgImages, 3000);
	
	//handle the woocommerce product gallery images
	jQuery(".woocommerce-product-gallery").on( "wc-product-gallery-after-init",function () {
	  wc_single_product_params.zoom_options.callback = () => {
		reign_replaceImages();
	  };
	});
	function reign_replaceImages(){
		
		const elements = document.querySelectorAll("img");

		if (elements){
			elements.forEach((element) => {
				const url = element.getAttribute("src");
				const images = dark_mode_settings.images;
				const dataKey = "data-light-img";
			  
				if (!images) {
					return;
				}
			
				if ( document.querySelector("html").classList.contains("dark-mode") ) {
					if (images.light_images.includes(url)) {
						const index = images.light_images.indexOf(url);

						element.setAttribute(dataKey, url);

						const srcset = element.getAttribute("srcset");
						if (srcset) {
							element.setAttribute("data-light-srcset", srcset);
							element.setAttribute("srcset", images.dark_images[index]);
						}						
						element.setAttribute("src", images.dark_images[index]);
					}
				} else {
					const light_img = element.getAttribute(dataKey);
					const srcset = element.getAttribute("data-light-srcset");
					if (light_img) {
					element.setAttribute("src", light_img);
					}

					if (srcset) {
					element.setAttribute("srcset", srcset);
					}
				}
			});
		}
	}
	
	function reign_replaceBgImages(){
		const elements = document.querySelectorAll(
			"header, footer, div, section"
		);

		if (elements){
			elements.forEach((element) => {
				const bi = window.getComputedStyle(element, false).backgroundImage;

				if (bi !== "none") {
					const url = bi.slice(4, -1).replace(/"/g, "");
					const images = dark_mode_settings.images;
					const dataKey = "data-light-bg";

					if (!images) {
					  return;
					}

					if (
					  document
						.querySelector("html")
						.classList.contains("dark-mode")
					) {
					  if (images.light_images.includes(url)) {
						const index = images.light_images.indexOf(url);

						element.setAttribute(dataKey, url);
						element.style.backgroundImage = `url('${images.dark_images[index]}')`;
					  }
					} else {
					  const light_bg = element.getAttribute(dataKey);
					  if (light_bg) {
						element.style.backgroundImage = `url('${light_bg}')`;
					  }
					}
				}
			
			});
		}
	}
});